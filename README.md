<h1 align="center">GarageOS</h1>

<p align="center">
<a href="https://github.com/garageos/garageos/actions"><img src="https://github.com/garageos/garageos/workflows/tests/badge.svg" alt="Build Status"></a>
<img src="https://img.shields.io/badge/tests-50%20passing-brightgreen" alt="Tests">
<img src="https://img.shields.io/badge/php-8.4-blue" alt="PHP 8.4">
<img src="https://img.shields.io/badge/laravel-12.x-red" alt="Laravel 12">
</p>

## Stack

- **Laravel 12** + **Vue 3.5** + **Inertia.js 2** + **TypeScript 5.8**
- **Tailwind CSS 4** + **MySQL 8.4** + **Redis 7**
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
```

## Producción

```bash
docker-compose up -d
```

## Licencia

MIT

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
