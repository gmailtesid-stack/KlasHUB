package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ProgressBar
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class AttendanceFragment : Fragment() {

    private lateinit var rvAttendance: RecyclerView
    private lateinit var adapter: AttendanceAdapter
    private lateinit var progress: ProgressBar

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val root = inflater.inflate(R.layout.fragment_attendance, container, false)
        rvAttendance = root.findViewById(R.id.rvAttendance)
        progress = root.findViewById(R.id.progressAttendance)

        rvAttendance.layoutManager = LinearLayoutManager(requireContext())
        adapter = AttendanceAdapter(listOf())
        rvAttendance.adapter = adapter

        fetchData()
        return root
    }

    private fun fetchData() {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.getAttendance().enqueue(object : Callback<AttendanceResponse> {
            override fun onResponse(call: Call<AttendanceResponse>, response: Response<AttendanceResponse>) {
                progress.visibility = View.GONE
                if (response.isSuccessful && response.body() != null) {
                    adapter.updateData(response.body()!!.myAttendances)
                } else if (response.code() == 401) {
                    startActivity(Intent(requireContext(), LoginActivity::class.java))
                    requireActivity().finish()
                } else {
                    Toast.makeText(requireContext(), "Failed to load attendance", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<AttendanceResponse>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(requireContext(), "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
