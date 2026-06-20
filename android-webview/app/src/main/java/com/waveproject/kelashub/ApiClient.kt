package com.waveproject.kelashub

import okhttp3.Cookie
import okhttp3.CookieJar
import okhttp3.HttpUrl
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import android.content.Context

object ApiClient {
    private const val BASE_URL = BuildConfig.BASE_URL
    private var retrofit: Retrofit? = null
    private lateinit var cookieJar: CookieJar

    fun init(context: Context) {
        cookieJar = object : CookieJar {
            private val prefs = SecurePrefs.get(context, "CookiePrefs")

            override fun saveFromResponse(url: HttpUrl, cookies: List<Cookie>) {
                if (cookies.isEmpty()) return
                
                val existingStrings = prefs.getStringSet(url.host, emptySet())
                val existingCookies = existingStrings?.mapNotNull { Cookie.parse(url, it) }
                    ?.filter { it.expiresAt > System.currentTimeMillis() }
                    ?.associateBy { it.name }?.toMutableMap() ?: mutableMapOf()

                for (c in cookies) {
                    existingCookies[c.name] = c
                }

                val editor = prefs.edit()
                editor.putStringSet(url.host, existingCookies.values.map { it.toString() }.toSet())
                editor.apply()
            }

            override fun loadForRequest(url: HttpUrl): List<Cookie> {
                val cookieStrings = prefs.getStringSet(url.host, emptySet())
                return cookieStrings?.mapNotNull { Cookie.parse(url, it) } ?: emptyList()
            }
        }
    }

    fun getClient(): Retrofit {
        if (retrofit == null) {
            val logging = HttpLoggingInterceptor()
            logging.setLevel(HttpLoggingInterceptor.Level.BODY)

            val okHttpClient = OkHttpClient.Builder()
                .cookieJar(cookieJar)
                .addInterceptor { chain ->
                    val requestUrl = chain.request().url
                    val cookies = cookieJar.loadForRequest(requestUrl)
                    val xsrfCookie = cookies.find { it.name == "XSRF-TOKEN" }?.value

                    var requestBuilder = chain.request().newBuilder()
                        .addHeader("Accept", "application/json")
                        .addHeader("X-Requested-With", "XMLHttpRequest")

                    if (xsrfCookie != null) {
                        try {
                            requestBuilder = requestBuilder.addHeader("X-XSRF-TOKEN", java.net.URLDecoder.decode(xsrfCookie, "UTF-8"))
                        } catch (e: Exception) {
                            e.printStackTrace()
                        }
                    }
                    chain.proceed(requestBuilder.build())
                }
                .addInterceptor(logging)
                .connectTimeout(30, TimeUnit.SECONDS)
                .readTimeout(30, TimeUnit.SECONDS)
                .writeTimeout(30, TimeUnit.SECONDS)
                .followRedirects(false)
                .followSslRedirects(false)
                .build()

            retrofit = Retrofit.Builder()
                .baseUrl(BASE_URL)
                .client(okHttpClient)
                .addConverterFactory(GsonConverterFactory.create())
                .build()
        }
        return retrofit!!
    }

    val apiInterface: ApiInterface by lazy {
        getClient().create(ApiInterface::class.java)
    }
}
