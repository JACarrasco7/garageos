# GarageOS — Plan de Implementación

> **Stack:** Laravel 13 + Vue 3.5 + Inertia.js 3 + TypeScript 5.8 + Tailwind CSS 4 + **PostgreSQL 16 + PostGIS** + Redis 7 + PHP 8.5
> **Última actualización:** Julio 2026

## 🛠️ Entorno de Desarrollo
Ver `WSL-SETUP.md` para instrucciones (Docker o WSL).

## ✅ Implementado

### Backend
- [x] Laravel 13 + Breeze + Vue 3 + TypeScript + Dark mode
- [x] **PostgreSQL 16** (migrado desde MySQL — JSONB, full-text search nativo, mejor concurrencia)
- [x] Paquetes: Sanctum, Pulse, Scout, MediaLibrary, Permission, Backup, DomPDF, QR Code, Pest
- [x] Estructura modular en `app/Modules/`
- [x] Migrations: `users`, `garages`, `vehicles`, `vehicle_specs`, `documents`, `maintenance_entries`, `alert_rules`, `maintenance_intervals`, `valuations`, `listings`, `conversations`, `transactions`, `marketplace_*`
- [x] Factories: `VehicleFactory`, `VehicleSpecFactory`, `GarageFactory`, `ListingFactory`, factories de Alerts/Billing/Identity/Maintenance/Marketplace/Vehicle/VehicleImport
- [x] Seeders: `MaintenanceIntervalSeeder`
- [x] Modelos con relationships y casts
- [x] Resources: `VehicleResource`, `VehicleSpecResource`, `VehiclePhotoResource`
- [x] Controllers: `GarageController`, `VehicleController`, `DocumentController`, `PublicVehicleController`, `VehiclePhotoController`, `ListingController`
- [x] Eventos: `VehicleRegistered`, `AlertTriggered`, `ImportStepCompleted`, `TemporaryPlateExpiringSoon`
- [x] Listeners: `CreateDefaultAlertRules`, `EvaluateMaintenanceAlerts`
- [x] Jobs: `EvaluateAlertsJob`
- [x] Schedule: `alerts:evaluate`, `plates:check-expiry`, `alerts:evaluate-search`
- [x] **Full-text search en listings** (PostgreSQL `tsvector` con stemmer español)
- [x] Rutas API modularizadas

### Frontend
- [x] Páginas Vue: `Vehicle/Index.vue`, `Vehicle/Create.vue`, `Vehicle/Show.vue`, `Vehicle/Public.vue`, `Documents/Index.vue`, `Marketplace/MyListings.vue`
- [x] TypeScript interfaces en `resources/js/types/index.ts`
- [x] Tailwind CSS configurado

### Mobile
- [x] Capacitor 7 configurado (`capacitor.config.ts`)

## Próximos pasos

1. ~~`docker-compose up -d postgres`~~ → Usar MySQL en WSL (ya configurado)
2. `php artisan migrate` — ejecutar migrations
3. `npm run dev` — desarrollo frontend (usar PHP local de Windows)
4. ~~Implementar upload de documentos con Spatie MediaLibrary~~ → Ya implementado
5. ~~Sistema de notificaciones push (Firebase)~~ → Ya implementado
6. ~~Marketplace + PDF reports~~ → Ya implementado
7. ~~Geolocalización talleres con PostGIS~~ → Usar alternativa MySQL
8. ~~Particionado de tablas por fecha~~ → Futuro

## Comandos para arrancar (WSL)

```bash
# Arrancar servicios
wsl -d Ubuntu -u root service mysql start
wsl -d Ubuntu -u root service redis-server start

# Desde el proyecto
cd /mnt/c/laragon/www/app_garage
php artisan migrate
npm run dev
php artisan serve --host=0.0.0.0 --port=8000
```

## Comandos para arrancar (Windows)

```powershell
# Usar PHP local de Laragon
cd c:\laragon\www\app_garage
php artisan migrate
npm run dev
php artisan serve
```

## Estructura final

```
garageos/
├── app/Modules/
│   ├── Identity/
│   │   ├── Models/Garage.php
│   │   └── Providers/IdentityServiceProvider.php
│   ├── Vehicle/
│   │   ├── Models/Vehicle.php, VehicleSpec.php
│   │   ├── Events/VehicleRegistered.php
│   │   ├── Controllers/VehicleController.php, PublicVehicleController.php
│   │   ├── Resources/VehicleResource.php, VehicleSpecResource.php
│   │   └── Providers/VehicleServiceProvider.php
│   ├── Documents/
│   │   ├── Models/Document.php
│   │   ├── Controllers/DocumentController.php
│   │   └── Providers/DocumentServiceProvider.php
│   ├── Maintenance/
│   │   ├── Models/ServicePack.php
│   │   └── Actions/RecommendServicePackAction.php
│   ├── Marketplace/
│   │   ├── Models/MarketplaceListing.php, Transaction.php
│   │   ├── Controllers/MarketplaceController.php
│   │   └── Services/AffiliateService.php
│   ├── VehicleImport/
│   │   ├── Models/VehicleImport.php, TemporaryPlate.php
│   │   └── Jobs/CheckTemporaryPlateExpiryJob.php
│   └── Alerts/
│       ├── Models/AlertRule.php
│       ├── Events/AlertTriggered.php
│       ├── Listeners/CreateDefaultAlertRules.php
│       └── Jobs/EvaluateAlertsJob.php
├── database/
│   ├── migrations/ (15+ migrations)
│   ├── seeders/MaintenanceIntervalSeeder.php
│   └── factories/VehicleFactory.php, VehicleSpecFactory.php, GarageFactory.php, ListingFactory.php
├── resources/js/Pages/
│   ├── Vehicle/Index.vue, Create.vue, Show.vue, Public.vue
│   ├── Documents/Index.vue
│   └── Marketplace/MyListings.vue
├── routes/api.php
├── wsl-setup.sh
└── WSL-SETUP.md
```
