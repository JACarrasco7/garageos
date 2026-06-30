<h1 align="center">GarageOS</h1>

<p align="center">
<a href="https://github.com/garageos/garageos/actions"><img src="https://github.com/garageos/garageos/workflows/tests/badge.svg" alt="Build Status"></a>
<img src="https://img.shields.io/badge/tests-58%20written-brightgreen" alt="Tests">
<img src="https://img.shields.io/badge/php-8.4-blue" alt="PHP 8.4">
<img src="https://img.shields.io/badge/laravel-12.x-red" alt="Laravel 12">
<img src="https://img.shields.io/badge/tailwind-4.x-blue" alt="Tailwind 4">
</p>

## Stack

- **Laravel 12** + **Vue 3.5** + **Inertia.js 2** + **TypeScript 5.8**
- **Tailwind CSS 4** + **shadcn-vue** + **MySQL 8.4** + **Redis 7**
- **FrankenPHP + Octane** para producción
- **Capacitor 7** para móvil (iOS/Android)

## Desarrollo

```bash
composer install
npm install
npm run dev
php artisan serve
php artisan queue:listen
```

## Tests

```bash
php artisan test --parallel
# o con Pest
./vendor/bin/pest --parallel
```

## Producción

### Requisitos previos
- Configurar `.env` con:
  - `STRIPE_KEY` y `STRIPE_SECRET` (suscripciones)
  - `FIREBASE_PROJECT_ID` y `FIREBASE_CREDENTIALS` (push)
  - `SERVICES_COCHESNET_KEY` (scraper de mercado)

### Deploy
```bash
# 1. Migraciones
php artisan migrate --force

# 2. Storage link
php artisan storage:link

# 3. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Horizon
php artisan horizon:terminate

# 5. Docker
docker-compose up -d
```

### Mobile (Android)
```bash
npx cap add android
npx cap sync
npx cap open android
```

## Licencia

MIT

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
