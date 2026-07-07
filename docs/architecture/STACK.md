# Stack Tecnológico

> Stack completo de GarageOS con versiones y justificación.

---

## 🎯 Resumen

| Capa | Tecnología | Versión |
|------|-----------|---------|
| **Backend Framework** | Laravel | 13.x |
| **Lenguaje** | PHP | 8.4 |
| **Frontend Framework** | Vue.js | 3.5 |
| **Render Mode** | Inertia.js | 3.x |
| **Type System** | TypeScript | 5.8 |
| **CSS Framework** | Tailwind CSS | 4.x |
| **UI Components** | shadcn-vue | latest |
| **Build Tool** | Vite | 5.x |
| **Base de datos** | PostgreSQL | 16 |
| **Extensión DB** | PostGIS | 3.4 |
| **Cache/Queue** | Redis | 7 |
| **Server** | Octane + FrankenPHP | latest |
| **Mobile** | Capacitor | 7 |
| **Tests** | Pest PHP | 4 |
| **Containerización** | Docker | latest |

---

## 🔧 Backend (PHP/Laravel)

### Dependencias principales
```json
{
  "laravel/framework": "^13.0",
  "laravel/sanctum": "^4.0",
  "laravel/pulse": "^1.7",
  "laravel/scout": "^11.2",
  "laravel/cashier": "^15.0",
  "inertiajs/inertia-laravel": "^3.0",
  "spatie/laravel-permission": "^8.0",
  "spatie/laravel-medialibrary": "^11.23",
  "spatie/laravel-backup": "^10.3",
  "barryvdh/laravel-dompdf": "^3.1",
  "chillerlan/php-qrcode": "^6.0",
  "kreait/firebase-php": "^7.0",
  "smalot/pdfparser": "^2.0"
}
```

### Versiones PHP
- PHP 8.4+ requerido
- Extensiones: bcmath, ctype, curl, dom, fileinfo, intl, mbstring, opcache, pdo_pgsql, redis, zip, gd

---

## 🎨 Frontend (Vue/Inertia)

### Dependencias principales
```json
{
  "vue": "^3.5",
  "@inertiajs/vue3": "^3.0",
  "typescript": "^5.8",
  "tailwindcss": "^4.3",
  "@tailwindcss/vite": "^4.3",
  "class-variance-authority": "^0.7",
  "clsx": "^2.1",
  "tailwind-merge": "^3.6",
  "lucide-vue-next": "^1.0",
  "reka-ui": "^2.0",
  "@tanstack/vue-table": "^8.21",
  "vee-validate": "^4.0",
  "zod": "^3.0",
  "vue-sonner": "^2.0"
}
```

### Componentes shadcn-vue instalados
Button, Badge, Input, Label, Checkbox, Card, Table, Select, Dialog, DropdownMenu, Avatar, Form, Separator, Sheet, Sonner, Tabs

---

## 🗄️ Base de datos

### ¿Por qué PostgreSQL?

- **JSONB** indexable para datos dinámicos (specs de vehículos, raw extraction OCR)
- **Full-text search nativo** con stemmer español (`to_tsvector`)
- **Mejor concurrencia** (MVCC maduro)
- **PostGIS** disponible si necesitamos geolocalización
- **Particionado nativo** para escalar por fecha/tenant

### Alternativas descartadas
- MySQL: sin JSONB ni full-text tan potente
- SQLite: solo para tests
- MongoDB: relacional encaja mejor con el dominio

---

## 🚀 Server / Producción

### FrankenPHP + Octane
- PHP moderno con worker mode
- HTTP/2, HTTP/3 nativos
- Reduce latencia 3-5x vs php-fpm
- Compatible con todo el ecosistema Laravel

### Docker
- PHP 8.4-FPM con extensiones
- PostgreSQL 16 + PostGIS
- Redis 7-alpine

---

## 📱 Mobile

### Capacitor 7
- Wrapper nativo para iOS/Android
- Reutiliza el código Vue/Inertia
- Acceso a APIs nativas (cámara, GPS, push)
- Compilación a APK/IPA

---

## 🧪 Testing

### Pest PHP 3
- Sintaxis moderna sobre PHPUnit
- Paralelización nativa (`--parallel`)
- 115+ tests (114 passing)

---

## 📦 Package Manager

- **Composer** 2.x (PHP)
- **npm** 10.x (Node.js)

---

## 🔐 Seguridad

- **Sanctum** - API tokens
- **Spatie Permission** - Roles y permisos
- **Bcrypt** - Password hashing (rounds: 12)
- **CSRF** - Protección formularios
- **CORS** - Configurado para frontend

---

## 🔍 Observabilidad

- **Laravel Pulse** - Métricas en tiempo real
- **Laravel Telescope** (opcional) - Debug
- **Sentry** - Error tracking (configurado)
- **Horizon** - Queue monitoring (configurado)

---

## 📋 Convenciones de versionado

- **Laravel**: Major.Minor (12.x)
- **Vue**: Major.Minor (3.5)
- **PHP**: Major.Minor (8.4)
- **PostgreSQL**: Major (16)
- **Node**: Major.Minor (22.x)

---

## 🔄 Actualizaciones

Para actualizar el stack:

```bash
# Backend
composer update

# Frontend
npm update

# Verificar compatibilidades
composer outdated
npm outdated
```
