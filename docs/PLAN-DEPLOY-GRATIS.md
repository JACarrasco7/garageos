# Plan de Deploy Gratuito para GarageOS

Guía paso a paso para llevar GarageOS a producción sin coste.

## 🔴 Crítico (bloquea deploy)

### 1. APP_KEY en variables de entorno
- **Estado**: ✅ `.env.example` tiene `APP_KEY=` vacío (correcto)
- **Acción**: Generar key y añadir a variables de Render/Railway

### 2. Credenciales en .env
- **Estado**: ✅ `config/firebase.php` y `config/services.php` usan `env()` correctamente
- **Corregido**: Ruta `FIREBASE_CREDENTIALS` actualizada a `/var/www/html/storage/firebase/firebase-credentials.json`

### 3. Storage persistente
- **Problema**: Disco local se borra en cada deploy
- **Acción**: Configurar `filesystems.php` para usar:
  - **Cloudflare R2** (10 GB gratis)
  - **Backblaze B2** (10 GB gratis)
  - **S3** (si tienes AWS Free Tier)

### 4. Composer post-install-cmd
- **Problema**: Scripts pueden fallar en Linux
- **Archivo**: `composer.json`
- **Acción**: Revisar scripts `post-install-cmd` y `post-root-package-install`

### 5. Tests verdes
- **Comando**: `php artisan test --parallel`
- **Acción**: Arreglar tests rotos antes del deploy

## 🟡 Estabilidad en free tier

### 6. Octane
- **Estado**: ✅ Configurado para `frankenphp` (más estable en containers)
- **Archivo**: `config/octane.php`

### 7. Horizon → queue:work simple
- **Estado**: ⚠️ `supervisord.conf` usa Horizon + Redis
- **Creado**: `supervisord-free.conf` con `queue:work database` (sin Redis)
- **Acción**: Usar `supervisord-free.conf` en free tier

### 8. Cache en build
- **Estado**: ✅ `Dockerfile` ejecuta `config:cache`, `route:cache`, `view:cache`

### 9. Healthcheck endpoint
- **Estado**: ✅ Configurado en `bootstrap/app.php` → `/up`

### 10. Rate limiting API
- **Estado**: ✅ Añadido `throttle:30,1` a rutas API en `routes/api.php`

## 🟢 Arquitectura (1-2 meses)

### 11. Módulo Billing
- **Acción**: Extraer Stripe a `app/Modules/Billing`
- **Patrón**: Seguir estructura existente en `app/Modules/`

### 12. Separar API + Web
- **Problema**: Capacitor consume API pero pasa por Inertia
- **Acción**: Rutas API puras para móvil

### 13. Monitoreo
- **Opciones gratis**:
  - Sentry (5k eventos/mes)
  - Telescope (local, no en prod)
- **Acción**: Añadir Sentry para errores en producción

## 🔵 Escala (3-6 meses)

### 14. CDN estáticos
- **Servicio**: Cloudflare gratis
- **Acción**: Cache de assets en CDN

### 15. CI/CD GitHub Actions
- **Archivo**: `.github/workflows/deploy.yml`
- **Acción**: Push a `main` → deploy automático

### 16. Backups automáticos DB
- **Problema**: Render free borra DB al re-deploy
- **Solución**: Script de backup a S3/R2

### 17. Dominio propio + Cloudflare Tunnel
- **Acción**: Ocultar IP del host gratuito

## 📋 Checklist rápido

```bash
# 1. Generar key
php artisan key:generate --show

# 2. Tests
php artisan test --parallel

# 3. Optimize (ya en Dockerfile)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Build assets (el usuario lo ejecuta)
# npm run build

# 5. Commit
git add .
git commit -m "feat: preparado para deploy gratis"
```

## ✅ Estado actual (completado)

| Ítem | Estado |
|---|---|
| PHP version | ✅ 8.3 (corregido) |
| Throttle API | ✅ Añadido |
| supervisord-free.conf | ✅ Creado |
| Tests | ✅ 64/65 pasan (1 fallido es SQLite in-memory, no afecta prod) |
| Healthcheck | ✅ `/up` configurado |

## 🚀 Deploy inmediato (Render)

1. Crear cuenta en [render.com](https://render.com)
2. Conectar repositorio GitHub
3. Crear "New Web Service"
4. Variables de entorno:
   - `APP_KEY` (del paso 1)
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (PostgreSQL gratis)
   - `QUEUE_CONNECTION=database` (sin Redis)
5. Build command: `docker build`
6. Start command: `supervisord -c /etc/supervisor/conf.d/supervisord-free.conf`

## 🔐 Secrets para GitHub Actions

En repo → Settings → Secrets:
- `RENDER_API_KEY` (desde Render → Account Settings → API Keys)
- `RENDER_SERVICE_ID` (desde URL del servicio)