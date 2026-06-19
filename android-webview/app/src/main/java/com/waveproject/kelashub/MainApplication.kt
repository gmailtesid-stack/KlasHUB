package com.waveproject.kelashub

import android.app.Application
import com.onesignal.OneSignal
import com.onesignal.debug.LogLevel

class MainApplication : Application() {
    override fun onCreate() {
        super.onCreate()

        // OneSignal Initialization
        OneSignal.Debug.logLevel = LogLevel.VERBOSE
        OneSignal.initWithContext(this, BuildConfig.ONESIGNAL_APP_ID)

        // Offline First Initialization
        ApiClient.init(this)
    }
}
