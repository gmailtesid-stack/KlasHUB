package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import androidx.appcompat.app.AppCompatActivity

class AdminActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_admin)

        findViewById<Button>(R.id.btnBack).setOnClickListener {
            finish()
        }

        findViewById<Button>(R.id.btnNavValidation).setOnClickListener {
            startActivity(Intent(this, ValidationActivity::class.java))
        }

        findViewById<Button>(R.id.btnNavInputKas).setOnClickListener {
            startActivity(Intent(this, InputKasActivity::class.java))
        }
    }
}
