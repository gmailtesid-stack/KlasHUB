package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class InputKasActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_input_kas)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etAmount = findViewById<EditText>(R.id.etAmount)
        val etDesc = findViewById<EditText>(R.id.etDesc)
        val etDate = findViewById<EditText>(R.id.etDate)
        val rbIncome = findViewById<RadioButton>(R.id.rbIncome)
        val btnSubmit = findViewById<Button>(R.id.btnSubmit)
        val progress = findViewById<ProgressBar>(R.id.progressInput)

        btnSubmit.setOnClickListener {
            val amountStr = etAmount.text.toString()
            val desc = etDesc.text.toString()
            val dateStr = etDate.text.toString()

            if (amountStr.isEmpty() || desc.isEmpty() || dateStr.isEmpty()) {
                Toast.makeText(this, "Isi form lengkap!", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val amount = amountStr.toDoubleOrNull() ?: 0.0
            val typeVal = if (rbIncome.isChecked) "income" else "expense"

            progress.visibility = View.VISIBLE
            btnSubmit.isEnabled = false

            ApiClient.apiInterface.addCash(amount, typeVal, desc, dateStr).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    progress.visibility = View.GONE
                    if (response.isSuccessful) {
                        Toast.makeText(this@InputKasActivity, "Transaksi berhasil disimpan", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        btnSubmit.isEnabled = true
                        Toast.makeText(this@InputKasActivity, "Gagal melengkapi pengajuan", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    progress.visibility = View.GONE
                    btnSubmit.isEnabled = true
                    Toast.makeText(this@InputKasActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }
}
