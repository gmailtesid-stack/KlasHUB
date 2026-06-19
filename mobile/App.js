import React, { useState, useRef, useEffect } from 'react';
import { StatusBar } from 'expo-status-bar';
import {
  Text,
  View,
  TouchableOpacity,
  SafeAreaView,
  BackHandler,
  ActivityIndicator,
  Image,
  Animated,
  StyleSheet,
  Platform
} from 'react-native';
import { WebView } from 'react-native-webview';
import { LogIn } from 'lucide-react-native';
import * as SplashScreen from 'expo-splash-screen';
import Constants from 'expo-constants';

// 1. IMPORT LOGIC ONESIGNAL NATIVE
import { LogLevel, OneSignal } from 'react-native-onesignal';

SplashScreen.preventAutoHideAsync().catch(() => { });

export default function App() {
  const [isLaunched, setIsLaunched] = useState(false);
  const [loading, setLoading] = useState(false);
  const webViewRef = useRef(null);
  const fadeAnim = useRef(new Animated.Value(0)).current;

  // 2. INITIALIZE ONESIGNAL SAAT APLIKASI DIHP DIBUKA
  useEffect(() => {
    OneSignal.Debug.setLogLevel(LogLevel.Verbose);

    // GANTI string di bawah ini dengan OneSignal APP ID Anda yang valid
    OneSignal.initialize("MASUKKAN_ONESIGNAL_APP_ID_ANDA");

    // Otomatis minta izin Push Notification di HP
    OneSignal.Notifications.requestPermission(true);
  }, []);

  useEffect(() => {
    setTimeout(async () => {
      await SplashScreen.hideAsync().catch(() => { });
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 1000,
        useNativeDriver: true,
      }).start();
    }, 1500);

    const backAction = () => {
      if (isLaunched && webViewRef.current) {
        webViewRef.current.goBack();
        return true;
      }
      if (isLaunched) {
        setIsLaunched(false);
        return true;
      }
      return false;
    };

    const backHandler = BackHandler.addEventListener(
      'hardwareBackPress',
      backAction
    );

    return () => backHandler.remove();
  }, [isLaunched, fadeAnim]);

  const LandingScreen = () => (
    <SafeAreaView style={styles.container}>
      <StatusBar style="light" />
      <Animated.View style={[styles.centered, { opacity: fadeAnim }]}>

        <View style={styles.logoContainer}>
          <Image
            source={require('./assets/icon.png')}
            style={{ width: 140, height: 140 }}
            resizeMode="contain"
          />
        </View>

        <Text style={styles.title}>KELAS HUB</Text>
        <Text style={styles.subtitle}>
          Platform Manajemen Kelas Terpadu{"\n"}Versi Native Mobile
        </Text>

        <TouchableOpacity
          onPress={() => setIsLaunched(true)}
          activeOpacity={0.8}
          style={styles.button}
        >
          <LogIn size={20} color="white" />
          <Text style={styles.buttonText}>GO TO LOGIN</Text>
        </TouchableOpacity>

        <View style={styles.footer}>
          <Text style={styles.footerText}>NATIVE BUILD V1.0.3</Text>
        </View>

      </Animated.View>
    </SafeAreaView>
  );

  if (isLaunched) {
    return (
      <View style={styles.container}>
        <StatusBar style="light" />
        <View style={styles.webViewWrapper}>
          <WebView
            ref={webViewRef}
            source={{ uri: 'https://klas-hub.vercel.app/login' }}
            onLoadStart={() => setLoading(true)}
            onLoadEnd={() => setLoading(false)}
            style={{ flex: 1, backgroundColor: '#0d0d13' }}
            javaScriptEnabled={true}
            domStorageEnabled={true}
            startInLoadingState={true}
            scrollEnabled={true}
            originWhitelist={['*']}
            mixedContentMode="always"
            allowsInlineMediaPlayback={true}
            scalesPageToFit={true}
            mediaPlaybackRequiresUserAction={false}
            javaScriptCanOpenWindowsAutomatically={true}
            userAgent="Mozilla/5.0 (Linux; Android 13; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36"
            
            // 3. JEMBATAN SUNTIK TOKEN KE LOCALSTORAGE WEB
            injectedJavaScript={`
              (function() {
                setTimeout(function() {
                  if (window.localStorage) {
                    window.localStorage.setItem('native_onesignal_id', '${OneSignal.User.pushSubscription.id || ""}');
                  }
                }, 2000);
              })();
              true;
            `}
          />
          {loading && (
            <View style={styles.loadingOverlay}>
              <ActivityIndicator size="large" color="#3498db" />
              <Text style={styles.loadingText}>SINCHRONIZING SYSTEM...</Text>
            </View>
          )}
        </View>
      </View>
    );
  }

  return <LandingScreen />;
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0d0d13',
  },
  webViewWrapper: {
    flex: 1,
    paddingTop: Constants.statusBarHeight,
    backgroundColor: '#0d0d13',
  },
  centered: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 32,
  },
  logoContainer: {
    width: 170,
    height: 170,
    backgroundColor: '#161622',
    borderRadius: 45,
    borderWidth: 1,
    borderColor: '#232335',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 20,
    shadowColor: "#3498db",
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.2,
    shadowRadius: 20,
    elevation: 20,
  },
  title: {
    fontSize: 34,
    fontWeight: '900',
    color: 'white',
    letterSpacing: 4,
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 13,
    color: '#64748b',
    textAlign: 'center',
    marginBottom: 40,
    lineHeight: 20,
  },
  button: {
    backgroundColor: '#3498db',
    width: '100%',
    height: 60,
    borderRadius: 18,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  buttonText: {
    color: 'white',
    fontWeight: 'bold',
    marginLeft: 8,
    letterSpacing: 1.5,
  },
  footer: {
    position: 'absolute',
    bottom: 30,
  },
  footerText: {
    fontSize: 9,
    color: '#1e293b',
    letterSpacing: 5,
    fontWeight: 'bold',
  },
  loadingOverlay: {
    position: 'absolute',
    top: Constants.statusBarHeight,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: '#0d0d13',
    alignItems: 'center',
    justifyContent: 'center',
    zIndex: 9999,
  },
  loadingText: {
    color: 'white',
    fontSize: 11,
    marginTop: 20,
    letterSpacing: 3,
    fontWeight: 'bold'
  }
});