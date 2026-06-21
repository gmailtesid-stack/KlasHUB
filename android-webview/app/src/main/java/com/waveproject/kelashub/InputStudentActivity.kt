package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class InputStudentActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_input_student)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etNim = findViewById<EditText>(R.id.etNim)
        val etName = findViewById<EditText>(R.id.etName)
        
        val rbMahasiswa = findViewById<RadioButton>(R.id.rbMahasiswa)
        val rbBendahara = findViewById<RadioButton>(R.id.rbBendahara)
        val rbSekretaris = findViewById<RadioButton>(R.id.rbSekretaris)
        
        val btnSubmit = findViewById<Button>(R.id.btnSubmitStudent)
        val progress = findViewById<ProgressBar>(R.id.progressStudent)

        btnSubmit.setOnClickListener {
            val nim = etNim.text.toString()
            val name = etName.text.toString()

            if (nim.isEmpty() || name.isEmpty()) {
                Toast.makeText(this, "NIM dan Nama wajib diisi!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            var role = "mahasiswa"
            if (rbBendahara.isChecked) role = "bendahara"
            if (rbSekretaris.isChecked) role = "sekretaris"

            progress.visibility = View.VISIBLE
            btnSubmit.isEnabled = false

            ApiClient.apiInterface.addStudent(nim, name, role).enqueue(object : Callback<ApiResponse> {
                override fun onResponse(call: Call<ApiResponse>, response: Response<ApiResponse>) {
                    progress.visibility = View.GONE
                    if (response.isSuccessful && response.body()?.success == true) {
                        val genPass = response.body()?.defaultPassword ?: "12345678"
                        Toast.makeText(this@InputStudentActivity, "Ditambahkan! Sandi awal: $genPass", Toast.LENGTH_LONG).show()
                        finish()
                    } else {
                        btnSubmit.isEnabled = true
                        var errMsg = "Gagal didaftarkan"
                        try {
                            val errorString = response.errorBody()?.string()
                            if (errorString != null) {
                                val jsonObject = org.json.JSONObject(errorString)
                                if (jsonObject.has("message")) {
                                    errMsg = jsonObject.getString("message")
                                }
                            }
                        } catch (e: Exception) {
                            errMsg += " (Code: ${response.code()})"
                        }
                        Toast.makeText(this@InputStudentActivity, errMsg, Toast.LENGTH_LONG).show()
                    }
                }

                override fun onFailure(call: Call<ApiResponse>, t: Throwable) {
                    progress.visibility = View.GONE
                    btnSubmit.isEnabled = true
                    Toast.makeText(this@InputStudentActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }
}
