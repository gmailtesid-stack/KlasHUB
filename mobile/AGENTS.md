# KelasHUB Android App — Agent & AI Context

## Project Context
This is the **Android native Kotlin** module of the KelasHUB platform.

- **Backend Base URL**: `https://klas-hub.vercel.app/`
- **Authentication**: Session-based (cookie jar managed by OkHttp)
- **HTTP Library**: Retrofit2 + OkHttp3 + Gson
- **Push Notifications**: OneSignal SDK v5.x
- **OneSignal App ID**: `04a9cff3-874a-4e84-96c0-f79cfa86d255`
- **Min SDK**: 24 (Android 7.0+)
- **Target SDK**: 34 (Android 14)

## Key Files
| File | Purpose |
|:---|:---|
| `MainApplication.kt` | App-level init, OneSignal SDK initialization |
| `MainActivity.kt` | Dashboard screen + `syncOneSignalToken()` |
| `LoginActivity.kt` | Login form, session initiation |
| `DashboardActivity.kt` | Main launcher activity |
| `ApiClient.kt` | Retrofit + OkHttp singleton with cookie persistence |
| `ApiInterface.kt` | Retrofit endpoint definitions |
| `app/build.gradle` | Dependencies: Retrofit, OkHttp, OneSignal |

## Critical Conventions
1. **All API calls** go through `ApiClient.apiInterface` — never instantiate Retrofit directly.
2. **OneSignal token sync**: Always call `syncOneSignalToken()` in `MainActivity.onCreate()` after data is loaded.
3. **Token endpoint**: `POST /kh/device-token` with form field `player_id` = OneSignal Subscription ID.
4. **Cookie handling**: `ApiClient` includes a `CookieJar` to persist session cookies across requests.
5. **Error handling**: Handle `401` responses by redirecting to `LoginActivity`.

## OneSignal Integration Notes
- SDK is initialized in `MainApplication` (not Activity) to ensure it runs before any UI.
- `OneSignal.User.pushSubscription.id` returns the unique UUID for the device.
- The ID is nullable — always null-check before sending to backend.
- Token changes when user reinstalls the app or clears app data.
