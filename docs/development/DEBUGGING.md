# Debugging

> Guía para resolver problemas comunes en GarageOS.

---

## 🐛 Herramientas

### Laravel Telescope (opcional)
- Instalación: `composer require laravel/telescope`
- Acceso: `/telescope`
- Inspecciona requests, queries, jobs, events

### Laravel Pulse
- Métricas en tiempo real
- Acceso: `/pulse`
- Requiere auth

### Laravel Debugbar
- Barra inferior en dev
- Muestra queries, requests, memoria
- Solo en `APP_DEBUG=true`

### Logs
```bash
# Laravel
tail -f storage/logs/laravel.log

# Docker
docker-compose logs -f app
docker-compose logs -f postgres
docker-compose logs -f redis

# WSL MySQL
wsl -d Ubuntu -u root tail -f /var/log/mysql/error.log
```

---

## 🔍 Errores comunes

### "Class 'X' not found"

**Causa:** Autoload desactualizado o namespace incorrecto.

```bash
# Solución 1: Regenerar autoload
composer dump-autoload

# Solución 2: Verificar namespace
# app/Modules/Vehicle/Models/Vehicle.php
namespace App\Modules\Vehicle\Models;
```

### "SQLSTATE[HY000] [2002] Connection refused"

**Causa:** MySQL/Postgres no está corriendo.

```bash
# Docker
docker-compose ps
docker-compose up -d postgres

# WSL
wsl -d Ubuntu -u root service mysql status
wsl -d Ubuntu -u root service mysql start
```

### "SQLSTATE[42P01]: Undefined table"

**Causa:** Migraciones no ejecutadas o en mal orden.

```bash
# Ver estado
php artisan migrate:status

# Ejecutar pendientes
php artisan migrate

# Resetear todo (CUIDADO: borra datos)
php artisan migrate:fresh --seed
```

### "Permission denied" en storage/

```bash
# Linux/Mac
chmod -R 777 storage bootstrap/cache

# Windows
icacls storage /grant "Everyone:(OI)(CI)F"
```

### "Redis connection refused"

```bash
# Docker
docker-compose restart redis

# WSL
wsl -d Ubuntu -u root service redis-server start
```

### "npm: command not found"

```bash
# Instalar Node.js
# Windows: descargar desde nodejs.org
# WSL
wsl -d Ubuntu -u root apt install -y nodejs npm
```

### "Vite manifest not found"

```bash
# Rebuild assets
npm run build

# O en dev
npm run dev
```

### "CSRF token mismatch"

**Causa:** Formulario sin `@csrf` o sesión expirada.

```blade
<form method="POST">
    @csrf
    <!-- ... -->
</form>
```

### "419 Page Expired"

**Causa:** Sesión expirada.

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

---

## 🧰 Comandos de diagnóstico

```bash
# Info del sistema
php artisan about

# Listar rutas
php artisan route:list

# Ver configuración
php artisan config:show database

# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ver queries lentas
php artisan db:show

# Test conexión DB
php artisan db:monitor

# Estado de la cola
php artisan queue:monitor
```

---

## 🔬 Debugging avanzado

### Dump & Die
```php
dd($variable);              // Dump and die
dump($variable);            // Dump sin parar
ray($variable);             // Spatie Ray (mejor que dd)

// En tests
test('something', function () {
    ray($user)->blue();     // Marca color
});
```

### Telescope (queries)
1. Ir a `/telescope/requests`
2. Click en un request
3. Ver queries ejecutadas
4. Detectar N+1

### Pulse (métricas)
1. Ir a `/pulse`
2. Ver:
   - Requests lentos
   - Queries lentas
   - Jobs fallidos
   - Excepciones

### Sentry (errores producción)
- Configurado vía `config/sentry.php`
- Errores se reportan automáticamente
- Permite ver stack trace completo

---

## 🐞 Debugging en producción

⚠️ **NUNCA** actives debug en producción (`APP_DEBUG=false`).

### Logs estructurados
```php
Log::error('Vehicle import failed', [
    'user_id' => $user->id,
    'vin' => $vin,
    'exception' => $e,
]);
```

### Tinker en producción
```bash
php artisan tinker --execute="
\$vehicle = Vehicle::find(123);
echo \$vehicle->plate;
"
```

---

## 📊 Performance

### Detectar N+1
Usar Laravel Pulse o Telescope. Si ves muchas queries similares, hay N+1.

Solución:
```php
// ❌ N+1
$vehicles = Vehicle::all();
foreach ($vehicles as $v) {
    echo $v->specs->engine_cc; // 1 query por vehicle
}

// ✅ Eager loading
$vehicles = Vehicle::with('specs')->get();
```

### Cachear queries pesadas
```php
$valuations = Cache::remember('valuations.all', 3600, function () {
    return Valuation::with('vehicle')->get();
});
```

---

## 🆘 Escalando problemas

Si encuentras un bug que no puedes resolver:

1. **Buscar en logs**: `storage/logs/laravel.log`
2. **Reproducir en test**: crear test que falle
3. **Buscar en GitHub Issues**: ¿alguien más lo tuvo?
4. **Preguntar al equipo**: Slack/Discord
5. **Documentar**: añade la solución aquí

---

## 📚 Recursos

- [Laravel Docs](https://laravel.com/docs)
- [Spatie Ray](https://myray.app/)
- [Laravel Pulse](https://pulse.laravel.com/)
- [Sentry](https://sentry.io/)
