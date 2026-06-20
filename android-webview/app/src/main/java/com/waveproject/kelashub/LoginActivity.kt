package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class LoginActivity : AppCompatActivity() {

    private lateinit var etNim: android.widget.EditText
    private lateinit var etPassword: android.widget.EditText
    private lateinit var btnLogin: android.widget.Button
    private lateinit var progress: android.widget.ProgressBar

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        val prefs = SecurePrefs.get(this, "AuthPrefs")
        val lastAccess = prefs.getLong("last_access_time", System.currentTimeMillis())
        
        if (System.currentTimeMillis() - lastAccess > 30 * 60 * 1000) {
            prefs.edit().putBoolean("is_logged_in", false).apply()
            SecurePrefs.get(this, "CookiePrefs").edit().clear().apply()
            Toast.makeText(this, "Sesi Anda telah habis (30 menit).", Toast.LENGTH_SHORT).show()
        }

        if (prefs.getBoolean("is_logged_in", false)) {
            startActivity(Intent(this, MainActivity::class.java))
            finish()
            return
        }
        
        setContentView(R.layout.activity_login)

        etNim = findViewById(R.id.etNim)
        etPassword = findViewById(R.id.etPassword)
        btnLogin = findViewById(R.id.btnLogin)
        progress = findViewById(R.id.loginProgress)

        btnLogin.setOnClickListener {
            performLogin()
        }
    }

    private fun performLogin() {
        val nim = etNim.text.toString().trim()
        val password = etPassword.text.toString().trim()

        if (nim.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "NIM and Password required", Toast.LENGTH_SHORT).show()
            return
        }

        progress.visibility = View.VISIBLE
        btnLogin.isEnabled = false

        ApiClient.apiInterface.login(nim, password).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                progress.visibility = View.GONE
                btnLogin.isEnabled = true
                if (response.isSuccessful) {
                    val prefs = SecurePrefs.get(this@LoginActivity, "AuthPrefs")
                    prefs.edit().putBoolean("is_logged_in", true).apply()
                    
                    val intent = Intent(this@LoginActivity, MainActivity::class.java)
                    startActivity(intent)
                    finish()
                } else {
                    val errorBody = response.errorBody()?.string()
                    val errorMessage = if (response.code() == 401) {
                        "Login Failed: Invalid credentials"
                    } else {
                        "Login Failed: ${response.code()} ${errorBody ?: ""}"
                    }
                    Toast.makeText(this@LoginActivity, errorMessage, Toast.LENGTH_LONG).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                progress.visibility = View.GONE
                btnLogin.isEnabled = true
                Toast.makeText(this@LoginActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
