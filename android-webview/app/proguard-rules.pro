# Retrofit & OkHttp
-dontwarn retrofit2.**
-keep class retrofit2.** { *; }
-keepattributes Signature
-keepattributes Exceptions

# Gson
-keep class com.google.gson.** { *; }
-keep class sun.misc.Unsafe { *; }
-keepattributes *Annotation*

# Keep all our application data models and API interfaces to prevent "Call return type must be parameterized" issues
-keep class com.waveproject.kelashub.** { *; }
-keep interface com.waveproject.kelashub.** { *; }
-keep enum com.waveproject.kelashub.** { *; }
