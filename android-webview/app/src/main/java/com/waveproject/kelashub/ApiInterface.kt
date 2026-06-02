package com.waveproject.kelashub

import retrofit2.Call
import retrofit2.http.*

interface ApiInterface {

    @FormUrlEncoded
    @POST("kh/api/login")
    fun login(
        @Field("name") nim: String,
        @Field("password") password: String
    ): Call<Void>

    @GET("kh/api/dashboard-data")
    fun getDashboardData(@Query("semester") semester: Int? = null): Call<DashboardDataResponse>

    @FormUrlEncoded
    @POST("kh/device-token")
    fun updateDeviceToken(
        @Field("player_id") playerId: String
    ): Call<Void>

    @GET("kh/api/attendance")
    fun getAttendance(): Call<AttendanceResponse>

    @GET("kh/api/schedule")
    fun getSchedule(): Call<ScheduleResponse>

    @GET("kh/api/profile")
    fun getProfile(): Call<ProfileResponse>

    @GET("kh/api/pending-validations")
    fun getPendingValidations(): Call<PendingValidationResponse>

    @FormUrlEncoded
    @POST("kh/validate")
    fun validateData(
        @Field("id") id: Int,
        @Field("type") type: String
    ): Call<Void>

    @FormUrlEncoded
    @POST("kh/cash")
    fun addCash(
        @Field("amount") amount: Double,
        @Field("type") type: String,
        @Field("description") description: String,
        @Field("transaction_date") transactionDate: String
    ): Call<Void>

    @Multipart
    @POST("kh/cash")
    fun addCashWithProof(
        @Part("amount") amount: okhttp3.RequestBody,
        @Part("type") type: okhttp3.RequestBody,
        @Part("description") description: okhttp3.RequestBody,
        @Part("transaction_date") transactionDate: okhttp3.RequestBody,
        @Part proofImage: okhttp3.MultipartBody.Part
    ): Call<Void>

    @FormUrlEncoded
    @POST("kh/attendance")
    fun requestIzin(
        @Field("attendances[0][student_id]") studentId: Int,
        @Field("attendances[0][status]") status: String,
        @Field("subject_name") subjectName: String,
        @Field("date") date: String,
        @Field("notes") notes: String
    ): Call<Void>

    @FormUrlEncoded
    @POST("kh/master-subject")
    fun addMasterSubject(
        @Field("name") name: String,
        @Field("sks") sks: Int,
        @Field("code") code: String,
        @Field("default_lecturer") defaultLecturer: String
    ): Call<Void>

    @FormUrlEncoded
    @POST("kh/student")
    fun addStudent(
        @Field("nim") nim: String,
        @Field("name") name: String,
        @Field("role") role: String
    ): Call<Void>

    @GET("kh/api/students")
    fun getAllStudents(): Call<StudentsListResponse>

    @DELETE("kh/student/{id}")
    fun deleteStudent(@Path("id") id: Int): Call<Void>

    @FormUrlEncoded
    @POST("kh/student/{id}/role")
    fun updateStudentRole(
        @Path("id") id: Int,
        @Field("role") role: String
    ): Call<Void>

    @POST("kh/class/next-semester")
    fun nextSemester(): Call<Void>

    @Multipart
    @POST("kh/upload-qris")
    fun uploadQris(
        @Part qrisImage: okhttp3.MultipartBody.Part
    ): Call<ApiResponse>
}
