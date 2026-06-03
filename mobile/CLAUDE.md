# AI Agent Instructions for WaveProject Mobile Engineering

**CONTEXT:** You are operating inside the `/android-webview` repository.
**ENVIRONMENT:** Strict native Android Kotlin ecosystem.
**PROHIBITED ACTION:** Do NOT attempt to integrate Cordova, Ionic, or rewrite components to HTML/JS WebViews.

## Your Prime Directive:
You are assisting in building the **KelasHUB Android Client App**. This client communicates with a Vercel-hosted Laravel 11 Backend via REST JSON API (See `docs/API.md` out-of-bounds).

### Rule 1: Architecture Enforcement (MVVM & Retrofit)
- All network activities must be mapped inside `app/src/main/java/com/waveproject/kelashub/ApiInterface.kt`. 
- No hardcoded HTTP requests scattered inside UI classes (Activities/Fragments).
- Adhere strictly to the defined data classes in `Models.kt` to prevent GSON parsing crashes. If backend changes a boolean to Integer (e.g., `is_validated = 1`), ensure mapping handles it correctly.

### Rule 2: UI/UX Aesthetic Standardization
- **Never suggest bright themes.** This app enforces a *Zinc 900 Stealth Dark Mode* aesthetic.
- Components must rely on Android Jetpack XML `Material Components` (Material 3).
- Colors must be extracted from `res/values/colors.xml`. Avoid hardcoding `#HexCodes` in layout XML files.

### Rule 3: Do Not Hallucinate Vercel Solutions
If a network error (e.g., 504 Gateway Timeout or CORS block) occurs, **DO NOT** attempt to "fix" it by writing PHP code on the Android side or suggesting we migrate to a Node.js Express server. Your domain of control ends at Kotlin Retrofit Request generation. Tell the user to fix the Backend API Endpoint instead.

### Rule 4: The OneSignal Lifeline
If touching user login mechanisms (`MainActivity.kt` / `LoginActivity.kt`), absolutely ensure the `OneSignal.getUser().getPushSubscriptionId()` sync function is left completely intact. Removing this breaks the Zero-Delay asynchronous push network pipeline.
