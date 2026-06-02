package com.waveproject.kelashub

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ProfileFragment : Fragment() {

    private lateinit var tvName: TextView
    private lateinit var tvNim: TextView
    private lateinit var tvRole: TextView
    private lateinit var tvInitials: TextView
    private lateinit var progress: ProgressBar
    private lateinit var btnAdminPanel: Button
    private lateinit var btnLogout: Button
    private var currentUserRole: String = ""

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val root = inflater.inflate(R.layout.fragment_profile, container, false)
        tvName = root.findViewById(R.id.tvName)
        tvNim = root.findViewById(R.id.tvNim)
        tvRole = root.findViewById(R.id.tvRole)
        tvInitials = root.findViewById(R.id.tvInitials)
        progress = root.findViewById(R.id.progressProfile)
        btnAdminPanel = root.findViewById(R.id.btnAdminPanel)
        btnLogout = root.findViewById(R.id.btnLogout)

        btnLogout.setOnClickListener {
            // Bersihkan semua penyimpanan offline (Bypass Reset)
            requireContext().getSharedPreferences("AuthPrefs", android.content.Context.MODE_PRIVATE).edit().clear().apply()
            requireContext().getSharedPreferences("CookiePrefs", android.content.Context.MODE_PRIVATE).edit().clear().apply()
            requireContext().getSharedPreferences("OfflineCache", android.content.Context.MODE_PRIVATE).edit().clear().apply()
            
            startActivity(Intent(requireContext(), LoginActivity::class.java))
            requireActivity().finish()
        }
        
        btnAdminPanel.setOnClickListener {
            val intent = Intent(requireContext(), AdminActivity::class.java)
            intent.putExtra("USER_ROLE", currentUserRole)
            startActivity(intent)
        }

        fetchData()
        return root
    }

    private fun fetchData() {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.getProfile().enqueue(object : Callback<ProfileResponse> {
            override fun onResponse(call: Call<ProfileResponse>, response: Response<ProfileResponse>) {
                progress.visibility = View.GONE
                if (response.isSuccessful && response.body() != null) {
                    val student = response.body()!!.student
                    tvName.text = student.name
                    tvNim.text = student.nim
                    tvRole.text = student.role.uppercase().replace("_", " ")
                    if (student.name.isNotEmpty()) {
                        tvInitials.text = student.name.take(1).uppercase()
                    }
                    currentUserRole = student.role
                    if (student.role != "mahasiswa") {
                        btnAdminPanel.visibility = View.VISIBLE
                    }
                } else if (response.code() == 401) {
                    startActivity(Intent(requireContext(), LoginActivity::class.java))
                    requireActivity().finish()
                } else {
                    Toast.makeText(requireContext(), "Gagal memuat profil", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ProfileResponse>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(requireContext(), "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
