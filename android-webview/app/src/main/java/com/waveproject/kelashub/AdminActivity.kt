package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.View
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
        val btnNavNextSemester = findViewById<Button>(R.id.btnNavNextSemester)
        val btnNavSaaS = findViewById<Button>(R.id.btnNavSaaS) // New SaaS Button
        val btnNavUploadQris = findViewById<Button>(R.id.btnNavUploadQris)
        
        btnNavValidation.visibility = View.GONE
        btnNavInputKas.visibility = View.GONE
        btnNavNextSemester.visibility = View.GONE
        btnNavSaaS.visibility = View.GONE
        btnNavUploadQris.visibility = View.GONE

        val role = intent.getStringExtra("USER_ROLE") ?: ""
        if (role == "ketua_kelas" || role == "super_admin") {
            btnNavValidation.visibility = View.VISIBLE
            btnNavInputKas.visibility = View.VISIBLE
            btnNavInputSubject.visibility = View.VISIBLE
            btnNavInputStudent.visibility = View.VISIBLE
            btnNavManageStudent.visibility = View.VISIBLE
            btnNavNextSemester.visibility = View.VISIBLE
        }
        if (role == "super_admin") {
            btnNavSaaS.visibility = View.VISIBLE
        } else if (role == "bendahara" || role == "ketua_kelas") {
            btnNavUploadQris.visibility = View.VISIBLE
        }
        if (role == "bendahara") {
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

        btnNavNextSemester.setOnClickListener {
            android.app.AlertDialog.Builder(this)
                .setTitle("Perhatian!")
                .setMessage("Yakin ingin naik ke semester selanjutnya? Seluruh data Jadwal Harian dan Absensi semester ini akan terarsip, dan halaman utama akan kosong (Semester Baru).")
                .setPositiveButton("Naik Semester") { _, _ ->
                    ApiClient.apiInterface.nextSemester().enqueue(object: retrofit2.Callback<Void> {
                        override fun onResponse(call: retrofit2.Call<Void>, response: retrofit2.Response<Void>) {
                            if (response.isSuccessful) {
                                android.widget.Toast.makeText(this@AdminActivity, "Berhasil masuk ke Semester Baru!", android.widget.Toast.LENGTH_SHORT).show()
                            }
                        }
                        override fun onFailure(call: retrofit2.Call<Void>, t: Throwable) {}
                    })
                }
                .setNegativeButton("Batal", null)
                .show()
        }

        btnNavSaaS.setOnClickListener {
            startActivity(Intent(this, RegisterSaaSActivity::class.java))
        }

        btnNavUploadQris.setOnClickListener {
            startActivity(Intent(this, UploadQrisActivity::class.java))
        }
    }
}
