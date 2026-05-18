<?php

$baseDir = __DIR__ . '/android-webview';

// Array of files to create
$files = [
    'build.gradle' => <<<EOT
buildscript {
    ext.kotlin_version = "1.9.0"
    repositories {
        google()
        mavenCentral()
    }
    dependencies {
        classpath "com.android.tools.build:gradle:8.1.0"
        classpath "org.jetbrains.kotlin:kotlin-gradle-plugin:\$kotlin_version"
    }
}
allprojects {
    repositories {
        google()
        mavenCentral()
    }
}
EOT,

    'settings.gradle' => <<<EOT
rootProject.name = "KelasHub"
include ':app'
EOT,

    'gradle.properties' => <<<EOT
org.gradle.jvmargs=-Xmx2048m -Dfile.encoding=UTF-8
android.useAndroidX=true
android.enableJetifier=true
EOT,

    'app/build.gradle' => <<<EOT
plugins {
    id 'com.android.application'
    id 'kotlin-android'
}

android {
    namespace 'com.waveproject.kelashub'
    compileSdk 34

    defaultConfig {
        applicationId "com.waveproject.kelashub"
        minSdk 24
        targetSdk 34
        versionCode 1
        versionName "1.0"
    }
    buildTypes {
        release {
            minifyEnabled false
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
    compileOptions {
        sourceCompatibility JavaVersion.VERSION_1_8
        targetCompatibility JavaVersion.VERSION_1_8
    }
    kotlinOptions {
        jvmTarget = '1.8'
    }
}

dependencies {
    implementation 'androidx.core:core-ktx:1.12.0'
    implementation 'androidx.appcompat:appcompat:1.6.1'
    implementation 'com.google.android.material:material:1.10.0'
    implementation 'androidx.constraintlayout:constraintlayout:2.1.4'
}
EOT,

    'app/src/main/AndroidManifest.xml' => <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns:android="http://schemas.android.com/apk/res/android">

    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" android:maxSdkVersion="32" />
    <uses-permission android:name="android.permission.READ_MEDIA_IMAGES" />
    <uses-permission android:name="android.permission.READ_MEDIA_VIDEO" />

    <application
        android:allowBackup="true"
        android:icon="@mipmap/ic_launcher"
        android:label="KelasHub"
        android:roundIcon="@mipmap/ic_launcher_round"
        android:supportsRtl="true"
        android:theme="@style/Theme.AppCompat.NoActionBar"
        android:usesCleartextTraffic="true">
        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:screenOrientation="portrait">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>
        </activity>
    </application>
</manifest>
EOT,

    'app/src/main/res/values/strings.xml' => <<<EOT
<resources>
    <string name="app_name">KelasHub</string>
</resources>
EOT,

    'app/src/main/res/layout/activity_main.xml' => <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<androidx.constraintlayout.widget.ConstraintLayout 
    xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent">

    <WebView
        android:id="@+id/webView"
        android:layout_width="match_parent"
        android:layout_height="match_parent" />

</androidx.constraintlayout.widget.ConstraintLayout>
EOT,

    'app/src/main/java/com/waveproject/kelashub/MainActivity.kt' => <<<EOT
package com.waveproject.kelashub

import android.annotation.SuppressLint
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    
    private var filePathCallback: ValueCallback<Array<Uri>>? = null
    private val FILE_CHOOSER_RESULT_CODE = 1

    private val URL_KELASHUB = "https://kelashub.vercel.app/" // PASTIKAN GANTI INI JIKA URL VERCEL BEDA

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        webView = findViewById(R.id.webView)
        val webSettings: WebSettings = webView.settings
        webSettings.javaScriptEnabled = true
        webSettings.domStorageEnabled = true
        webSettings.loadsImagesAutomatically = true
        webSettings.allowFileAccess = true
        webSettings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW

        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                if (url != null) {
                    view?.loadUrl(url)
                    return true
                }
                return false
            }
        }

        webView.webChromeClient = object : WebChromeClient() {
            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                if (this@MainActivity.filePathCallback != null) {
                    this@MainActivity.filePathCallback?.onReceiveValue(null)
                }
                this@MainActivity.filePathCallback = filePathCallback

                val intent = Intent(Intent.ACTION_GET_CONTENT)
                intent.addCategory(Intent.CATEGORY_OPENABLE)
                intent.type = "*/*"

                startActivityForResult(
                    Intent.createChooser(intent, "Pilih File Modul"),
                    FILE_CHOOSER_RESULT_CODE
                )
                return true
            }
        }

        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack()
                } else {
                    finish()
                }
            }
        })

        webView.loadUrl(URL_KELASHUB)
    }

    @Deprecated("Deprecated in Java")
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        if (requestCode == FILE_CHOOSER_RESULT_CODE) {
            if (filePathCallback == null) return
            val result = if (data == null || resultCode != RESULT_OK) null else arrayOf(data.data!!)
            filePathCallback?.onReceiveValue(result as Array<Uri>?)
            filePathCallback = null
        } else {
            super.onActivityResult(requestCode, resultCode, data)
        }
    }
}
EOT
];

// Create directories and write files
foreach ($files as $path => $content) {
    $fullPath = $baseDir . '/' . $path;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($fullPath, $content);
}

// Copy the icon if exists
$iconPath = __DIR__ . '/public/icon.png';
if (file_exists($iconPath)) {
    $mipmapDir = $baseDir . '/app/src/main/res/mipmap-xxhdpi';
    if (!is_dir($mipmapDir)) mkdir($mipmapDir, 0777, true);
    copy($iconPath, $mipmapDir . '/ic_launcher.png');
    copy($iconPath, $mipmapDir . '/ic_launcher_round.png');
}

echo "Proyek Android WebView berhasil di-generate di folder: android-webview/\n";
