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
    fun getDashboardData(): Call<DashboardData>

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
}
