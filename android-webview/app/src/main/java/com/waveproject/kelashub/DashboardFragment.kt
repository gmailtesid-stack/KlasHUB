package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.PopupMenu
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Button
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
    private lateinit var btnSemesterFilter: Button

    private lateinit var assignmentAdapter: AssignmentAdapter
    private lateinit var moduleAdapter: ModuleAdapter
    
    private var currentMaxSemester: Int = 1
    private var selectedSemester: Int? = null

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
        
        btnSemesterFilter = root.findViewById(R.id.btnSemesterFilter)
        btnSemesterFilter.setOnClickListener {
            val popup = PopupMenu(requireContext(), it)
            for (i in 1..currentMaxSemester) {
                popup.menu.add(0, i, 0, "Arsip Semester $i")
            }
            popup.setOnMenuItemClickListener { item ->
                selectedSemester = item.itemId
                btnSemesterFilter.text = "Semester ${item.itemId} ▼"
                fetchData()
                true
            }
            popup.show()
        }
    }

    private fun fetchData() {
        val progress = requireActivity().findViewById<ProgressBar>(R.id.mainProgress)
        progress?.visibility = View.VISIBLE

        ApiClient.apiInterface.getDashboardData(selectedSemester).enqueue(object : Callback<DashboardDataResponse> {
            override fun onResponse(call: Call<DashboardDataResponse>, response: Response<DashboardDataResponse>) {
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

            override fun onFailure(call: Call<DashboardDataResponse>, t: Throwable) {
                progress?.visibility = View.GONE
                Toast.makeText(requireContext(), "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateUI(data: DashboardDataResponse) {
        currentMaxSemester = data.classSemester
        if (selectedSemester == null) {
            btnSemesterFilter.text = "Semester ${data.classSemester} ▼"
        }
        
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
