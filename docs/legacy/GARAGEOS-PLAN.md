# GarageOS — Plan de Implementación

> **Stack:** Laravel 13 + Vue 3.5 + Inertia.js 3 + TypeScript 5.8 + Tailwind CSS 4 + MySQL 8.4 + Redis 7 + PHP 8.5
> **Última actualización:** 4 de Julio de 2026

---

## Tabla de contenidos

**PARTE I — GarageOS Personal (plan original)**
1. [Stack definitivo](#stack-definitivo)
2. [Estructura del proyecto](#estructura-del-proyecto)
3. [Base de datos — Schema completo](#base-de-datos--schema-completo)
4. [Event Bus — Eventos del sistema](#event-bus--eventos-del-sistema)
5. [Paquetes y dependencias](#paquetes-y-dependencias)
6. [Rendimiento y optimizaciones](#rendimiento-y-optimizaciones)
7. [Configuración de entorno](#configuración-de-entorno)
8. [Roadmap de implementación](#roadmap-de-implementación)
9. [Testing strategy](#testing-strategy)
10. [Primeros comandos](#primeros-comandos)
11. [MVP mínimo viable — mañana mismo](#mvp-mínimo-viable--mañana-mismo)
12. [DevOps & CI/CD](#devops--cicd)
13. [Seguridad & Monitoreo](#seguridad--monitoreo)
14. [Internacionalización & UX](#internacionalización--ux)
15. [Documentación & API](#documentación--api)
16. [Backup & Rollback](#backup--rollback)

---

> **PARTE II** → Ver [`GARAGEOS-PLAN-V2.md`](./GARAGEOS-PLAN-V2.md) — Marketplace de Importación + Servicios
19. [Fase 3 — Providers + marketplace de servicios](#fase-3--providers--marketplace-de-servicios)
20. [Fase 4 — Transactions + messaging](#fase-4--transactions--messaging)
21. [Fase 5 — Import Wizard (matriculación España)](#fase-5--import-wizard-matriculación-españa)
22. [Fase 6 — Pagos con Stripe Connect](#fase-6--pagos-con-stripe-connect)
23. [Fase 7 — Mobile + push](#fase-7--mobile--push)
24. [Roadmap v2 resumen](#roadmap-v2-resumen)
25. [Decisiones tomadas](#decisiones-tomadas)
26. [Riesgos identificados](#riesgos-identificados)
27. [Próximos pasos inmediatos](#próximos-pasos-inmediatos)

---

## Stack definitivo

| Capa | Tecnología | Versión | Propósito |
|---|---|---|---|
| Runtime | PHP | 8.5 | Fibers, JIT mejorado, deprecated warnings limpios |
| Backend | Laravel | 13.x | Monolito modular, ORM, colas, eventos, schedule |
| Frontend | Vue | 3.5 | Composition API, Vapor mode, mejor TypeScript |
| Bridge | Inertia.js | 3.x | SPA sin API boilerplate, SSR nativo, TypeScript mejorado |
| Tipado | TypeScript | 5.8 | Tipado estricto, erasableSyntaxOnly |
| Estilos | Tailwind CSS | 4.x | Oxide engine (Rust), 5x más rápido, CSS-first config |
| BD | MySQL | 8.4 | JSON mejorado, InnoDB optimizado |
| Cache / Queues | Redis | 7.x + Laravel Queues | Colas de alertas, OCR, notificaciones |
| Mobile | Capacitor | 7.x | iOS + Android nativo |
| Storage | Laravel Storage + S3 | — | Documentos, fotos, PDFs |
| Auth | Laravel Sanctum | 4.x | API tokens + SPA auth |
| Scaffolding | Laravel Breeze | 2.x | Auth + Inertia + Vue + TS out-of-box |
| Notificaciones | Firebase Cloud Messaging | — | Push nativo en mobile |
| OCR | Tesseract 5 / AWS Textract | — | Parseo de facturas |
| PDF | DomPDF 3 / Browsershot | — | Informes de venta |
| Búsqueda | Laravel Scout + Meilisearch | — | Full-text search |
| Monitorización | Laravel Pulse | — | Métricas en tiempo real |
| Colas UI | Laravel Horizon | — | Dashboard de colas Redis |
| Server | FrankenPHP + Octane | — | Keep-alive, workers persistentes |

> **Por qué monolito modular primero:** Misma lógica que microservicios, sin el coste operacional de orquestar 6 servicios antes de tener usuarios. Cuando escale, cada módulo se extrae como servicio independiente sin reescribir.

### Por qué estas versiones específicas

- **PHP 8.5**: JIT mejorado, Fibers para async nativo
- **Laravel 13**: Mejor rendimiento, Inertia 3 integrado, `php artisan config:cache` más rápido
- **Vue 3.5**: Vapor mode (mejor rendimiento), mejor soporte TypeScript, `defineModel` simplificado, Suspense nativo
- **Inertia.js 3**: SSR nativo mejorado, partial reloads optimizados, TypeScript más estricto
- **Tailwind CSS 4**: Motor Oxide escrito en Rust (5x más rápido), configuración CSS-first (sin `tailwind.config.js`), container queries nativas, `@theme` directive
- **Capacitor 7**: Mejor rendimiento en iOS/Android, plugins actualizados, soporte para Swift 6 / Kotlin 2
- **TypeScript 5.8**: `erasableSyntaxOnly`, mejor inferencia, decorators estandarizados
- **MySQL 8.4**: Mejoras en JSON, InnoDB paralelo, mejor optimizer

---

## Estructura del proyecto

```
garageos/
├── app/
│   ├── Enums/                          # Enums globales (FuelType, DocumentType, etc.)
│   ├── Traits/                         # Traits compartidos (HasUuid, HasMedia, etc.)
│   └── Modules/
│       ├── Identity/
│       │   ├── Models/                 User, Garage
│       │   ├── Actions/                CreateUserAction, GenerateQRAction
│       │   ├── Http/
│       │   │   ├── Controllers/        AuthController, GarageController
│       │   │   ├── Requests/           LoginRequest, RegisterRequest
│       │   │   └── Resources/          UserResource, GarageResource
│       │   ├── Events/                 UserRegistered
│       │   ├── Providers/              IdentityServiceProvider
│       │   └── Tests/
│       │
│       ├── Vehicle/
│       │   ├── Models/                 Vehicle, VehicleSpec, KmHistory
│       │   ├── Actions/                RegisterVehicleAction, UpdateKmAction
│       │   ├── Http/
│       │   │   ├── Controllers/        VehicleController, QrController
│       │   │   ├── Requests/           StoreVehicleRequest
│       │   │   └── Resources/          VehicleResource
│       │   ├── Events/                 VehicleRegistered, KmUpdated
│       │   ├── Providers/              VehicleServiceProvider
│       │   └── Tests/
│       │
│       ├── Documents/
│       │   ├── Models/                 Document
│       │   ├── Actions/                UploadDocumentAction, ParseDocumentAction
│       │   ├── Http/
│       │   │   ├── Controllers/        DocumentController
│       │   │   ├── Requests/           UploadDocumentRequest
│       │   │   └── Resources/          DocumentResource
│       │   ├── Events/                 DocumentUploaded, DocumentProcessed
│       │   ├── Jobs/                   ParseDocumentJob
│       │   ├── Providers/              DocumentServiceProvider
│       │   └── Tests/
│       │
│       ├── Maintenance/
│       │   ├── Models/                 MaintenanceEntry, Workshop, MaintenanceInterval, ServicePack
│       │   ├── Actions/                CreateEntryAction, BuildServicePackAction
│       │   ├── Http/
│       │   │   ├── Controllers/        MaintenanceController, WorkshopController
│       │   │   ├── Requests/           StoreMaintenanceRequest
│       │   │   └── Resources/          MaintenanceResource
│       │   ├── Events/                 RevisionCompleted
│       │   ├── Providers/              MaintenanceServiceProvider
│       │   └── Tests/
│       │
│       ├── Alerts/
│       │   ├── Models/                 AlertRule, Notification
│       │   ├── Actions/                EvaluateAlertsAction
│       │   ├── Listeners/              (escucha eventos de otros módulos)
│       │   ├── Jobs/                   SendAlertJob, SendPushJob
│       │   ├── Console/                EvaluateAlertsCommand (kernel schedule)
│       │   ├── Providers/              AlertServiceProvider
│       │   └── Tests/
│       │
│       └── Marketplace/
│           ├── Models/                 SaleReport, MarketValue
│           ├── Actions/                GenerateCertificateAction, CalculateScoreAction
│           ├── Http/
│           │   ├── Controllers/        SaleReportController, PublicReportController
│           │   ├── Requests/           GenerateReportRequest
│           │   └── Resources/          SaleReportResource
│           ├── Events/                 ReportGenerated
│           ├── Providers/              MarketplaceServiceProvider
│           └── Tests/
│
├── bootstrap/
│   ├── app.php
│   └── providers.php                   # Laravel 12: providers centralizados
│
├── config/
│   ├── modules.php                     # Configuración de módulos
│   └── ...
│
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_users_table.php
│   │   ├── xxxx_create_garages_table.php
│   │   ├── xxxx_create_vehicles_table.php
│   │   ├── xxxx_create_vehicle_specs_table.php
│   │   ├── xxxx_create_km_history_table.php
│   │   ├── xxxx_create_documents_table.php
│   │   ├── xxxx_create_workshops_table.php
│   │   ├── xxxx_create_maintenance_entries_table.php
│   │   ├── xxxx_create_maintenance_intervals_table.php
│   │   ├── xxxx_create_service_packs_table.php
│   │   ├── xxxx_create_alert_rules_table.php
│   │   ├── xxxx_create_notifications_table.php
│   │   ├── xxxx_create_sale_reports_table.php
│   │   └── xxxx_create_market_values_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── MaintenanceIntervalSeeder.php
│   │   └── ServicePackSeeder.php
│   └── factories/
│       └── (por módulo)
│
├── routes/
│   ├── web.php                         # Rutas Inertia principales
│   ├── api.php                         # API pública para QR/informes
│   └── modules/
│       ├── identity.php
│       ├── vehicle.php
│       ├── documents.php
│       ├── maintenance.php
│       ├── alerts.php
│       └── marketplace.php
│
├── resources/
│   ├── css/
│   │   └── app.css                     # Tailwind 4: @import "tailwindcss"
│   └── js/
│       ├── Components/
│       │   ├── ui/                     # Button, Input, Modal, Select, Badge
│       │   ├── forms/                  # FormGroup, DatePicker, FileUpload
│       │   └── layout/                 # Sidebar, Navbar, Footer
│       ├── Composables/                # useNotifications, useAlerts, useVehicle
│       ├── Layouts/
│       │   ├── AppLayout.vue
│       │   └── PublicLayout.vue
│       ├── Pages/
│       │   ├── Dashboard/
│       │   │   ├── Index.vue
│       │   │   └── Stats.vue
│       │   ├── Vehicle/
│       │   │   ├── Index.vue
│       │   │   ├── Show.vue
│       │   │   ├── Create.vue
│       │   │   └── Public.vue
│       │   ├── Maintenance/
│       │   │   ├── Index.vue
│       │   │   └── Create.vue
│       │   ├── Documents/
│       │   │   ├── Index.vue
│       │   │   ├── Show.vue
│       │   │   └── Upload.vue
│       │   └── Marketplace/
│       │       ├── Report.vue
│       │       └── PublicReport.vue
│       ├── Types/
│       │   ├── vehicle.ts
│       │   ├── document.ts
│       │   ├── maintenance.ts
│       │   ├── alert.ts
│       │   └── index.ts
│       └── app.ts
│
├── tests/
│   ├── Pest.php
│   ├── Unit/
│   │   └── Modules/
│   ├── Feature/
│   │   └── Modules/
│   └── Browser/
│
├── public/
│   ├── index.php
│   └── build/
│
├── capacitor.config.ts
├── tsconfig.json
├── vite.config.ts
├── postcss.config.js
├── package.json
├── composer.json
└── .env
```

---

## Base de datos — Schema completo

### Módulo: Identity

```sql
CREATE TABLE users (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('owner', 'workshop', 'admin') DEFAULT 'owner',
    avatar      VARCHAR(255) NULL,
    phone       VARCHAR(20) NULL,
    email_verified_at TIMESTAMP NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role)
);

CREATE TABLE garages (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(100) DEFAULT 'Mi garaje',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Módulo: Vehicle

```sql
CREATE TABLE vehicles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    garage_id       BIGINT UNSIGNED NOT NULL,
    plate           VARCHAR(10) NOT NULL,
    vin             VARCHAR(17) NULL UNIQUE,
    qr_token        VARCHAR(64) NOT NULL UNIQUE,
    brand           VARCHAR(50) NOT NULL,
    model           VARCHAR(80) NOT NULL,
    year            YEAR NOT NULL,
    fuel_type       ENUM('gasolina','diesel','hibrido','electrico','glp') NOT NULL,
    color           VARCHAR(40) NULL,
    current_km      INT UNSIGNED DEFAULT 0,
    purchase_date   DATE NULL,
    purchase_price  DECIMAL(10,2) NULL,
    photo           VARCHAR(255) NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE,
    INDEX idx_plate (plate),
    INDEX idx_vin (vin),
    INDEX idx_qr (qr_token),
    INDEX idx_garage_active (garage_id, is_active)
);

CREATE TABLE vehicle_specs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id      BIGINT UNSIGNED NOT NULL UNIQUE,
    engine_cc       INT NULL,
    power_hp        INT NULL,
    torque_nm       INT NULL,
    transmission    ENUM('manual','automatico','cvt') NULL,
    drive           ENUM('fwd','rwd','4wd','awd') NULL,
    doors           TINYINT NULL,
    seats           TINYINT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

CREATE TABLE km_history (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id  BIGINT UNSIGNED NOT NULL,
    km          INT UNSIGNED NOT NULL,
    recorded_at DATE NOT NULL,
    source      ENUM('manual','document','obd') DEFAULT 'manual',
    notes       VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    INDEX idx_vehicle_date (vehicle_id, recorded_at)
);
```

### Módulo: Documents

```sql
CREATE TABLE documents (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id      BIGINT UNSIGNED NOT NULL,
    type            ENUM('factura','itv','seguro','impuesto','otro') NOT NULL,
    title           VARCHAR(150) NULL,
    file_path       VARCHAR(255) NOT NULL,
    file_size       INT UNSIGNED NULL,
    mime_type       VARCHAR(50) NULL,
    km_at_time      INT UNSIGNED NULL,
    document_date   DATE NULL,
    expiry_date     DATE NULL,
    amount          DECIMAL(10,2) NULL,
    parsed_data     JSON NULL,
    is_verified     BOOLEAN DEFAULT FALSE,
    verified_by     BIGINT UNSIGNED NULL,
    verified_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_vehicle_type (vehicle_id, type),
    INDEX idx_expiry (expiry_date)
);
```

### Módulo: Maintenance

```sql
CREATE TABLE workshops (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,
    address     VARCHAR(200) NULL,
    city        VARCHAR(100) NULL,
    lat         DECIMAL(10,7) NULL,
    lng         DECIMAL(10,7) NULL,
    phone       VARCHAR(20) NULL,
    email       VARCHAR(150) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE maintenance_entries (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id      BIGINT UNSIGNED NOT NULL,
    document_id     BIGINT UNSIGNED NULL,
    workshop_id     BIGINT UNSIGNED NULL,
    type            ENUM(
                        'aceite','filtros','neumaticos','frenos',
                        'distribucion','embrague','bateria','itv',
                        'revision_general','otro'
                    ) NOT NULL,
    title           VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    km_at_service   INT UNSIGNED NOT NULL,
    service_date    DATE NOT NULL,
    cost            DECIMAL(10,2) NULL,
    is_verified     BOOLEAN DEFAULT FALSE,
    notes           TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE SET NULL,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    INDEX idx_vehicle_date (vehicle_id, service_date),
    INDEX idx_vehicle_type (vehicle_id, type)
);

CREATE TABLE maintenance_intervals (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand           VARCHAR(50) NULL,
    model           VARCHAR(80) NULL,
    type            VARCHAR(50) NOT NULL,
    interval_km     INT UNSIGNED NULL,
    interval_months TINYINT UNSIGNED NULL,
    description     VARCHAR(200) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE service_packs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    maintenance_type VARCHAR(50) NOT NULL,
    name            VARCHAR(100) NOT NULL,
    items           JSON NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Módulo: Alerts

```sql
CREATE TABLE alert_rules (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id      BIGINT UNSIGNED NOT NULL,
    type            ENUM(
                        'itv','seguro','aceite','neumaticos',
                        'revision','impuesto','bateria','custom'
                    ) NOT NULL,
    trigger_km      INT UNSIGNED NULL,
    trigger_date    DATE NULL,
    advance_days    TINYINT DEFAULT 30,
    advance_km      INT DEFAULT 1000,
    is_active       BOOLEAN DEFAULT TRUE,
    last_triggered  TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    INDEX idx_vehicle_active (vehicle_id, is_active)
);

CREATE TABLE notifications (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    vehicle_id  BIGINT UNSIGNED NULL,
    type        VARCHAR(50) NOT NULL,
    title       VARCHAR(150) NOT NULL,
    body        TEXT NOT NULL,
    channel     ENUM('push','email','in_app') DEFAULT 'in_app',
    data        JSON NULL,
    read_at     TIMESTAMP NULL,
    sent_at     TIMESTAMP NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at),
    INDEX idx_created (created_at)
);
```

### Módulo: Marketplace

```sql
CREATE TABLE sale_reports (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id      BIGINT UNSIGNED NOT NULL,
    token           VARCHAR(64) NOT NULL UNIQUE,
    score           TINYINT UNSIGNED NULL,
    pdf_path        VARCHAR(255) NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    expires_at      TIMESTAMP NULL,
    generated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    views           INT UNSIGNED DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

CREATE TABLE market_values (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id      BIGINT UNSIGNED NOT NULL,
    estimated_value DECIMAL(10,2) NOT NULL,
    min_value       DECIMAL(10,2) NULL,
    max_value       DECIMAL(10,2) NULL,
    source          VARCHAR(50) DEFAULT 'scraper',
    recorded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    INDEX idx_vehicle_date (vehicle_id, recorded_at)
);
```

---

## Event Bus — Eventos del sistema

Con **Laravel Events + Listeners** (colas con Redis cuando toque), sin RabbitMQ hasta que escale:

```
Vehicle
  VehicleRegistered    → Listener: CreateDefaultAlertRules (sync)
  KmUpdated            → Listener: EvaluateMaintenanceAlerts (queued)
  VehiclePhotoUploaded → Listener: ProcessVehiclePhoto (queued)

Documents
  DocumentUploaded     → Listener: ParseDocumentWithOCR (queued)
  DocumentProcessed    → Listener: CreateMaintenanceEntry (si es factura, sync)
                         Listener: CreateAlertRule (si es ITV/seguro, sync)

Maintenance
  RevisionCompleted    → Listener: UpdateVehicleScore (queued)
                         Listener: RecalculateAlerts (queued)

Alerts
  AlertTriggered       → Listener: SendNotificationJob (queued: push + email)
```

---

## Paquetes y dependencias

## Paquetes y dependencias

### PHP / Composer — Producción

| Paquete | Versión | Razón |
|---|---|---|
| `laravel/framework` | ^13.0 | Framework base |
| `inertiajs/inertia-laravel` | ^3.0 | Inertia SSR/SPA |
| `laravel/sanctum` | ^4.0 | API tokens + SPA auth |
| `laravel/breeze` | ^2.4 | Scaffolding auth + Inertia + Vue + TS |
| `laravel/tinker` | ^3.0 | Consola interactiva |
| `laravel/horizon` | ^5.30 | Dashboard colas Redis |
| `laravel/pulse` | ^1.7 | Monitorización en tiempo real |
| `laravel/scout` | ^10.8 | Búsqueda full-text |
| `laravel/cashier` | ^15.6 | Stripe suscripciones (fase 3) |
| `chillerlan/php-qrcode` | ^5.0 | QR por vehículo |
| `barryvdh/laravel-dompdf` | ^3.1 | PDF informes de venta |
| `spatie/laravel-medialibrary` | ^11.12 | Gestión de fotos/documentos con S3 |
| `spatie/laravel-permission` | ^6.15 | Roles y permisos |
| `spatie/laravel-activitylog` | ^4.11 | Auditoría de cambios |
| `spatie/laravel-backup` | ^9.3 | Backups automáticos |
| `spatie/laravel-sluggable` | ^3.7 | Slugs para URLs públicas |
| `maatwebsite/laravel-excel` | ^3.1 | Import/export CSV |
| `mews/purifier` | ^3.4 | Sanitizar HTML |
| `predis/predis` | ^2.3 | Cliente Redis |
| `pestphp/pest` | ^3.0 | Test runner moderno |
| `spatie/laravel-scribe` | ^4.0 | Generación de documentación API (Swagger) |

### PHP / Composer — Desarrollo

| Paquete | Versión | Razón |
|---|---|---|
| `barryvdh/laravel-debugbar` | ^3.14 | Debugbar queries + memoria |
| `barryvdh/laravel-ide-helper` | ^3.5 | Autocompletado |
| `laravel/pint` | ^1.21 | Formateo PSR-12 |
| `larastan/larastan` | ^3.0 | Static analysis nivel 9 |
| `fakerphp/faker` | ^1.24 | Factories + seeders |
| `spatie/laravel-ignition` | ^2.9 | Páginas de error |
| `nunomaduro/collision` | ^8.5 | Error output bonito en CLI |
| `pestphp/pest-plugin-laravel` | ^3.0 | Pest + Laravel integration |
| `github/gh` | — | CLI para GitHub Actions/Workflows |

### NPM — Frontend

| Paquete | Versión | Razón |
|---|---|---|
| `vue` | ^3.5 | Composition API, Vapor mode |
| `@inertiajs/vue3` | ^3.0 | Inertia adapter |
| `@vitejs/plugin-vue` | ^6.0 | Vite + Vue |
| `vite` | ^6.3 | Build tool (Laravel 13 usa Vite 6) |
| `typescript` | ^5.8 | Tipado estricto, erasableSyntaxOnly |
| `tailwindcss` | ^4.1 | Oxide engine (Rust), 5x más rápido |
| `@tailwindcss/forms` | ^0.5 | Reset de formularios |
| `@tailwindcss/typography` | ^0.5 | Prose styles |
| `@headlessui/vue` | ^1.7 | Componentes accesibles |
| `@heroicons/vue` | ^2.2 | Iconos |
| `recharts` | ^2.15 | Gráficas de gastos |
| `dayjs` | ^1.11 | Fechas ligeras |
| `axios` | ^1.8 | HTTP client |
| `pinia` | ^3.0 | State management |
| `@vueuse/core` | ^14.0 | Composables utilitarios |
| `vue-sonner` | ^2.0 | Toast notifications |
| `i18next` | ^23.0 | Internacionalización |
| `vue-i18n` | ^10.0 | i18n para Vue |
| `sentry` | — | Monitoreo de errores frontend |

### NPM — Desarrollo

| Paquete | Versión | Razón |
|---|---|---|
| `laravel-vite-plugin` | ^3.1 | Integración Laravel + Vite |
| `autoprefixer` | ^10.5 | CSS vendor prefixes |
| `postcss` | ^8.5 | Procesador CSS |
| `prettier` | ^3.5 | Formateo |
| `eslint` | ^9.25 | Linter flat config |
| `@typescript-eslint/parser` | ^8.30 | TS parser para ESLint |
| `@typescript-eslint/eslint-plugin` | ^8.30 | Reglas TS |
| `vue-tsc` | ^2.2 | Type-checking Vue SFC |
| `playwright` | — | E2E Testing |

### Capacitor — Mobile

| Paquete | Versión | Razón |
|---|---|---|
| `@capacitor/core` | ^7.0 | Runtime |
| `@capacitor/cli` | ^7.0 | CLI |
| `@capacitor/android` | ^7.0 | Android target |
| `@capacitor/ios` | ^7.0 | iOS target |
| `@capacitor/push-notifications` | ^7.0 | Push nativas |
| `@capacitor/filesystem` | ^7.0 | Descarga de PDFs |
| `@capacitor/share` | ^7.0 | Compartir informe |
| `@capacitor/splash-screen` | ^7.0 | Splash screen nativa |
| `@capacitor/status-bar` | ^7.0 | Status bar nativa |
| `@capacitor-community/fcm` | ^7.0 | Firebase Cloud Messaging |

---

## Rendimiento y optimizaciones

### Backend

| Optimización | Cómo | Impacto |
|---|---|---|
| **OPcache + JIT** | `opcache.enable=1`, `opcache.jit=1255` en `php.ini` | 30-50% más rápido en cálculos |
| **Route caching** | `php artisan route:cache` en producción | Elimina parsing de rutas |
| **Config caching** | `php artisan config:cache` en producción | Elimina lectura de archivos config |
| **View caching** | `php artisan view:cache` en producción | Pre-compila Blade |
| **Event caching** | `php artisan event:cache` en producción | Registra listeners una vez |
| **Query caching** | `Cache::remember()` para maintenance_intervals, alert_rules | Evita queries repetitivas |
| **Eager loading** | `with(['garage', 'specs', 'documents'])` siempre | Evita N+1 |
| **Lazy collections** | `cursor()` y `lazy()` para datasets grandes | Memoria constante |
| **Redis cache** | `CACHE_DRIVER=redis` | Cache más rápido que file/database |
| **Queue workers** | Horizon con supervisores dedicados por prioridad | Jobs pesados (OCR, PDF) no bloquean |
| **Database indexing** | Índices compuestos en queries frecuentes | Queries 10-100x más rápidas |
| **Connection pooling** | PhpRedis con persistent connections | Menos overhead de conexión |
| **Octane + FrankenPHP** | `php artisan octane:start --server=frankenphp` | Keep-alive, workers persistentes, HTTP/2 |

### Frontend

| Optimización | Cómo | Impacto |
|---|---|---|
| **Code splitting** | Lazy loading por ruta en Vue Router | Bundle inicial 60% más pequeño |
| **Tree shaking** | Vite lo hace automático | Elimina código no usado |
| **Image optimization** | Spatie MediaLibrary con conversiones WebP/AVIF | 70% menos peso en fotos |
| **Prefetch Inertia** | `<Link prefetch>` para páginas frecuentes | Navegación instantánea |
| **Pinia stores** | Estado centralizado, sin prop drilling | Menos re-renders |
| **Virtual scrolling** | Para listas largas (timeline, documentos) | Render solo lo visible |
| **Debounced search** | `useDebounce` de VueUse en búsquedas | Menos requests |
| **Service Worker** | PWA con Workbox (fase 2) | Offline, carga instantánea |

### Base de datos

| Optimización | Cómo | Impacto |
|---|---|---|
| **Índices compuestos** | `(vehicle_id, service_date)`, `(vehicle_id, type)` | Queries de timeline instantáneas |
| **Partitioning** | Por año en `km_history`, `maintenance_entries` | Escalabilidad a millones de filas |
| **JSON columns** | `parsed_data`, `data` en notifications | Flexibilidad sin schema changes |
| **Read replicas** | MySQL replica para reportes/estadísticas | No impacta escritura |
| **Connection pooling** | ProxySQL en producción | Maneja más conexiones |

### Infraestructura recomendada (producción)

```
                    ┌─────────────┐
                    │  CloudFlare  │  (CDN + WAF + DNS)
                    └──────┬──────┘
                           │
                    ┌──────┴──────┐
                    │   Nginx     │  (Reverse proxy + SSL)
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
        ┌─────┴─────┐ ┌───┴───┐ ┌─────┴─────┐
        │  Laravel   │ │ Redis │ │  MySQL    │
        │  (Octane)  │ │   7   │ │  8.4      │
        └─────┬─────┘ └───┬───┘ └─────┬─────┘
              │            │            │
        ┌─────┴─────┐      │      ┌─────┴─────┐
        │  Horizon   │      │      │  Replica  │
        │  (queues)  │      │      │  (read)   │
        └────────────┘      │      └───────────┘
                      ┌─────┴─────┐
                      │  Meili    │
                      │  Search   │
                      └───────────┘
```

---

## Configuración de entorno

### .env

```env
APP_NAME=GarageOS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://garageos.app

# PHP 8.4 + OPcache JIT
PHP_OPCACHE_ENABLE=1
PHP_OPCACHE_JIT=1255

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=garageos
DB_USERNAME=garageos
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache + Sesiones + Colas
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail (Mailpit en dev, SES/Postmark en prod)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@garageos.app"
MAIL_FROM_NAME="${APP_NAME}"

# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=garageos-prod
AWS_USE_PATH_STYLE_ENDPOINT=false

# Búsqueda
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=

# Stripe (fase 3)
CASHIER_CURRENCY=eur
CASHIER_CURRENCY_LOCALE=es
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Firebase (push notifications)
FIREBASE_PROJECT_ID=
FIREBASE_CREDENTIALS=

# Horizon
HORIZON_PREFIX=horizon

# Pulse
PULSE_ENABLED=true
```

### php.ini optimizado para producción

```ini
; OPcache
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=32
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1

; JIT (PHP 8.4)
opcache.jit=1255
opcache.jit_buffer_size=256M

; Realpath cache
realpath_cache_size=4096K
realpath_cache_ttl=600

; Uploads (documentos)
upload_max_filesize=20M
post_max_size=25M
max_execution_time=120
memory_limit=512M
```

---

## Roadmap de implementación

### Fase 0 — Setup (2-3 días)

- [x] `laravel new garageos --using=laravel-presets/inertia` (Inertia 3 nativo en Laravel 13)
- [x] Instalar paquetes Composer + NPM
- [x] Configurar estructura modular en `app/Modules/`
- [x] Configurar MySQL + Redis en `.env`
- [x] Breeze + Inertia + Vue + TypeScript (scaffolding auth)
- [x] Setup inicial de Capacitor 7
- [x] Configurar Pint, Larastan, IDE Helper
- [x] Configurar Pest como test runner
- [x] Commit inicial: `git add -A && git commit -m "init: garageos scaffold"`

### Fase 1 — Core MVP (3-4 semanas)

**Semana 1: Identity + Vehicle**

- [ ] Migrations: `users`, `garages`, `vehicles`, `vehicle_specs`, `km_history`
- [ ] Service Providers por módulo
- [ ] Models con relationships, casts, factories, Enums PHP 8.4
- [ ] CRUD de vehículos (placa, VIN, marca, modelo, año)
- [ ] Generación de QR único por vehículo
- [ ] Dashboard con listado del garaje virtual
- [ ] Vista pública por QR (sin login)
- [ ] Tests: Pest Unit + Feature de Vehicle

**Semana 2: Documents**

- [ ] Migration de `documents`
- [ ] Upload de ficheros con Spatie MediaLibrary (S3 o local)
- [ ] Visor de documentos en frontend (PDF/image preview)
- [ ] Alertas básicas de caducidad (ITV, seguro) — rules manuales
- [ ] Listado de docs por vehículo con filtro por tipo

**Semana 3: Maintenance**

- [ ] Migrations: `workshops`, `maintenance_entries`, `maintenance_intervals`, `service_packs`
- [ ] Timeline de vida del coche (ordenado por fecha/km desc)
- [ ] Formulario para añadir entrada manual
- [ ] Cálculo de próximo mantenimiento basado en `maintenance_intervals`
- [ ] Sello "Verificado" cuando lo añade un taller

**Semana 4: Alerts + Notificaciones**

- [ ] Migrations: `alert_rules`, `notifications`
- [ ] Job programado: `EvaluateAlertsCommand` en `php artisan schedule:run`
- [ ] Notificaciones in-app (badge en navbar)
- [ ] Email de recordatorio con Laravel Mail
- [ ] Push notifications con Capacitor + Firebase

### Fase 2 — Features de valor (3-4 semanas)

**Semana 5-6: Marketplace**

- [ ] Migrations: `sale_reports`, `market_values`
- [ ] Generación de informe PDF con DomPDF
- [ ] Score de salud del vehículo (algoritmo basado en historial)
- [ ] Link público temporal con token (expira en 30 días)
- [ ] Vista pública del informe (sin login, para compradores)

**Semana 7-8: Stats + UX avanzada**

- [ ] Calculadora de coste por km (suma de facturas / km recorridos)
- [ ] Gráfica de gastos mensual (Recharts)
- [ ] Historial de valor de mercado (scraper API Autocasión / coches.net)
- [ ] OCR básico para facturas (Tesseract via shell o AWS Textract)
- [ ] Notificaciones push funcionales en iOS + Android

### Fase 3 — Escala y monetización (continuo)

- [ ] Sistema de suscripción con Laravel Cashier + Stripe
- [ ] Panel de talleres colaboradores (dashboard separado)
- [ ] Afiliación de piezas (Autodoc / Amazon API)
- [ ] Service packs automáticos con enlaces de afiliado
- [ ] API pública para integraciones OBD
- [ ] Laravel Octane con FrankenPHP para máximo rendimiento
- [ ] Migración de módulos de alta carga a servicios independientes

### Fase 4 — Importación y Onboarding (NUEVA)

- [ ] **Import Wizard:** Herramienta para importar vehículos y clientes desde CSV/Excel.
- [ ] **Onboarding Guiado:** Tutorial interactivo para nuevos usuarios y talleres.
- [ ] **Sincronización Offline:** Estrategia de datos para Capacitor (uso en zonas sin cobertura).

### Fase 5 — Cumplimiento y Legal (NUEVA)

- [ ] **Gestión de Privacidad (GDPR):** Consentimiento, exportación de datos y borrado.
- [ ] **Auditoría de Seguridad:** Implementación de logs de actividad y auditoría de cambios.

### Fase 6 — Marketplace de Importación (V2)

- [x] Onboarding Express de profesionales (`StripeConnectController`)
- [x] `application_fee_amount` (8% comisión por defecto)
- [x] Webhook `payment_intent.succeeded` (`PaymentController`)
- [x] Facturación B2B de comisiones (`GenerateInvoiceAction` + `invoice.blade.php`)

---

## Testing strategy
...

```
tests/
├── Pest.php                        # Configuración global
├── Unit/
│   └── Modules/
│       ├── Identity/
│       │   └── Actions/
│       │       └── CreateUserActionTest.php
│       ├── Vehicle/
│       │   └── Actions/
│       │       ├── RegisterVehicleActionTest.php
│       │       └── UpdateKmActionTest.php
│       ├── Alerts/
│       │   └── Actions/
│       │       └── EvaluateAlertsActionTest.php
│       ├── Alerts/
│       │   └── Jobs/
│       │       └── SendPushJobTest.php
│       └── Marketplace/
│           └── Actions/
│               ├── CalculateScoreActionTest.php
│               └── ScrapeMarketValueActionTest.php
│
├── Feature/
│   └── Modules/
│       ├── Identity/
│       │   ├── AuthTest.php           # login, register, logout
│       │   └── GarageTest.php        # CRUD garajes
│       ├── Vehicle/
│       │   ├── VehicleTest.php       # CRUD vehículos
│       │   └── QrTest.php            # generación + vista pública
│       ├── Documents/
│       │   └── DocumentTest.php      # upload, list, delete
│       ├── Maintenance/
│       │   └── MaintenanceTest.php   # CRUD entries, timeline
│       ├── Alerts/
│       │   └── AlertTest.php         # rules, triggers, notifications
│       └── Marketplace/
│           └── SaleReportTest.php    # generación, PDF, link público
│
└── Browser/                           # Pest Dusk plugin
    └── Vehicle/
        └── VehicleBrowserTest.php
```

**Estado actual: 54 tests pasando** (12 VehicleTest + 9 AlertTest + 4 unitarios nuevos + 2 ServicePack + 2 API pública + 12 Import)

Comandos base:

```bash
# Todos los tests
./vendor/bin/pest

# Con coverage
./vendor/bin/pest --coverage --min=80

# Parallel (más rápido)
./vendor/bin/pest --parallel

# Static analysis
vendor/bin/phpstan analyse --memory-limit=2G

# Lint
./vendor/bin/pint

# Type-check Vue
npx vue-tsc --noEmit
```

---

## Primeros comandos para arrancar

```bash
# 1. Crear proyecto (Laravel 13 con Inertia 3)
laravel new garageos --using=laravel-presets/inertia
cd garageos

# 2. Instalar paquetes producción
composer require inertiajs/inertia-laravel:^3.0
composer require laravel/sanctum:^4.0
composer require laravel/breeze:^2.4 --dev
composer require laravel/horizon:^5.30
composer require laravel/pulse:^1.7
composer require laravel/scout:^11.2
composer require spatie/laravel-medialibrary:^11.23
composer require spatie/laravel-permission:^8.0
composer require spatie/laravel-activitylog:^4.11
composer require spatie/laravel-backup:^10.3
composer require chillerlan/php-qrcode:^6.0
composer require barryvdh/laravel-dompdf:^3.1
composer require maatwebsite/laravel-excel:^3.1
composer require predis/predis:^3.5

# 3. Paquetes desarrollo
composer require --dev barryvdh/laravel-debugbar:^4.3
composer require --dev barryvdh/laravel-ide-helper:^3.5
composer require --dev laravel/pint:^1.29
composer require --dev larastan/larastan:^3.10
composer require --dev pestphp/pest:^4.7 --with-all-dependencies
composer require --dev pestphp/pest-plugin-laravel:^4.1
composer require --dev nunomaduro/collision:^8.6

# 4. Frontend (Tailwind 4 + Vue 3.5 + Inertia 3)
npm install vue@^3.5 @inertiajs/vue3@^3.0
npm install tailwindcss@^4.1 @tailwindcss/forms @tailwindcss/typography
npm install @headlessui/vue @heroicons/vue@^2.2
npm install recharts dayjs axios pinia@^3.0
npm install @vueuse/core@^14.0 vue-sonner@^2.0
npm install --save-dev typescript@^5.8 vue-tsc@^2.2
npm install --save-dev eslint@^9.25 @typescript-eslint/parser@^8.30 @typescript-eslint/eslint-plugin@^8.30
npm install --save-dev prettier@^3.5

# 5. Breeze (Inertia + Vue + TypeScript)
php artisan breeze:install vue --typescript
npm install && npm run build

# 6. Capacitor 7
npm install @capacitor/core@^7.0 @capacitor/cli@^7.0
npx cap init GarageOS com.garageos.app
npx cap add android
# npx cap add ios   # solo macOS

# 7. Publicar configs
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
php artisan horizon:install
php artisan pulse:install

# 8. Migrations
php artisan make:migration create_garages_table
php artisan make:migration create_vehicles_table
php artisan make:migration create_vehicle_specs_table
php artisan make:migration create_km_history_table
php artisan make:migration create_documents_table
php artisan make:migration create_workshops_table
php artisan make:migration create_maintenance_entries_table
php artisan make:migration create_maintenance_intervals_table
php artisan make:migration create_service_packs_table
php artisan make:migration create_alert_rules_table
php artisan make:migration create_notifications_table
php artisan make:migration create_sale_reports_table
php artisan make:migration create_market_values_table

# 9. Ejecutar
php artisan migrate
php artisan storage:link

# 10. Models + Factories
php artisan make:model Vehicle -f
php artisan make:model VehicleSpec -f
php artisan make:model KmHistory -f
php artisan make:model Document -f
php artisan make:model Workshop -f
php artisan make:model MaintenanceEntry -f
php artisan make:model MaintenanceInterval -f
php artisan make:model ServicePack -f
php artisan make:model AlertRule -f
php artisan make:model Notification -f
php artisan make:model SaleReport -f
php artisan make:model MarketValue -f

# 11. Enums PHP 8.4
php artisan make:enum FuelType
php artisan make:enum DocumentType
php artisan make:enum MaintenanceType
php artisan make:enum AlertType

# 12. Eventos + Listeners
php artisan make:event VehicleRegistered
php artisan make:listener CreateDefaultAlertRules --event=VehicleRegistered
php artisan make:event KmUpdated
php artisan make:event DocumentUploaded
php artisan make:event DocumentProcessed
php artisan make:event RevisionCompleted
php artisan make:event AlertTriggered

# 13. Jobs
php artisan make:job ParseDocumentJob
php artisan make:job SendAlertJob
php artisan make:job SendPushJob
php artisan make:job EvaluateAlertsJob
php artisan make:job GenerateSaleReportJob

# 14. Verificar todo
php artisan test          # Pest
vendor/bin/phpstan analyse
./vendor/bin/pint --test
npx vue-tsc --noEmit
```

---

## Actualización a Laravel 13 / Inertia 3 (Julio 2026)

- Laravel 13: Mejor rendimiento, Inertia 3 integrado
- Inertia 3: SSR mejorado, TypeScript más estricto
- Vue 3.5: Vapor mode, defineModel simplificado
- Pest 4: Test runner actualizado
- Tailwind 4: Oxide engine (Rust)

---

## MVP mínimo viable — mañana mismo

El orden importa. Esta es la secuencia mínima que da valor real:

1. **Login + registro de usuario** (Breeze ya lo da)
2. **Añadir un coche** (placa, marca, modelo, año, km actuales)
3. **Subir la ITV** → que genere automáticamente una alerta de caducidad
4. **Dashboard** con el coche y el aviso de ITV

Con eso tienes un producto que ya resuelve un dolor real y puedes enseñárselo a alguien. Todo lo demás viene después.

---

## Estado actual (Julio 2026)

| Componente | Estado | Tests |
|------------|--------|-------|
| Vehicle Module | ✅ Completo | 12 tests |
| Alerts Module | ✅ Completo | 9 tests |
| Documents Module | ✅ Completo | - |
| Maintenance Module | ✅ Completo | - |
| Marketplace Module | ✅ Completo | - |
| FCM Push Notifications | ✅ Implementado | - |
| Dashboard Stats | ✅ Implementado | - |
| Pest Dusk | ✅ Scaffold | - |
| API pública OBD | ✅ Implementado | 2 tests |
| Stripe Subscriptions | ✅ Implementado | - |
| Panel Taller | ✅ Implementado | - |
| Service Pack Recommendations | ✅ Implementado | 2 tests |
| OpenAPI Docs | ✅ Implementado | - |
| Facturación B2B | ✅ Implementado | - |

**Total: 89 tests pasando** (25 Unit + 64 Feature)

### Próximos pasos

- [ ] Tests Unit para `GenerateInvoiceAction`
- [ ] Fase 5: GDPR (exportación/borrado datos)
- [ ] Fase 6: Marketplace de Importación (ver `plan_import.md`)

> **Nota:** Tests Feature con Inertia necesitan `npm run build` para Vite manifest.
- [ ] Deploy con `docker-compose up -d`

---

## DevOps & CI/CD

- **CI/CD Pipeline**: GitHub Actions para lint, tests, build y deploy automático.
- **Infrastructure as Code**: Docker Compose para entornos locales y producción.
- **Environment Management**: `.env.example` centralizado con scripts de configuración.

## Seguridad & Monitoreo

- **Seguridad**: CSP, headers seguros, auditoría de dependencias integrada en CI.
- **Monitoreo**: Laravel Pulse (backend), Sentry (frontend), Horizon para colas.

## Internacionalización & UX

- **i18n**: `laravel-lang` (backend) y `vue-i18n` (frontend) para soporte multi-idioma.
- **Accesibilidad**: Componentes accesibles (Headless UI), auditorías WCAG.

## Documentación & API

- **API Documentation**: Generada automáticamente con `laravel-scribe` (Swagger/OpenAPI).
- **Developer Docs**: Wiki interna para arquitectura y procesos.
- **User Docs**: Guías integradas en la aplicación.

## Backup & Rollback

- **Backup Strategy**: `spatie/laravel-backup` con backups diarios y pruebas de restauración.
- **Rollback Plan**: Estrategia "Blue-Green" para despliegues seguros.



## Nombre

Drivv: Modificación de Drive. Simple, tecnológico, directo al grano.

Zump: Suena a velocidad, a saltarse la burocracia de un plumazo. Muy estilo startup moderna.

Revv: De revoluciones (del motor). Visualmente es brutal con la doble 'v' (como Drivv), es cortísimo, y transmite la adrenalina de conseguir el coche que buscas. Suena a plataforma premium.

Shiftto: Juega con Shift (cambiar de marcha) y el movimiento de traer el coche. Tiene esa terminación en -o que buscabas al principio, pero sin sonar infantil. Suena a proceso automatizado.

Torqq: De Torque (par motor, la fuerza empuje). Si vuestro software es el que empuja todo el trámite burocrático y calcula la fuerza fiscal, este nombre transmite potencia bruta y tecnología.
