# 🚀 GarageOS Cheat Sheet

Guía rápida de comandos para el desarrollo diario.

## � Arranque rápido (cada día)

**Terminal 1** (Docker - Backend + DB):
```bash
docker-compose up -d
```

**Terminal 2** (Vite - Frontend):
```bash
npm run dev
```

**Navegador:** http://localhost:8000

## �🐳 Docker & Entorno

| Acción | Comando |
| :--- | :--- |
| **Arrancar todo** | `docker-compose up -d` |
| **Parar todo** | `docker-compose down` |
| **Reconstruir (si cambias .env o Dockerfile)** | `docker-compose up -d --build` |
| **Reconstrucción total (limpia caché)** | `docker-compose down && docker-compose build --no-cache && docker-compose up -d` |
| **Ver logs en tiempo real** | `docker-compose logs -f` |
| **Entrar a la terminal del contenedor** | `docker-compose exec app bash` |

## 🐘 Laravel & Base de Datos (Artisan)

**IMPORTANTE:** Ejecuta estos comandos **dentro** del contenedor usando `docker-compose exec app`.

| Acción | Comando |
| :--- | :--- |
| **Ejecutar migraciones** | `php artisan migrate` |
| **Limpiar y resetear BD (BORRA DATOS)** | `php artisan migrate:fresh` |
| **Ver estado de migraciones** | `php artisan migrate:status` |
| **Limpiar caché de configuración** | `php artisan config:clear` |
| **Limpiar caché de rutas** | `php artisan route:clear` |
| **Limpiar caché de vistas** | `php artisan view:clear` |
| **Forzar conexión a PostgreSQL** | `php artisan migrate --database=pgsql` |

## 🎨 Frontend (Vite & NPM)

| Acción | Comando |
| :--- | :--- |
| **Modo desarrollo (Vite)** | `npm run dev` (en tu terminal local, NO dentro de Docker) |
| **Compilar para producción** | `npm run build` |
| **Instalar nueva dependencia** | `npm install <nombre>` |

> **⚠️ IMPORTANTE:** `npm run dev` **SIEMPRE** se ejecuta en tu terminal local (fuera del contenedor). El contenedor accede vía `localhost:5173`. No montar `node_modules` desde Windows al contenedor (binarios incompatibles).

## 🛠️ Troubleshooting (Solución de problemas)

### "Connection refused" o "Could not find driver"
Si Laravel intenta usar `sqlite` en lugar de `pgsql`:
1. Verifica que `.env` tenga `DB_CONNECTION=pgsql` y `DB_HOST=postgres`.
2. Ejecuta `docker-compose exec app php artisan config:clear`.
3. Si persiste, reconstruye: `docker-compose up -d --build`.

### "APP_KEY" / "No application encryption key"
1. Ejecuta `php artisan key:generate` en local.
2. Copia el `APP_KEY=...` al archivo `.env.docker`.
3. Ejecuta `docker-compose restart app`.

### Cambios en `app/`, `config/`, `routes/` no se reflejan
El `COPY . .` del Dockerfile copia el código al construir. Para cambios:
```bash
docker-compose build app
docker-compose up -d app
```
Los bind mounts de `./app`, `./config` etc. están deshabilitados por rendimiento (Windows Docker es muy lento).

### Errores CSP en consola (Content Security Policy)
Si ves errores de fuentes/scripts bloqueados:
- Edita `app/Http/Middleware/SecurityHeadersMiddleware.php` (rama `local`).
- Reconstruye: `docker-compose build app && docker-compose up -d app`.

### Vite tarda mucho o no carga
1. Verifica que `npm run dev` esté corriendo en tu terminal local.
2. Verifica `vite.config.js` tenga `server.host = 'localhost'`.
3. Recarga el navegador con Ctrl+Shift+R.

### "Relation '...' does not exist" (Error de Migración)
Si una migración falla por una tabla que "no existe":
1. Es probable que el orden de las migraciones sea incorrecto.
2. Si has corregido el archivo, el contenedor no lo sabe. **Reconstruye la imagen**: `docker-compose up -d --build app`.
3. Si la base de datos quedó corrupta, usa `php artisan migrate:fresh`.

### "The following dependencies are imported but could not be resolved"
Si Vite se queja de paquetes faltantes:
1. Ejecuta `npm install`.
2. Si sigue fallando, borra `node_modules` y reinstala: `rm -rf node_modules && npm install`.

### SuperAdmin no accesible
Si el usuario SuperAdmin no existe:
```bash
php artisan db:seed --class=SuperAdminSeeder
```
Usuario: `jacararsco@garageos.com` / Password: `admin`
