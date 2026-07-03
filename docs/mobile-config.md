# Mobile App Configuration

## Capacitor Setup

```bash
npm install @capacitor/core @capacitor/cli
npm install @capacitor/push-notifications
npm install @capacitor/camera
npm install @capacitor/filesystem
npm install @capacitor/share
npx cap init
npx cap add android
npx cap add ios
```

## App Icons y Splash

```bash
npx cap generate
```

## Configuración iOS/Android

### iOS (App Store)
- `ios/App/App/Info.plist` - Configurar permissions
- `ios/App/App/Assets.xcassets` - Iconos

### Android (Play Store)
- `android/app/src/main/AndroidManifest.xml` - Permissions
- `android/app/src/main/res/mipmap` - Iconos

## Permissions Necesarias

### Android
```xml
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.INTERNET" />
```

### iOS
```xml
<key>NSCameraUsageDescription</key>
<string>Necesario para escanear documentos</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>Necesario para guardar imágenes</string>
```

## Optimizaciones

### Imágenes
- Convertir a WebP automáticamente
- Redimensionar antes de subir
- Cachear imágenes con Capacitor

### Network
- Implementar retry en fallos
- Cachear respuestas con localStorage
- Mostrar estado offline
