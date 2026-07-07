# Setup Inicial

> Primeros pasos para configurar GarageOS en un entorno nuevo.

---

## 📋 Prerrequisitos

Antes de empezar, asegúrate de tener instalado:

- **Docker Desktop** (recomendado) - [Descargar](https://www.docker.com/products/docker-desktop)
- **Git** - [Descargar](https://git-scm.com/)
- **VS Code** (recomendado) - [Descargar](https://code.visualstudio.com/)

### Extensiones VS Code recomendadas
- Laravel Extra Intellisense
- PHP Intelephense
- Volar (Vue)
- Tailwind CSS IntelliSense
- Docker
- WSL (si usas WSL)

---

## 🚀 Opción A: Docker (recomendado)

### 1. Clonar el repositorio
```bash
git clone https://github.com/your-user/garageos.git
cd garageos
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
```

### 3. Levantar contenedores
```bash
docker-compose up -d
```

### 4. Instalar dependencias y migrar
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
```

### 5. Acceder
Abre [http://localhost:8000](http://localhost:8000)

---

## 🐧 Opción B: WSL + PHP local

### 1. Instalar WSL
```powershell
wsl --install
```

### 2. Instalar PHP, MySQL, Redis en WSL
```bash
wsl -d Ubuntu -u root
apt update
apt install -y php8.4-cli php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-curl php8.4-zip php8.4-mysql php8.4-sqlite3 php8.4-opcache php8.4-gd php8.4-intl php8.4-fpm php8.4-redis
apt install -y mysql-server redis-server
apt install -y nodejs npm
```

### 3. Instalar Composer en WSL
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --quiet
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### 4. Crear base de datos
```bash
service mysql start
mysql -u root -e "CREATE DATABASE garageos;"
mysql -u root -e "CREATE USER 'garageos'@'localhost' IDENTIFIED BY 'GarageOS2026Dev!';"
mysql -u root -e "GRANT ALL ON garageos.* TO 'garageos'@'localhost';"
```

### 5. Clonar e instalar
```bash
cd /mnt/c/laragon/www
git clone https://github.com/your-user/garageos.git
cd garageos
composer install
npm install
```

### 6. Configurar .env
```bash
cp .env.example .env
php artisan key:generate
```

### 7. Levantar servicios
```powershell
wsl -d Ubuntu -u root service mysql start
wsl -d Ubuntu -u root service redis-server start
```

### 8. Migrar y servir
```bash
php artisan migrate
php artisan serve
```

Accede a [http://localhost:8000](http://localhost:8000)

---

## 🔍 Verificación

Una vez instalado, ejecuta:

```bash
# Con Docker
docker-compose exec app php artisan about

# Sin Docker
php artisan about
```

Deberías ver información del entorno (PHP 8.4, Laravel 13, entorno local).

---

## 🛠️ Troubleshooting

### Error: "Class 'PDO' not found"
Instala la extensión:
```bash
wsl -d Ubuntu -u root apt install -y php8.4-mysql
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"
Verifica que MySQL esté corriendo:
```bash
wsl -d Ubuntu -u root service mysql status
```

### Error: "Redis connection refused"
```bash
wsl -d Ubuntu -u root service redis-server start
```

### Error: "Permission denied" en storage
```bash
chmod -R 777 storage bootstrap/cache
```

### Ver logs de Laravel
```bash
tail -f storage/logs/laravel.log
```

### Ver logs de Docker
```bash
docker-compose logs -f app
```

---

## 📚 Siguientes pasos

- [Setup Docker](DOCKER.md)
- [Setup WSL](WSL-SETUP.md)
- [Setup PostgreSQL](POSTGRESQL.md)
- [Convenciones de desarrollo](../development/CONVENTIONS.md)
