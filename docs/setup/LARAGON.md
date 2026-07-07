# ðŸš€ Setup con Laragon (RECOMENDADO - 100% nativo)

> **Setup oficial.** Laravel + PostgreSQL corren nativos en Windows con Laragon.
> **Sin Docker**, sin overhead. MÃ¡ximo rendimiento.

---

## âš¡ Por quÃ© todo Laragon

| Aspecto | Todo Docker | Laragon + Docker DB | **Todo Laragon (100% nativo)** |
|---------|------------|--------------------|---------------------------------|
| Velocidad 1Âª carga | 8-11s | ~1s | **~1s** |
| RAM consumida | ~1GB | ~500MB | **~50MB** |
| Hot reload Vite | OK | Mejor | **Mejor** |
| Servicios extra | Docker Desktop | Docker Desktop | **Ninguno** |
| Auto-arranque | âŒ | âŒ | **âœ… con Laragon** |

---

## ðŸ“‹ Requisitos

- **Laragon** (Full): https://laragon.org/download/
  - Incluye PHP 8.x, Composer, Node.js
- **PostgreSQL 16** (instalador oficial): https://www.postgresql.org/download/windows/
  - Instalar en `C:\laragon\bin\postgresql\16`
- **Git**: https://git-scm.com/

> âš ï¸ Laragon NO incluye PostgreSQL. Lo instalamos manualmente en su carpeta `bin/` para integraciÃ³n nativa.

---

## ðŸŽ¯ Setup paso a paso

### 1. Instalar Laragon

Descarga e instala Laragon Full.

### 2. Instalar PostgreSQL en Laragon

1. Descarga PostgreSQL 16 desde https://www.postgresql.org/download/windows/
2. Ejecuta el instalador con estas opciones:
   - **Directorio**: `C:\laragon\bin\postgresql\16`
   - **Password del usuario `postgres`**: `postgres` (o la que prefieras)
   - **Port**: `5432`
   - **Locale**: `Spanish, Spain`
   - **NO** marques "Stack Builder" al final
3. **Reinicia PowerShell** para que tome el PATH

### 3. Ejecutar script de auto-configuraciÃ³n

```powershell
cd C:\laragon\www\app_garage
powershell -ExecutionPolicy Bypass -File scripts\setup-postgresql-native.ps1
```

Este script:
- AÃ±ade PostgreSQL al PATH
- Crea la DB `garageos`
- Crea el usuario `garageos`
- Configura el servicio para auto-arranque

### 4. Clonar el proyecto

```powershell
cd C:\laragon\www
git clone <repo-url> app_garage
cd app_garage
```

### 5. Instalar dependencias

```powershell
composer install
npm install
```

### 6. Configurar `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=garageos
DB_USERNAME=garageos
DB_PASSWORD=garageos
```

```powershell
php artisan key:generate
```

### 7. Migrar base de datos

```powershell
php artisan migrate
```

### 8. Arrancar

**Terminal 1**:
```powershell
php artisan serve
# â†’ http://localhost:8000
```

**Terminal 2**:
```powershell
npm run dev
# â†’ http://localhost:5173
```

---

### 1. Instalar Laragon

Descarga e instala Laragon Full. Esto trae:
- PHP (cualquier versiÃ³n 8.x)
- Composer
- Node.js
- HeidiSQL (cliente MySQL/MariaDB)
- Redis

### 2. Habilitar extensiones PHP necesarias

Edita `C:\laragon\bin\php\php-X.X.X\php.ini` y descomenta:

```ini
extension=pdo_pgsql
extension=pgsql
extension=zip
extension=intl
extension=gd
extension=mbstring
extension=bcmath
```

### 3. Instalar PostgreSQL en Laragon (opcional)

Si quieres PostgreSQL **nativo** sin Docker, descÃ¡rgalo desde:
- https://www.postgresql.org/download/windows/

Crea la DB:
```sql
CREATE DATABASE garageos;
CREATE USER garageos WITH PASSWORD 'garageos';
GRANT ALL PRIVILEGES ON DATABASE garageos TO garageos;
```

> âš ï¸ Si prefieres Docker DB (recomendado), salta este paso.

### 4. Clonar el proyecto

```powershell
cd C:\laragon\www
git clone <repo-url> app_garage
cd app_garage
```

### 5. Instalar dependencias

```powershell
composer install
npm install
```

### 6. Configurar `.env`

Copia `.env.example` a `.env` y configura:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1          # o "postgres" si usas Docker DB
DB_PORT=5432
DB_DATABASE=garageos
DB_USERNAME=garageos
DB_PASSWORD=garageos

REDIS_HOST=127.0.0.1       # o "redis" si usas Docker Redis
```

Genera la APP_KEY:
```powershell
php artisan key:generate
```

### 7. Levantar servicios Docker (DB)

```powershell
# Solo PostgreSQL + Redis (sin servicio `app`)
docker-compose up -d postgres redis
```

### 8. Migrar base de datos

```powershell
php artisan migrate
php artisan db:seed  # opcional
```

### 9. Arrancar Laravel + Vite

**Terminal 1** (Laravel):
```powershell
php artisan serve
# â†’ http://localhost:8000
```

**Terminal 2** (Vite dev):
```powershell
npm run dev
# â†’ http://localhost:5173
```

---

## ðŸ”„ Workflow diario

```powershell
# 1. Arrancar DB (si no estÃ¡ corriendo)
docker-compose up -d postgres redis

# 2. En terminal 1:
php artisan serve

# 3. En terminal 2:
npm run dev

# 4. Abrir navegador:
# http://localhost:8000
```

Para parar todo:
```powershell
# Ctrl+C en cada terminal
docker-compose stop
```

---

## ðŸ› Troubleshooting

### "could not find driver" (pgsql)
- Verifica que `pdo_pgsql` y `pgsql` estÃ©n descomentadas en `php.ini`
- Reinicia Laragon

### Puerto 8000 ocupado
```powershell
php artisan serve --port=8001
```

### Cambios en `.env` no se reflejan
```powershell
php artisan config:clear
```

### Cambios en cÃ³digo frontend no se reflejan
- Verifica que `npm run dev` estÃ© corriendo
- Recarga navegador con Ctrl+Shift+R

### Redis no conecta
- Si usas Laragon Redis: `REDIS_HOST=127.0.0.1`
- Si usas Docker: `REDIS_HOST=redis`

---

## ðŸš« Modo legacy (Docker completo)

âš ï¸ **No recomendado**. Solo si necesitas paridad total con producciÃ³n.

```powershell
# Levanta TODO en Docker (Laravel + DB)
docker-compose --profile legacy up -d --build
```

Rendimiento esperado: ~10x mÃ¡s lento que Laragon nativo.

Para volver al modo recomendado:
```powershell
docker-compose --profile legacy down
docker-compose up -d postgres redis
```

---

## ðŸ“¦ Para el otro programador del equipo

**Checklist de instalaciÃ³n (orden):**
1. âœ… Instalar Laragon Full
2. âœ… Instalar Docker Desktop
3. âœ… Clonar repo en `C:\laragon\www\app_garage`
4. âœ… `composer install`
5. âœ… `npm install`
6. âœ… Copiar `.env.example` â†’ `.env` y configurar
7. âœ… `php artisan key:generate`
8. âœ… `docker-compose up -d postgres redis`
9. âœ… `php artisan migrate`
10. âœ… `php artisan serve` + `npm run dev` (en terminales separadas)

Tiempo estimado: 15-20 minutos.
