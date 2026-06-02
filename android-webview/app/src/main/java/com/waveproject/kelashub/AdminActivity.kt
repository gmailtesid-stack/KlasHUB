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

        val btnNavValidation = findViewById<Button>(R.id.btnNavValidation)
        val btnNavInputKas = findViewById<Button>(R.id.btnNavInputKas)
        val btnNavInputSubject = findViewById<Button>(R.id.btnNavInputSubject)
        val btnNavInputStudent = findViewById<Button>(R.id.btnNavInputStudent)
        val btnNavManageStudent = findViewById<Button>(R.id.btnNavManageStudent)
        
        btnNavValidation.visibility = View.GONE
        btnNavInputKas.visibility = View.GONE

        val role = intent.getStringExtra("USER_ROLE") ?: ""
        if (role == "ketua_kelas" || role == "super_admin") {
            btnNavValidation.visibility = View.VISIBLE
            btnNavInputKas.visibility = View.VISIBLE
            btnNavInputSubject.visibility = View.VISIBLE
            btnNavInputStudent.visibility = View.VISIBLE
            btnNavManageStudent.visibility = View.VISIBLE
        } else if (role == "bendahara") {
            btnNavInputKas.visibility = View.VISIBLE
        }

        btnNavValidation.setOnClickListener {
            startActivity(Intent(this, ValidationActivity::class.java))
        }

        btnNavInputKas.setOnClickListener {
            startActivity(Intent(this, InputKasActivity::class.java))
        }
        
        btnNavInputSubject.setOnClickListener {
            startActivity(Intent(this, InputSubjectActivity::class.java))
        }

        btnNavInputStudent.setOnClickListener {
            startActivity(Intent(this, InputStudentActivity::class.java))
        }

        btnNavManageStudent.setOnClickListener {
            startActivity(Intent(this, ManageStudentsActivity::class.java))
        }
    }
}
