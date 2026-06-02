package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class IzinActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_izin)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etSubject = findViewById<EditText>(R.id.etSubject)
        val etDate = findViewById<EditText>(R.id.etDate)
        val etNotes = findViewById<EditText>(R.id.etNotes)
        val rbIzin = findViewById<RadioButton>(R.id.rbIzin)
        val btnSubmit = findViewById<Button>(R.id.btnSubmitIzin)
        val progress = findViewById<ProgressBar>(R.id.progressIzin)

        btnSubmit.setOnClickListener {
            val subject = etSubject.text.toString()
            val dateStr = etDate.text.toString()
            val notes = etNotes.text.toString()

            if (subject.isEmpty() || dateStr.isEmpty()) {
                Toast.makeText(this, "Mata kuliah dan tanggal wajib diisi!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val statusVal = if (rbIzin.isChecked) "Izin" else "Sakit"

            progress.visibility = View.VISIBLE
            btnSubmit.isEnabled = false

            // Get Student ID directly from Profile API
            ApiClient.apiInterface.getProfile().enqueue(object : Callback<ProfileResponse> {
                override fun onResponse(call: Call<ProfileResponse>, response: Response<ProfileResponse>) {
                    if (response.isSuccessful && response.body() != null) {
                        val studentId = response.body()!!.student.id
                        submitIzin(studentId, statusVal, subject, dateStr, notes, progress, btnSubmit)
                    } else {
                        progress.visibility = View.GONE
                        btnSubmit.isEnabled = true
                        Toast.makeText(this@IzinActivity, "Gagal melacak ID Mahasiswa", Toast.LENGTH_SHORT).show()
                    }
                }
                override fun onFailure(call: Call<ProfileResponse>, t: Throwable) {
                    progress.visibility = View.GONE
                    btnSubmit.isEnabled = true
                }
            })
        }
    }

    private fun submitIzin(studentId: Int, status: String, subject: String, date: String, notes: String, progress: ProgressBar, btn: Button) {
        ApiClient.apiInterface.requestIzin(studentId, status, subject, date, notes).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                progress.visibility = View.GONE
                if (response.isSuccessful) {
                    Toast.makeText(this@IzinActivity, "Pengajuan berhasil. Menunggu Validasi", Toast.LENGTH_LONG).show()
                    finish()
                } else {
                    btn.isEnabled = true
                    Toast.makeText(this@IzinActivity, "Gagal mengajukan izin", Toast.LENGTH_SHORT).show()
                }
            }
            override fun onFailure(call: Call<Void>, t: Throwable) {
                progress.visibility = View.GONE
                btn.isEnabled = true
            }
        })
    }
}
