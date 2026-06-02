package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.onesignal.OneSignal
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.NumberFormat
import java.util.*

class MainActivity : AppCompatActivity() {

    private lateinit var tvSaldo: TextView
    private lateinit var tvIncome: TextView
    private lateinit var tvExpense: TextView
    private lateinit var rvAssignments: RecyclerView
    private lateinit var rvModules: RecyclerView
    private lateinit var progress: ProgressBar

    private lateinit var assignmentAdapter: AssignmentAdapter
    private lateinit var moduleAdapter: ModuleAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        initUI()
        fetchData()
        syncOneSignalToken()
    }

    private fun initUI() {
        tvSaldo = findViewById(R.id.tvSaldo)
        tvIncome = findViewById(R.id.tvIncome)
        tvExpense = findViewById(R.id.tvExpense)
        rvAssignments = findViewById(R.id.rvAssignments)
        rvModules = findViewById(R.id.rvModules)
        progress = findViewById(R.id.mainProgress)

        rvAssignments.layoutManager = LinearLayoutManager(this)
        assignmentAdapter = AssignmentAdapter(listOf())
        rvAssignments.adapter = assignmentAdapter

        rvModules.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)
        moduleAdapter = ModuleAdapter(listOf())
        rvModules.adapter = moduleAdapter
    }

    private fun fetchData() {
        progress.visibility = View.VISIBLE

        ApiClient.apiInterface.getDashboardData().enqueue(object : Callback<DashboardData> {
            override fun onResponse(call: Call<DashboardData>, response: Response<DashboardData>) {
                progress.visibility = View.GONE
                if (response.isSuccessful) {
                    val data = response.body()
                    if (data != null) {
                        updateUI(data)
                    }
                } else {
                    if (response.code() == 401) {
                        // Session expired, go to login
                        startActivity(Intent(this@MainActivity, LoginActivity::class.java))
                        finish()
                    } else {
                        Toast.makeText(this@MainActivity, "Failed to load data", Toast.LENGTH_SHORT).show()
                    }
                }
            }

            override fun onFailure(call: Call<DashboardData>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(this@MainActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateUI(data: DashboardData) {
        // Calculate Saldo
        var income = 0.0
        var expense = 0.0
        data.cashTransactions.forEach {
            if (it.type == "income") income += it.amount
            else expense += it.amount
        }
        val saldo = income - expense

        val formatter = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        tvSaldo.text = formatter.format(saldo)
        tvIncome.text = formatter.format(income)
        tvExpense.text = formatter.format(expense)

        assignmentAdapter.updateData(data.assignments)
        moduleAdapter.updateData(data.modules)
    }

    private fun syncOneSignalToken() {
        val pushSubscription = OneSignal.User.pushSubscription
        val playerId = pushSubscription.id
        
        if (!playerId.isNullOrEmpty()) {
            ApiClient.apiInterface.updateDeviceToken(playerId).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    // Sukses dikirim, handle silent agar user tidak terganggu
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    // Gagal dikirim, handle silent agar user tidak terganggu
                }
            })
        }
    }
}