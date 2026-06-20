package com.waveproject.kelashub

import android.os.Bundle
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.EditText
import android.widget.Spinner
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.timepicker.MaterialTimePicker
import com.google.android.material.timepicker.TimeFormat
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.util.Locale

class InputScheduleActivity : AppCompatActivity() {

    private var timeStartParam: String = ""
    private var timeEndParam: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_input_schedule)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etSubjectName = findViewById<EditText>(R.id.etSubjectName)
        val etSubjectCode = findViewById<EditText>(R.id.etSubjectCode)
        val etLecturerName = findViewById<EditText>(R.id.etLecturerName)
        val spinnerDay = findViewById<Spinner>(R.id.spinnerDay)
        val tvTimeStart = findViewById<TextView>(R.id.tvTimeStart)
        val tvTimeEnd = findViewById<TextView>(R.id.tvTimeEnd)
        val etRoom = findViewById<EditText>(R.id.etRoom)
        val etClassName = findViewById<EditText>(R.id.etClassName)
        val etDeliveryType = findViewById<EditText>(R.id.etDeliveryType)
        val btnSubmit = findViewById<Button>(R.id.btnSubmit)

        val days = arrayOf("Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu")
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_dropdown_item, days)
        spinnerDay.adapter = adapter

        tvTimeStart.setOnClickListener {
            showTimePicker { hour, minute ->
                val formatted = String.format(Locale.getDefault(), "%02d:%02d", hour, minute)
                timeStartParam = formatted
                tvTimeStart.text = "Waktu Mulai: $formatted"
            }
        }

        tvTimeEnd.setOnClickListener {
            showTimePicker { hour, minute ->
                val formatted = String.format(Locale.getDefault(), "%02d:%02d", hour, minute)
                timeEndParam = formatted
                tvTimeEnd.text = "Waktu Selesai: $formatted"
            }
        }

        btnSubmit.setOnClickListener {
            val subjectName = etSubjectName.text.toString().trim()
            val lecturerName = etLecturerName.text.toString().trim()
            val day = spinnerDay.selectedItem.toString().lowercase()
            val room = etRoom.text.toString().trim()
            
            if (subjectName.isEmpty() || lecturerName.isEmpty() || timeStartParam.isEmpty() || timeEndParam.isEmpty() || room.isEmpty()) {
                Toast.makeText(this, "Mohon lengkapi semua field yang wajib", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            btnSubmit.isEnabled = false
            btnSubmit.text = "Menyimpan..."

            val subjectCode = etSubjectCode.text.toString().trim().ifEmpty { null }
            val className = etClassName.text.toString().trim().ifEmpty { null }
            val deliveryType = etDeliveryType.text.toString().trim().ifEmpty { null }

            ApiClient.apiInterface.storeSchedule(
                subjectName, subjectCode, lecturerName, day, timeStartParam, timeEndParam, room, className, deliveryType
            ).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@InputScheduleActivity, "Jadwal berhasil ditambahkan!", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        Toast.makeText(this@InputScheduleActivity, "Gagal menambahkan jadwal", Toast.LENGTH_SHORT).show()
                        btnSubmit.isEnabled = true
                        btnSubmit.text = "Simpan Jadwal"
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    Toast.makeText(this@InputScheduleActivity, "Kesalahan jaringan", Toast.LENGTH_SHORT).show()
                    btnSubmit.isEnabled = true
                    btnSubmit.text = "Simpan Jadwal"
                }
            })
        }
    }

    private fun showTimePicker(onTimeSelected: (Int, Int) -> Unit) {
        val picker = MaterialTimePicker.Builder()
            .setTimeFormat(TimeFormat.CLOCK_24H)
            .setHour(8)
            .setMinute(0)
            .setTitleText("Pilih Waktu")
            .build()
        
        picker.addOnPositiveButtonClickListener {
            onTimeSelected(picker.hour, picker.minute)
        }
        
        picker.show(supportFragmentManager, "TIME_PICKER")
    }
}
