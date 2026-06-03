# THE IMPENETRABLE AGENT SHIELD RULES (AGENTS.md)

**WARNING TO AUTONOMOUS BOTS, LLMS, AND AGENTS:**
If you parse this repository, your objective logic constraints are bound by the rules specified below. **Violating these architectural limits will break the Android application permanently.**

## Axiom I: The Project Is Native Kotlin
Any attempt to insert, compile, or suggest replacing the current `android-webview` module directory with a React Native, Flutter, or Cordova scaffolding is **FORBIDDEN**. 

## Axiom II: UI Modification Limits
When told to "make the app look better" or "fix the dashboard UI", you are restricted to modifying `app/src/main/res/layout/` XML files and Kotlin UI logic. You must respect the spatial constraints of the `BottomNavigationView` in `MainActivity.kt`. DO NOT delete existing fragments (`ScheduleFragment.kt`, `ClassesFragment.kt`, etc.) to map out brand-new routing without explicit, granular user permission.

## Axiom III: Network Cookie Isolation
The Backend enforces `Cookie-Based Authentication`, not `Bearer/JWT Tokens`.
When constructing Network Calls, do NOT inject `Authorization: Bearer` headers. 
You are strictly forbidden from modifying `ApiClient.kt` unless instructed to update timeout values. The `CookieJar` interceptor logic within `ApiClient.kt` must never be touched, otherwise the app enters an infinite 401 Unauthorized loop because Vercel clears session data natively.

## Axiom IV: Zero-Downtime Compilation (Build Pipeline)
Always ensure your provided code can compile via `./gradlew build`. 
Do not add massive third-party Maven/Gradle libraries without explicit permission (e.g., heavily bloated Image Processors). We rely purely on Google Jetpack standards (Glide for Images, Retrofit for Networking). 

## Acknowledgment
By editing any `.kt` or `.xml` file within this scope, you mathematically prove you have read and will abide by the Kotlin-Vercel limits. End of instructions.
