# GarageOS - Guía Maestra de Docker

Esta guía centraliza toda la configuración para desarrollar GarageOS utilizando Docker Desktop.

---

## 🚀 Flujo de Trabajo Docker (Recomendado)

### 1. Arrancar contenedores
```powershell
docker-compose up -d
```

### 2. Ejecutar migraciones
```powershell
docker-compose exec app php artisan migrate
```

### 3. Acceso
Abre tu navegador en: [http://localhost:8000](http://localhost:8000)

### 4. Detener contenedores
```powershell
docker-compose down
```

---

## 🛠️ Stack Tecnológico (Docker)

| Componente | Versión | Notas |
|------------|---------|-------|
| **PHP** | 8.3-FPM | Docker |
| **PostgreSQL** | 16 + PostGIS | Docker |
| **Redis** | 7 | Docker |

---

## 🔑 Credenciales Docker

### PostgreSQL
- **Host:** `127.0.0.1`
- **Port:** `5432`
- **Database:** `garageos`
- **Username:** `garageos`
- **Password:** `garageos`

---

## 💻 Comandos de Desarrollo

### Docker
```powershell
# Arrancar
docker-compose up -d

# Migraciones
docker-compose exec app php artisan migrate

# Tests
docker-compose exec app php artisan test --parallel

# Shell en contenedor
docker-compose exec app sh

# Detener
docker-compose down
```

---

## ⚠️ Troubleshooting

| Problema | Solución |
| :--- | :--- |
| **Conexión DB fallida** | `docker-compose ps` y verifica que postgres esté corriendo |
| **Build fallido** | `docker-compose build --no-cache` |
| **Permisos storage** | `icacls storage /grant "IIS_IUSRS:(OI)(CI)F"` |
wsl -d Ubuntu -u root composer --version
wsl -d Ubuntu -u root node --version
wsl -d Ubuntu -u root mysql -u garageos -p'GarageOS2026Dev!' -e "SELECT 1;"
wsl -d Ubuntu -u root redis-cli ping
wsl -d Ubuntu -u root tesseract --version
```

## Troubleshooting

### MySQL: Plugin caching_sha2_password
Si hay errores de autenticación, el usuario `garageos` está configurado con `caching_sha2_password`.

### Redis no arranca
```powershell
wsl -d Ubuntu -u root service redis-server restart
```

### PHP-FPM para desarrollo
Para desarrollo con `php artisan serve`, no es necesario FPM. Se usa PHP CLI directamente.

# Guía de Desarrollo - GarageOS

## 🚀 Arranque Rápido (Windows)

### 1. Levantar servicios WSL
```powershell
# Abrir PowerShell como administrador
wsl -d Ubuntu -u root service mysql start
wsl -d Ubuntu -u root service redis-server start
```

### 2. Terminal 1: Backend (PHP)
```powershell
cd c:\laragon\www\app_garage
php artisan serve
```

### 3. Terminal 2: Frontend (Vite)
```powershell
cd c:\laragon\www\app_garage
npm run dev
```

### 4. Terminal 3: Queue (opcional)
```powershell
cd c:\laragon\www\app_garage
php artisan queue:listen --tries=1
```

### 5. Abrir navegador
```
http://localhost:8000
```

---

## 🐧 Arranque WSL (alternativo)

### 1. Entrar a WSL
```powershell
wsl -d Ubuntu -u root bash /mnt/c/laragon/www/app_garage/wsl-setup.sh
```

### 2. Desde WSL
```bash
cd /mnt/c/laragon/www/app_garage
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
```

---

## 🔧 Comandos Útiles

### Migraciones
```powershell
php artisan migrate
php artisan migrate:fresh --seed
```

### Tests
```powershell
php artisan test --parallel
```

### Build (producción)
```powershell
npm run build
```

### Linting
```powershell
vendor/bin/pint
```

---

## 📋 Checklist de Verificación

- [ ] MySQL corriendo (`wsl -d Ubuntu -u root service mysql status`)
- [ ] Redis corriendo (`wsl -d Ubuntu -u root redis-cli ping`)
- [ ] `.env` configurado (DB_PASSWORD=GarageOS2026Dev!)
- [ ] `php artisan migrate` ejecutado
- [ ] `npm install` ejecutado

---

## 🗂️ Estructura del Proyecto

```
app/Modules/
├── Vehicle/           # Vehículos y especificaciones
├── VehicleImport/     # Importación de vehículos
├── Maintenance/       # Service Packs
├── Documents/         # Documentos + OCR
├── Marketplace/       # Marketplace + valuations
├── Alerts/            # Reglas de alertas
└── Identity/          # Garajes y usuarios
```

---

## 🐛 Troubleshooting

### Error de conexión MySQL
Verificar que el servicio está corriendo y credenciales en `.env`.

### Error de Redis
```powershell
wsl -d Ubuntu -u root service redis-server restart
```

### Cache corrupta
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```
