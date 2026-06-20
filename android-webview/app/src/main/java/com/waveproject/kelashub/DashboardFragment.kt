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
    private lateinit var btnPayKas: Button
    private lateinit var tvEmptyAssignments: TextView
    private lateinit var tvEmptyModules: TextView

    private lateinit var assignmentAdapter: AssignmentAdapter
    private lateinit var moduleAdapter: ModuleAdapter
    
    private var currentMaxSemester: Int = 1
    private var selectedSemester: Int? = null
    private var currentQrisImage: String? = null
    private var currentUserRole: String = ""

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
        tvEmptyAssignments = root.findViewById(R.id.tvEmptyAssignments)
        tvEmptyModules = root.findViewById(R.id.tvEmptyModules)

        rvAssignments.layoutManager = LinearLayoutManager(requireContext())
        assignmentAdapter = AssignmentAdapter(listOf()) { assignment, view ->
            showOptionsMenuAssignment(assignment, view)
        }
        rvAssignments.adapter = assignmentAdapter

        rvModules.layoutManager = LinearLayoutManager(requireContext(), LinearLayoutManager.HORIZONTAL, false)
        moduleAdapter = ModuleAdapter(listOf()) { module, view ->
            showOptionsMenuModule(module, view)
        }
        rvModules.adapter = moduleAdapter
        
        btnPayKas = root.findViewById(R.id.btnPayKas)
        btnPayKas.setOnClickListener {
            val intent = Intent(requireContext(), PayKasActivity::class.java)
            intent.putExtra("QRIS_URL", currentQrisImage)
            startActivity(intent)
        }
        
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
                        try {
                            val prefs = SecurePrefs.get(requireContext(), "OfflineCache")
                            val json = com.google.gson.Gson().toJson(data)
                            val cacheKey = if (selectedSemester != null) "dashboard_data_$selectedSemester" else "dashboard_data_active"
                            prefs.edit().putString(cacheKey, json).apply()
                        } catch (e: Exception) {}
                        
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
                try {
                    val prefs = SecurePrefs.get(requireContext(), "OfflineCache")
                    val cacheKey = if (selectedSemester != null) "dashboard_data_$selectedSemester" else "dashboard_data_active"
                    val json = prefs.getString(cacheKey, null)
                    if (json != null) {
                        val data = com.google.gson.Gson().fromJson(json, DashboardDataResponse::class.java)
                        updateUI(data)
                        Toast.makeText(requireContext(), "Mode Offline Aktif (\u26A0\uFE0F)", Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(requireContext(), "Tidak ada koneksi dan cache kosong", Toast.LENGTH_SHORT).show()
                    }
                } catch (e: Exception) {
                    Toast.makeText(requireContext(), "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            }
        })
    }

    private fun updateUI(data: DashboardDataResponse) {
        currentMaxSemester = data.classSemester
        currentQrisImage = data.qrisImage

        if (selectedSemester == null) {
            btnSemesterFilter.text = "Semester ${data.classSemester} ▼"
        }
        
        currentUserRole = data.student.role
        try {
            val authPrefs = SecurePrefs.get(requireContext(), "AuthPrefs")
            authPrefs.edit().putString("currentUserRole", currentUserRole).apply()
        } catch (e: Exception) {}
        
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

        if (data.assignments.isEmpty()) {
            tvEmptyAssignments.visibility = View.VISIBLE
            rvAssignments.visibility = View.GONE
        } else {
            tvEmptyAssignments.visibility = View.GONE
            rvAssignments.visibility = View.VISIBLE
        }

        if (data.modules.isEmpty()) {
            tvEmptyModules.visibility = View.VISIBLE
            rvModules.visibility = View.GONE
        } else {
            tvEmptyModules.visibility = View.GONE
            rvModules.visibility = View.VISIBLE
        }
    }

    private fun showOptionsMenuAssignment(assignment: Assignment, view: View) {
        val prefs = SecurePrefs.get(requireContext(), "AuthPrefs")
        val role = prefs.getString("currentUserRole", "") ?: ""
        if (role == "mahasiswa" || role.isEmpty()) return

        val popup = PopupMenu(requireContext(), view)
        
        if (role == "ketua_kelas" || role == "sekretaris" || role == "bendahara" || role == "super_admin") {
            popup.menu.add(0, 1, 0, "Ubah Tugas")
        }
        
        if (role == "ketua_kelas" || role == "super_admin") {
            popup.menu.add(0, 2, 0, "Hapus Tugas")
        }
        
        popup.setOnMenuItemClickListener { item ->
            when (item.itemId) {
                1 -> {
                    val intent = Intent(requireContext(), InputAssignmentActivity::class.java)
                    intent.putExtra("ASSIGNMENT_ID", assignment.id)
                    startActivity(intent)
                }
                2 -> deleteAssignment(assignment.id)
            }
            true
        }
        popup.show()
    }

    private fun deleteAssignment(id: Int) {
        ApiClient.apiInterface.deleteAssignment(id).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(requireContext(), "Tugas Dihapus", Toast.LENGTH_SHORT).show()
                    fetchData()
                } else {
                    Toast.makeText(requireContext(), "Gagal: Akses Ditolak", Toast.LENGTH_SHORT).show()
                }
            }
            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(requireContext(), "Error Network", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun showOptionsMenuModule(module: Module, view: View) {
        val prefs = SecurePrefs.get(requireContext(), "AuthPrefs")
        val role = prefs.getString("currentUserRole", "") ?: ""
        if (role == "mahasiswa" || role.isEmpty()) return

        val popup = PopupMenu(requireContext(), view)
        
        if (role == "ketua_kelas" || role == "sekretaris" || role == "bendahara" || role == "super_admin") {
            if (module.type == "link") popup.menu.add(0, 1, 0, "Ubah Referensi")
        }
        
        if (role == "ketua_kelas" || role == "super_admin") {
            popup.menu.add(0, 2, 0, "Hapus Modul")
        }
        
        popup.setOnMenuItemClickListener { item ->
            when (item.itemId) {
                1 -> {
                    val intent = Intent(requireContext(), InputModuleActivity::class.java)
                    intent.putExtra("MODULE_ID", module.id)
                    startActivity(intent)
                }
                2 -> deleteModule(module.id)
            }
            true
        }
        popup.show()
    }

    private fun deleteModule(id: Int) {
        ApiClient.apiInterface.deleteModule(id).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(requireContext(), "Modul Dihapus", Toast.LENGTH_SHORT).show()
                    fetchData()
                } else {
                    Toast.makeText(requireContext(), "Gagal: Akses Ditolak", Toast.LENGTH_SHORT).show()
                }
            }
            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(requireContext(), "Error Network", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
