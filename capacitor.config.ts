import { defineConfig } from 'capacitor';

export default defineConfig({
  appId: 'com.garageos.app',
  appName: 'GarageOS',
  webDir: 'public',
  server: {
    url: 'http://10.0.2.2:8000', // Para desarrollo con Laravel
    cleartext: true,
  },
  plugins: {
    SplashScreen: {
      launchShowDuration: 0,
    },
    PushNotifications: {
      presentationOptions: ['badge', 'sound', 'alert'],
    },
  },
});