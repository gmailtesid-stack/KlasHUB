package com.waveproject.kelashub

import com.google.gson.annotations.SerializedName

data class LoginResponse(
    val success: Boolean,
    val message: String?
)

data class DashboardData(
    @SerializedName("semua_mahasiswa") val students: List<Student>,
    @SerializedName("semua_tugas") val assignments: List<Assignment>,
    @SerializedName("semua_modul") val modules: List<Module>,
    @SerializedName("transaksi_kas") val cashTransactions: List<CashTransaction>
)

data class Student(
    val id: Int,
    val name: String,
    val nim: String,
    val role: String
)

data class Assignment(
    val id: Int,
    val title: String,
    val subject_name: String,
    val deadline: String,
    val type: String
)

data class Module(
    val id: Int,
    val title: String,
    val subject_name: String,
    val type: String
)

data class CashTransaction(
    val id: Int,
    val amount: Double,
    val type: String,
    val description: String,
    val transaction_date: String
)

data class AttendanceResponse(
    @SerializedName("absensi_saya") val myAttendances: List<AttendanceStat>
)

data class AttendanceStat(
    val subject: String,
    @SerializedName("total_alfa") val totalAlfa: Int,
    val nyawa: Int,
    @SerializedName("status_nilai") val status: String,
    @SerializedName("is_banned") val isBanned: Boolean
)

data class ScheduleResponse(
    val schedules: List<ScheduleData>
)

data class ScheduleData(
    val id: Int,
    @SerializedName("subject_name") val subjectName: String,
    @SerializedName("lecturer_name") val lecturer: String,
    val day: String,
    @SerializedName("time_start") val timeStart: String?,
    @SerializedName("time_end") val timeEnd: String?,
    val room: String?,
    @SerializedName("delivery_type") val deliveryType: String?,
    @SerializedName("is_validated") val isValidated: Boolean
)

data class ProfileResponse(
    val student: Student
)

data class StudentsListResponse(
    val students: List<Student>
)

data class PendingValidationResponse(
    val pending: List<PendingValidationItem>
)

data class PendingValidationItem(
    val id: Int,
    val type: String,
    val title: String,
    val description: String
)
