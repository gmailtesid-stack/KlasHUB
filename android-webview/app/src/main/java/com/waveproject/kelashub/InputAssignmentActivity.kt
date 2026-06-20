package com.waveproject.kelashub

import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.RadioButton
import android.widget.RadioGroup
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.datepicker.MaterialDatePicker
import com.google.android.material.timepicker.MaterialTimePicker
import com.google.android.material.timepicker.TimeFormat
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class InputAssignmentActivity : AppCompatActivity() {

    private var selectedDateTime: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_input_assignment)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etSubjectName = findViewById<EditText>(R.id.etSubjectName)
        val etTitle = findViewById<EditText>(R.id.etTitle)
        val etDescription = findViewById<EditText>(R.id.etDescription)
        val tvDeadline = findViewById<TextView>(R.id.tvDeadline)
        val rgType = findViewById<RadioGroup>(R.id.rgType)
        val rbIndividu = findViewById<RadioButton>(R.id.rbIndividu)
        val etMembers = findViewById<EditText>(R.id.etMembers)
        val etMaterialLink = findViewById<EditText>(R.id.etMaterialLink)
        val btnSubmit = findViewById<Button>(R.id.btnSubmit)

        rgType.setOnCheckedChangeListener { _, checkedId ->
            if (checkedId == R.id.rbKelompok) {
                etMembers.visibility = View.VISIBLE
            } else {
                etMembers.visibility = View.GONE
                etMembers.setText("")
            }
        }

        tvDeadline.setOnClickListener {
            val datePicker = MaterialDatePicker.Builder.datePicker()
                .setTitleText("Pilih Tanggal Deadline")
                .build()

            datePicker.addOnPositiveButtonClickListener { selection ->
                val sdfDate = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                val formattedDate = sdfDate.format(Date(selection))

                val timePicker = MaterialTimePicker.Builder()
                    .setTimeFormat(TimeFormat.CLOCK_24H)
                    .setHour(23)
                    .setMinute(59)
                    .setTitleText("Pilih Waktu Deadline")
                    .build()

                timePicker.addOnPositiveButtonClickListener {
                    val formattedTime = String.format(Locale.getDefault(), "%02d:%02d", timePicker.hour, timePicker.minute)
                    selectedDateTime = "$formattedDate $formattedTime"
                    tvDeadline.text = "Deadline: $selectedDateTime"
                }
                timePicker.show(supportFragmentManager, "TIME_PICKER")
            }
            datePicker.show(supportFragmentManager, "DATE_PICKER")
        }

        btnSubmit.setOnClickListener {
            val subjectName = etSubjectName.text.toString().trim()
            val title = etTitle.text.toString().trim()
            val type = if (rbIndividu.isChecked) "individual" else "kelompok"
            
            if (subjectName.isEmpty() || title.isEmpty() || selectedDateTime.isEmpty()) {
                Toast.makeText(this, "Mohon isi Nama MK, Judul, dan Deadline", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            btnSubmit.isEnabled = false
            btnSubmit.text = "Menyimpan..."

            val description = etDescription.text.toString().trim().ifEmpty { null }
            val materialLink = etMaterialLink.text.toString().trim().ifEmpty { null }
            var members = etMembers.text.toString().trim().ifEmpty { null }
            
            if (type == "Kelompok" && members == null) {
                members = "Akan ditentukan"
            }

            ApiClient.apiInterface.storeAssignment(
                subjectName, title, description, selectedDateTime, materialLink, type, members
            ).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@InputAssignmentActivity, "Tugas berhasil ditambahkan!", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        Toast.makeText(this@InputAssignmentActivity, "Gagal menambahkan tugas", Toast.LENGTH_SHORT).show()
                        btnSubmit.isEnabled = true
                        btnSubmit.text = "Simpan Tugas"
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    Toast.makeText(this@InputAssignmentActivity, "Kesalahan jaringan", Toast.LENGTH_SHORT).show()
                    btnSubmit.isEnabled = true
                    btnSubmit.text = "Simpan Tugas"
                }
            })
        }
    }
}
