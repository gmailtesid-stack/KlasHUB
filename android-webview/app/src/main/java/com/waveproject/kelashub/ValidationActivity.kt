package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ValidationActivity : AppCompatActivity() {

    private lateinit var rvValidation: RecyclerView
    private lateinit var adapter: ValidationAdapter
    private lateinit var progress: ProgressBar
    private lateinit var tvEmpty: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_validation)

        rvValidation = findViewById(R.id.rvValidation)
        progress = findViewById(R.id.progressValidation)
        tvEmpty = findViewById(R.id.tvEmpty)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        rvValidation.layoutManager = LinearLayoutManager(this)
        adapter = ValidationAdapter(listOf()) { item ->
            approveItem(item)
        }
        rvValidation.adapter = adapter

        fetchData()
    }

    private fun fetchData() {
        progress.visibility = View.VISIBLE
        tvEmpty.visibility = View.GONE
        ApiClient.apiInterface.getPendingValidations().enqueue(object : Callback<PendingValidationResponse> {
            override fun onResponse(call: Call<PendingValidationResponse>, response: Response<PendingValidationResponse>) {
                progress.visibility = View.GONE
                if (response.isSuccessful && response.body() != null) {
                    val list = response.body()!!.pending
                    adapter.updateData(list)
                    if (list.isEmpty()) {
                        tvEmpty.visibility = View.VISIBLE
                    }
                } else {
                    Toast.makeText(this@ValidationActivity, "Gagal memuat validasi", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<PendingValidationResponse>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(this@ValidationActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun approveItem(item: PendingValidationItem) {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.validateData(item.id, item.type).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ValidationActivity, "Berhasil disetujui", Toast.LENGTH_SHORT).show()
                    fetchData() // refresh list
                } else {
                    progress.visibility = View.GONE
                    Toast.makeText(this@ValidationActivity, "Gagal menyetujui", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(this@ValidationActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
