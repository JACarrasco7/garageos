# 🚢 Guía Completa de Docker para GarageOS

> Guía paso a paso para usar Docker con tu proyecto Laravel.

---

## 📚 ¿Qué es Docker?

Docker es una plataforma que permite **empaquetar aplicaciones en contenedores** ligeros y portables. Cada contenedor incluye todo lo necesario para que la aplicación funcione: código, runtime, librerías y dependencias.

**Ventajas:**
- ✅ Mismo entorno en desarrollo y producción
- ✅ No necesitas instalar PHP, MySQL, Redis manualmente
- ✅ Fácil de limpiar y reiniciar
- ✅ Aislado del sistema operativo

---

## 🛠️ Requisitos Previos

1. **Docker Desktop** instalado y corriendo
   - Descarga: https://www.docker.com/products/docker-desktop
   - Verifica que esté corriendo (icono en la bandeja del sistema)

2. **WSL 2** activado (Docker Desktop lo usa internamente)
   - Docker Desktop lo instala automáticamente

3. **Git** instalado

---

## 🚀 Arrancar el Proyecto

### Paso 1: Verificar Docker
```powershell
docker --version
docker-compose --version
```

### Paso 2: Levantar los contenedores
```powershell
docker-compose up -d
```

**¿Qué hace este comando?**
- `up`: Construye y arranca los contenedores
- `-d`: Modo "detached" (corre en segundo plano)

**Contenedores que se crean:**
| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| `app` | 8000 | Aplicación Laravel |
| `postgres` | 5432 | Base de datos PostgreSQL + PostGIS |
| `redis` | 6379 | Cache y colas |

### Paso 3: Ejecutar migraciones
```powershell
docker-compose exec app php artisan migrate --force
```

### Paso 4: Acceder a la aplicación
Abre tu navegador en: **http://localhost:8000**

---

## 📋 Comandos Útiles

### Gestión de Contenedores

```powershell
# Ver estado de los contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app

# Detener todos los contenedores
docker-compose down

# Reiniciar un servicio
docker-compose restart app
```

### Comandos Laravel

```powershell
# Entrar al contenedor de la app
docker-compose exec app bash

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Ejecutar seeders
docker-compose exec app php artisan db:seed

# Ejecutar tests
docker-compose exec app php artisan test

# Limpiar caché
docker-compose exec app php artisan cache:clear

# Ver configuración
docker-compose exec app php artisan config:show
```

### Reconstruir Imágenes

```powershell
# Reconstruir después de cambiar el Dockerfile
docker-compose build

# Reconstruir desde cero (sin caché)
docker-compose build --no-cache

# Reconstruir y levantar
docker-compose up -d --build
```

---

## 🗂️ Estructura de Archivos Docker

```
garageos/
├── docker/
│   └── Dockerfile          # Imagen de PHP + Laravel
├── docker-compose.yml      # Orquestación de contenedores
├── .dockerignore           # Archivos ignorados por Docker
└── DOCKER.md              # Esta guía
```

---

## 🐛 Troubleshooting

### Problema 1: Puerto ocupado
**Error:** `Bind for 0.0.0.0:8000 failed: port is already allocated`

**Solución:** Cambia el puerto en `docker-compose.yml`:
```yaml
ports:
  - "8001:8000"  # Usa el 8001 en tu máquina
```

### Problema 2: Errores de permisos en storage
**Error:** `Permission denied` en archivos de storage

**Solución:**
```powershell
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

### Problema 3: Base de datos no conecta
**Error:** `SQLSTATE[42P01]: Undefined table`

**Solución:**
```powershell
# Espera a que PostgreSQL esté listo
docker-compose ps

# Ejecuta migraciones
docker-compose exec app php artisan migrate --force
```

### Problema 4: Cambios no se reflejan
**Solución:**
```powershell
# Reinicia el contenedor
docker-compose restart app

# Si no funciona, reconstruye
docker-compose up -d --build
```

### Problema 5: Error "composer install failed"
**Solución:**
```powershell
# Limpia caché de Composer
docker-compose exec app composer clear-cache

# Reinstala dependencias
docker-compose exec app composer install
```

### Problema 6: Ver logs detallados
```powershell
# Logs de un servicio específico
docker-compose logs app --tail 100

# Logs con timestamps
docker-compose logs -f -t app
```

---

## 🧹 Limpieza Completa

Si quieres empezar de cero:

```powershell
# Detener y eliminar contenedores + volúmenes
docker-compose down -v

# Eliminar imágenes
docker-compose down --rmi all

# Eliminar todo (contenedores, imágenes, volúmenes, redes)
docker system prune -a --volumes
```

---

## 🔄 Flujo de Trabajo Diario

### Al empezar a trabajar:
```powershell
# 1. Levantar contenedores (si están parados)
docker-compose up -d

# 2. Verificar que todo esté corriendo
docker-compose ps

# 3. Ejecutar migraciones nuevas (si hay)
docker-compose exec app php artisan migrate
```

### Durante el desarrollo:
```powershell
# Ver logs en tiempo real
docker-compose logs -f app

# Entrar al contenedor para debugging
docker-compose exec app bash

# Ejecutar tests
docker-compose exec app php artisan test
```

### Al terminar:
```powershell
# Detener contenedores (sin eliminar datos)
docker-compose down

# O dejar todo limpio
docker-compose down -v
```

---

## 📊 Monitoreo

### Ver uso de recursos
```powershell
# Ver uso de CPU/RAM de los contenedores
docker stats

# Ver procesos dentro de un contenedor
docker-compose exec app ps aux
```

### Conectar a la base de datos
```powershell
# Desde el host (usando psql)
docker-compose exec postgres psql -U garageos -d garageos

# Desde dentro del contenedor de la app
docker-compose exec app php artisan tinker
```

### Conectar a Redis
```powershell
# Usando redis-cli
docker-compose exec redis redis-cli

# Ver todas las claves
docker-compose exec redis redis-cli KEYS *
```

---

## 🎯 Comandos Rápidos (Cheat Sheet)

```powershell
# ARRANCAR
docker-compose up -d

# DETENER
docker-compose down

# VER ESTADO
docker-compose ps

# VER LOGS
docker-compose logs -f app

# ENTRAR AL CONTENEDOR
docker-compose exec app bash

# MIGRACIONES
docker-compose exec app php artisan migrate

# TESTS
docker-compose exec app php artisan test

# RECONSTRUIR
docker-compose build --no-cache

# LIMPIAR TODO
docker-compose down -v
```

---

## 🆘 Ayuda Adicional

- **Documentación oficial de Docker:** https://docs.docker.com
- **Docker Compose:** https://docs.docker.com/compose
- **Laravel + Docker:** https://laravel.com/docs/deployment#docker

---

**💡 Tip:** Si eres nuevo en Docker, empieza con los comandos básicos (`up`, `down`, `ps`, `logs`) y ve añadiendo más según los necesites.