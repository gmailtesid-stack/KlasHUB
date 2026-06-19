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

class ScheduleFragment : Fragment() {

    private lateinit var rvSchedule: RecyclerView
    private lateinit var adapter: ScheduleAdapter
    private lateinit var progress: ProgressBar
    private lateinit var tvEmptySchedule: TextView

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val root = inflater.inflate(R.layout.fragment_schedule, container, false)
        rvSchedule = root.findViewById(R.id.rvSchedule)
        progress = root.findViewById(R.id.progressSchedule)
        tvEmptySchedule = root.findViewById(R.id.tvEmptySchedule)

        rvSchedule.layoutManager = LinearLayoutManager(requireContext())
        adapter = ScheduleAdapter(listOf())
        rvSchedule.adapter = adapter

        fetchData()
        return root
    }

    private fun fetchData() {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.getSchedule().enqueue(object : Callback<ScheduleResponse> {
            override fun onResponse(call: Call<ScheduleResponse>, response: Response<ScheduleResponse>) {
                progress.visibility = View.GONE
                if (response.isSuccessful && response.body() != null) {
                    val data = response.body()!!
                    try {
                        val prefs = requireContext().getSharedPreferences("OfflineCache", android.content.Context.MODE_PRIVATE)
                        val json = com.google.gson.Gson().toJson(data)
                        prefs.edit().putString("schedule_data", json).apply()
                    } catch (e: Exception) {}
                    
                    if (data.schedules.isEmpty()) {
                        tvEmptySchedule.visibility = View.VISIBLE
                        rvSchedule.visibility = View.GONE
                    } else {
                        tvEmptySchedule.visibility = View.GONE
                        rvSchedule.visibility = View.VISIBLE
                        adapter.updateData(data.schedules)
                    }
                } else if (response.code() == 401) {
                    startActivity(Intent(requireContext(), LoginActivity::class.java))
                    requireActivity().finish()
                } else {
                    Toast.makeText(requireContext(), "Gagal memuat jadwal", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ScheduleResponse>, t: Throwable) {
                progress.visibility = View.GONE
                try {
                    val prefs = requireContext().getSharedPreferences("OfflineCache", android.content.Context.MODE_PRIVATE)
                    val json = prefs.getString("schedule_data", null)
                    if (json != null) {
                        val data = com.google.gson.Gson().fromJson(json, ScheduleResponse::class.java)
                        if (data.schedules.isEmpty()) {
                            tvEmptySchedule.visibility = View.VISIBLE
                            rvSchedule.visibility = View.GONE
                        } else {
                            tvEmptySchedule.visibility = View.GONE
                            rvSchedule.visibility = View.VISIBLE
                            adapter.updateData(data.schedules)
                        }
                        Toast.makeText(requireContext(), "Mode Offline: Jadwal Tersimpan", Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(requireContext(), "Error koneksi, cache kosong", Toast.LENGTH_SHORT).show()
                    }
                } catch (e: Exception) {
                    Toast.makeText(requireContext(), "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            }
        })
    }
}
