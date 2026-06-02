package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class InputSubjectActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_input_subject)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etSubjectName = findViewById<EditText>(R.id.etSubjectName)
        val etCode = findViewById<EditText>(R.id.etCode)
        val etSks = findViewById<EditText>(R.id.etSks)
        val etLecturer = findViewById<EditText>(R.id.etLecturer)
        val btnSubmit = findViewById<Button>(R.id.btnSubmitSubject)
        val progress = findViewById<ProgressBar>(R.id.progressSubject)

        btnSubmit.setOnClickListener {
            val name = etSubjectName.text.toString()
            val code = etCode.text.toString()
            val sks = etSks.text.toString().toIntOrNull() ?: 0
            val lecturer = etLecturer.text.toString()

            if (name.isEmpty() || code.isEmpty() || sks <= 0) {
                Toast.makeText(this, "Isi form lengkap!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            progress.visibility = View.VISIBLE
            btnSubmit.isEnabled = false

            ApiClient.apiInterface.addMasterSubject(name, sks, code, lecturer).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    progress.visibility = View.GONE
                    if (response.isSuccessful) {
                        Toast.makeText(this@InputSubjectActivity, "Mata kuliah berhasil ditambahkan", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        btnSubmit.isEnabled = true
                        Toast.makeText(this@InputSubjectActivity, "Gagal membuat mata kuliah (Kode Duplikat?)", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    progress.visibility = View.GONE
                    btnSubmit.isEnabled = true
                    Toast.makeText(this@InputSubjectActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }
}
