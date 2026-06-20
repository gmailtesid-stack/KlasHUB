package com.waveproject.kelashub

import android.app.Activity
import android.app.Application
import android.content.Context
import android.content.Intent
import android.os.Bundle
import com.onesignal.OneSignal
import com.onesignal.debug.LogLevel

class MainApplication : Application() {
    override fun onCreate() {
        super.onCreate()

        // OneSignal Initialization
        OneSignal.Debug.logLevel = LogLevel.VERBOSE
        OneSignal.initWithContext(this, BuildConfig.ONESIGNAL_APP_ID)

        // Offline First Initialization
        ApiClient.init(this)

        registerActivityLifecycleCallbacks(object : ActivityLifecycleCallbacks {
            override fun onActivityPaused(activity: Activity) {
                getSharedPreferences("AuthPrefs", Context.MODE_PRIVATE)
                    .edit()
                    .putLong("last_access_time", System.currentTimeMillis())
                    .apply()
            }

            override fun onActivityResumed(activity: Activity) {
                val prefs = getSharedPreferences("AuthPrefs", Context.MODE_PRIVATE)
                val lastAccess = prefs.getLong("last_access_time", System.currentTimeMillis())
                if (System.currentTimeMillis() - lastAccess > 30 * 60 * 1000 && prefs.getBoolean("is_logged_in", false)) {
                    prefs.edit().putBoolean("is_logged_in", false).apply()
                    getSharedPreferences("CookiePrefs", Context.MODE_PRIVATE).edit().clear().apply()

                    if (activity !is LoginActivity) {
                        val intent = Intent(this@MainApplication, LoginActivity::class.java)
                        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                        startActivity(intent)
                    }
                }
            }

            override fun onActivityCreated(activity: Activity, savedInstanceState: Bundle?) {}
            override fun onActivityStarted(activity: Activity) {}
            override fun onActivityStopped(activity: Activity) {}
            override fun onActivitySaveInstanceState(activity: Activity, outState: Bundle) {}
            override fun onActivityDestroyed(activity: Activity) {}
        })
    }
}
