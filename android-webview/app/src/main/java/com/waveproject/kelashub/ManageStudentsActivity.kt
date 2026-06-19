package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.PopupMenu
import android.widget.ProgressBar
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ManageStudentsActivity : AppCompatActivity() {

    private lateinit var rvManageStudents: RecyclerView
    private lateinit var adapter: ManageStudentsAdapter
    private lateinit var progress: ProgressBar

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_manage_student)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }
        progress = findViewById(R.id.progressManage)
        rvManageStudents = findViewById(R.id.rvManageStudents)
        rvManageStudents.layoutManager = LinearLayoutManager(this)

        adapter = ManageStudentsAdapter(listOf()) { student, view ->
            showOptionsMenu(student, view)
        }
        rvManageStudents.adapter = adapter

        fetchData()
    }

    private fun fetchData() {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.getAllStudents().enqueue(object : Callback<StudentsListResponse> {
            override fun onResponse(call: Call<StudentsListResponse>, response: Response<StudentsListResponse>) {
                progress.visibility = View.GONE
                if (response.isSuccessful && response.body() != null) {
                    adapter.updateData(response.body()!!.students)
                } else {
                    Toast.makeText(this@ManageStudentsActivity, "Gagal memuat daftar", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<StudentsListResponse>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(this@ManageStudentsActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun showOptionsMenu(student: Student, view: View) {
        val popup = PopupMenu(this, view)
        popup.menu.add(0, 1, 0, "Mutasi Jabatan (Role)")
        popup.menu.add(0, 2, 0, "Keluarkan Mahasiswa")
        popup.setOnMenuItemClickListener {
            when (it.itemId) {
                1 -> showRoleDialog(student)
                2 -> showDeleteDialog(student)
            }
            true
        }
        popup.show()
    }

    private fun showRoleDialog(student: Student) {
        val roles = arrayOf("mahasiswa", "ketua_kelas", "sekretaris", "bendahara")
        val displayRoles = arrayOf("Mahasiswa Biasa", "Ketua Kelas", "Sekretaris", "Bendahara")
        
        val currentIdx = roles.indexOf(student.role).takeIf { it >= 0 } ?: 0

        MaterialAlertDialogBuilder(this)
            .setTitle("Mutasi Role: ${student.name}")
            .setSingleChoiceItems(displayRoles, currentIdx) { dialog, which ->
                val selectedRole = roles[which]
                updateRole(student.id, selectedRole)
                dialog.dismiss()
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun showDeleteDialog(student: Student) {
        MaterialAlertDialogBuilder(this)
            .setTitle("Konfirmasi")
            .setMessage("Keluarkan ${student.name} dari kelas?\n(Tindakan ini tidak bisa dibatalkan)")
            .setPositiveButton("Hapus") { _, _ -> deleteStudent(student.id) }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun updateRole(id: Int, newRole: String) {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.updateStudentRole(id, newRole).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ManageStudentsActivity, "Jabatan diperbarui", Toast.LENGTH_SHORT).show()
                    fetchData()
                } else {
                    progress.visibility = View.GONE
                    Toast.makeText(this@ManageStudentsActivity, "Akses ditolak", Toast.LENGTH_SHORT).show()
                }
            }
            override fun onFailure(call: Call<Void>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(this@ManageStudentsActivity, "Gagal mutasi", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun deleteStudent(id: Int) {
        progress.visibility = View.VISIBLE
        ApiClient.apiInterface.deleteStudent(id).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ManageStudentsActivity, "Berhasil dihapus", Toast.LENGTH_SHORT).show()
                    fetchData()
                } else {
                    progress.visibility = View.GONE
                    Toast.makeText(this@ManageStudentsActivity, "Gagal/Ditolak (Super Admin?)", Toast.LENGTH_SHORT).show()
                }
            }
            override fun onFailure(call: Call<Void>, t: Throwable) {
                progress.visibility = View.GONE
                Toast.makeText(this@ManageStudentsActivity, "Gagal menghapus", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
