package com.waveproject.kelashub

import android.app.Application
import com.onesignal.OneSignal
import com.onesignal.debug.LogLevel

class MainApplication : Application() {
    override fun onCreate() {
        super.onCreate()

        // OneSignal Initialization
        OneSignal.Debug.logLevel = LogLevel.VERBOSE
        OneSignal.initWithContext(this, "04a9cff3-874a-4e84-96c0-f79cfa86d255")

        // Offline First Initialization
        ApiClient.init(this)
    }
}
