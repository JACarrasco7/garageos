# 🚀 Arranque de Desarrollo (WSL + Nginx)

Este script configura un entorno profesional donde **Nginx corre en WSL** pero accedes mediante un dominio local en Windows.

## 🛠️ Requisitos Previos

Ejecuta esto una sola vez en PowerShell (como Administrador):
```powershell
wsl -d Ubuntu -u root apt update && wsl -d Ubuntu -u root apt install -y nginx
```

## 🚀 Cómo arrancar

1. Abre **PowerShell como Administrador** (necesario para editar el archivo `hosts`).
2. Ejecuta el script:
   ```powershell
   .\start-dev.ps1
   ```

El script hará lo siguiente:
- ✅ Crea la configuración de Nginx en WSL para `app-garage.test`.
- ✅ Añade `127.0.0.1 app-garage.test` a tu archivo `hosts` de Windows.
- ✅ Arranca Nginx y PHP-FPM en WSL.

**Acceso:** [http://app-garage.test](http://app-garage.test)

---

## 🛑 Cómo detener todo

Para apagar los servicios en WSL:
```powershell
wsl -d Ubuntu -u root service nginx stop
wsl -d Ubuntu -u root service php8.5-fpm stop
```

## 📋 Checklist de Verificación

- [ ] `wsl -d Ubuntu -u root service nginx status` (Debe decir `active (running)`)
- [ ] `ping app-garage.test` (Debe responder a `127.0.0.1`)
- [ ] `http://app-garage.test` carga la página de Laravel

## 🛠️ Troubleshooting

### Error de permisos en hosts
Asegúrate de que PowerShell se ejecutó como **Administrador**.

### Error de Nginx (Configuración)
Si cambias algo en el proyecto, puedes reiniciar Nginx así:
```powershell
wsl -d Ubuntu -u root service nginx restart
```

### Error de PHP-FPM
Si ves un error `502 Bad Gateway`, reinicia PHP-FPM:
```powershell
wsl -d Ubuntu -u root service php8.5-fpm restart
```

---

## 🐳 Entorno Docker (Recomendado)

Para trabajar con la base de datos PostgreSQL y el entorno completo aislado, usa Docker.

### 🚀 Arrancar Docker
```powershell
docker-compose up -d
```

### 🛠️ Comandos de Base de Datos (Artisan)
Ejecuta estos comandos **dentro** del contenedor `app`:

| Acción | Comando |
| :--- | :--- |
| **Ejecutar migraciones** | `php artisan migrate` |
| **Limpiar y resetear BD (BORRA DATOS)** | `php artisan migrate:fresh` |
| **Ver estado de migraciones** | `php artisan migrate:status` |
| **Forzar conexión a PostgreSQL** | `php artisan migrate --database=pgsql` |

### 🛠️ Comandos de Reconstrucción
Si haces cambios en el `.env`, en el `Dockerfile` o en las migraciones y no se reflejan:
```powershell
docker-compose up -d --build app
```

### 🛠️ Otros comandos útiles

```powershell
# Entrar a la terminal del contenedor
docker-compose exec app bash

# Ver logs de Laravel
docker-compose exec app tail -f storage/logs/laravel.log
```

---

## 🛠️ Troubleshooting

### Error de Base de Datos (Driver/Connection)
Si Laravel intenta usar `sqlite` o falla la conexión:
1. Verifica que `.env` tenga `DB_CONNECTION=pgsql` y `DB_HOST=postgres`.
2. Limpia caché: `docker-compose exec app php artisan config:clear`.
3. Reconstruye: `docker-compose up -d --build app`.
