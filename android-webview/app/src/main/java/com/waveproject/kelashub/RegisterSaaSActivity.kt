package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class RegisterSaaSActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register_saas)

        val etKetuaName = findViewById<EditText>(R.id.etKetuaName)
        val etKetuaNim = findViewById<EditText>(R.id.etKetuaNim)
        val etClassCode = findViewById<EditText>(R.id.etClassCode)
        val etDepartment = findViewById<EditText>(R.id.etDepartment)
        
        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }
        
        findViewById<Button>(R.id.btnRegisterSubmit).setOnClickListener {
            val name = etKetuaName.text.toString()
            val nim = etKetuaNim.text.toString()
            val classCode = etClassCode.text.toString()
            val department = etDepartment.text.toString()
            
            if (name.isEmpty() || nim.isEmpty() || classCode.isEmpty() || department.isEmpty()) {
                Toast.makeText(this, "Mohon lengkapi semua data wajib!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            
            val requestBody = mapOf(
                "ketua_name" to name,
                "ketua_nim" to nim,
                "class_code" to classCode,
                "department" to department,
                "contact" to ""
            )

            ApiClient.apiInterface.registerClass(requestBody).enqueue(object : Callback<ApiResponse> {
                override fun onResponse(call: Call<ApiResponse>, response: Response<ApiResponse>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@RegisterSaaSActivity, "Institusi Kelas Baru Sukses Diaktifkan!", Toast.LENGTH_LONG).show()
                        finish()
                    } else {
                        Toast.makeText(this@RegisterSaaSActivity, "Gagal didaftarkan: Kelas/NIM mungkin sudah terdaftar", Toast.LENGTH_LONG).show()
                    }
                }

                override fun onFailure(call: Call<ApiResponse>, t: Throwable) {
                    Toast.makeText(this@RegisterSaaSActivity, "Koneksi Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }
}
