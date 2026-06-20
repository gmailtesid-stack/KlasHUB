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

    @POST("kh/api/logout")
    fun logout(): Call<Void>

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
    @PUT("kh/master-subject/{id}")
    fun updateMasterSubject(
        @Path("id") id: Int,
        @Field("name") name: String,
        @Field("sks") sks: Int,
        @Field("code") code: String,
        @Field("default_lecturer") defaultLecturer: String?
    ): Call<Void>

    @DELETE("kh/subject/{id}")
    fun deleteMasterSubject(@Path("id") id: Int): Call<Void>

    @FormUrlEncoded
    @POST("kh/student")
    fun addStudent(
        @Field("nim") nim: String,
        @Field("name") name: String,
        @Field("role") role: String
    ): Call<ApiResponse>

    @GET("kh/api/students")
    fun getAllStudents(): Call<StudentsListResponse>

    @FormUrlEncoded
    @PUT("kh/student/{id}")
    fun updateStudent(
        @Path("id") id: Int,
        @Field("nim") nim: String,
        @Field("name") name: String
    ): Call<Void>

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

    @FormUrlEncoded
    @POST("kh/class")
    fun registerClass(@FieldMap body: Map<String, String>): Call<ApiResponse>

    @FormUrlEncoded
    @POST("kh/schedule")
    fun storeSchedule(
        @Field("subject_name") subjectName: String,
        @Field("subject_code") subjectCode: String?,
        @Field("lecturer_name") lecturerName: String,
        @Field("day") day: String,
        @Field("time_start") timeStart: String,
        @Field("time_end") timeEnd: String,
        @Field("room") room: String,
        @Field("class_name") className: String?,
        @Field("delivery_type") deliveryType: String?
    ): Call<Void>

    @FormUrlEncoded
    @PUT("kh/schedule/{id}")
    fun updateSchedule(
        @Path("id") id: Int,
        @Field("subject_name") subjectName: String,
        @Field("subject_code") subjectCode: String?,
        @Field("lecturer_name") lecturerName: String,
        @Field("day") day: String,
        @Field("time_start") timeStart: String,
        @Field("time_end") timeEnd: String,
        @Field("room") room: String,
        @Field("class_name") className: String?,
        @Field("delivery_type") deliveryType: String?
    ): Call<Void>

    @DELETE("kh/schedule/{id}")
    fun deleteSchedule(@Path("id") id: Int): Call<Void>

    @FormUrlEncoded
    @POST("kh/assignment")
    fun storeAssignment(
        @Field("subject_name") subjectName: String,
        @Field("title") title: String,
        @Field("description") description: String?,
        @Field("deadline") deadline: String,
        @Field("material_link") materialLink: String?,
        @Field("type") type: String,
        @Field("members") members: String?
    ): Call<Void>

    @FormUrlEncoded
    @PUT("kh/assignment/{id}")
    fun updateAssignment(
        @Path("id") id: Int,
        @Field("subject_name") subjectName: String,
        @Field("title") title: String,
        @Field("description") description: String?,
        @Field("deadline") deadline: String,
        @Field("material_link") materialLink: String?,
        @Field("type") type: String,
        @Field("members") members: String?
    ): Call<Void>

    @DELETE("kh/assignment/{id}")
    fun deleteAssignment(@Path("id") id: Int): Call<Void>

    @Multipart
    @POST("kh/module")
    fun storeModule(
        @Part("subject_name") subjectName: okhttp3.RequestBody,
        @Part("type") type: okhttp3.RequestBody,
        @Part("title") title: okhttp3.RequestBody?,
        @Part("link_url") linkUrl: okhttp3.RequestBody?,
        @Part file: okhttp3.MultipartBody.Part?
    ): Call<Void>

    @FormUrlEncoded
    @PUT("kh/module/{id}")
    fun updateModuleLink(
        @Path("id") id: Int,
        @Field("subject_name") subjectName: String,
        @Field("type") type: String,
        @Field("title") title: String?,
        @Field("link_url") linkUrl: String?
    ): Call<Void>

    @DELETE("kh/module/{id}")
    fun deleteModule(@Path("id") id: Int): Call<Void>
}
