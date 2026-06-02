package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.NumberFormat
import java.util.Locale

class DashboardFragment : Fragment() {

    private lateinit var tvSaldo: TextView
    private lateinit var tvIncome: TextView
    private lateinit var tvExpense: TextView
    private lateinit var rvAssignments: RecyclerView
    private lateinit var rvModules: RecyclerView

    private lateinit var assignmentAdapter: AssignmentAdapter
    private lateinit var moduleAdapter: ModuleAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val root = inflater.inflate(R.layout.fragment_dashboard, container, false)
        initUI(root)
        fetchData()
        return root
    }

    private fun initUI(root: View) {
        tvSaldo = root.findViewById(R.id.tvSaldo)
        tvIncome = root.findViewById(R.id.tvIncome)
        tvExpense = root.findViewById(R.id.tvExpense)
        rvAssignments = root.findViewById(R.id.rvAssignments)
        rvModules = root.findViewById(R.id.rvModules)

        rvAssignments.layoutManager = LinearLayoutManager(requireContext())
        assignmentAdapter = AssignmentAdapter(listOf())
        rvAssignments.adapter = assignmentAdapter

        rvModules.layoutManager = LinearLayoutManager(requireContext(), LinearLayoutManager.HORIZONTAL, false)
        moduleAdapter = ModuleAdapter(listOf())
        rvModules.adapter = moduleAdapter
    }

    private fun fetchData() {
        val progress = requireActivity().findViewById<ProgressBar>(R.id.mainProgress)
        progress?.visibility = View.VISIBLE

        ApiClient.apiInterface.getDashboardData().enqueue(object : Callback<DashboardData> {
            override fun onResponse(call: Call<DashboardData>, response: Response<DashboardData>) {
                progress?.visibility = View.GONE
                if (response.isSuccessful) {
                    val data = response.body()
                    if (data != null) {
                        updateUI(data)
                    }
                } else {
                    if (response.code() == 401) {
                        startActivity(Intent(requireContext(), LoginActivity::class.java))
                        requireActivity().finish()
                    } else {
                        Toast.makeText(requireContext(), "Failed to load data", Toast.LENGTH_SHORT).show()
                    }
                }
            }

            override fun onFailure(call: Call<DashboardData>, t: Throwable) {
                progress?.visibility = View.GONE
                Toast.makeText(requireContext(), "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateUI(data: DashboardData) {
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
}
