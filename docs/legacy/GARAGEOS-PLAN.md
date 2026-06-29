# GarageOS — Plan de Implementación

> **Stack:** Laravel 12 + Vue 3.5 + Inertia.js 2 + TypeScript 5.8 + Tailwind CSS 4 + MySQL 8.4 + Redis 7 + PHP 8.4
> **Última actualización:** 29 de Junio de 2026

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
| Runtime | PHP | 8.4 | Fibers, lazy objects, property hooks, JIT mejorado |
| Backend | Laravel | 12.x | Monolito modular, ORM, colas, eventos, schedule |
| Frontend | Vue | 3.5 | Composition API, Vapor mode, mejor TypeScript |
| Bridge | Inertia.js | 2.x | SPA sin API boilerplate, SSR nativo |
| Tipado | TypeScript | 5.8 | Tipado estricto, decorators, mejor inferencia |
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

- **PHP 8.4**: Property hooks (menos boilerplate en modelos), lazy objects (mejor rendimiento con relaciones), Fibers para async nativo, JIT mejorado para cálculos pesados (score de vehículos, cálculos de coste/km)
- **Laravel 12**: Mejor estructura de rutas, `laravel new` con Inertia nativo, mejor integración con Vite 6, `php artisan config:cache` más rápido, soporte para lazy collections mejorado
- **Vue 3.5**: Vapor mode (mejor rendimiento), mejor soporte TypeScript, `defineModel` simplificado, Suspense nativo
- **Inertia.js 2**: SSR nativo mejorado, partial reloads optimizados, mejor manejo de assets
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

### PHP / Composer — Producción

| Paquete | Versión | Razón |
|---|---|---|
| `laravel/framework` | ^12.0 | Framework base |
| `inertiajs/inertia-laravel` | ^2.0 | Inertia SSR/SPA |
| `laravel/sanctum` | ^4.0 | API tokens + SPA auth |
| `laravel/breeze` | ^2.3 | Scaffolding auth + Inertia + Vue + TS |
| `laravel/tinker` | ^2.10 | Consola interactiva |
| `laravel/horizon` | ^5.30 | Dashboard colas Redis |
| `laravel/pulse` | ^1.4 | Monitorización en tiempo real |
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

### NPM — Frontend

| Paquete | Versión | Razón |
|---|---|---|
| `vue` | ^3.5 | Composition API, Vapor mode |
| `@inertiajs/vue3` | ^2.0 | Inertia adapter |
| `@vitejs/plugin-vue` | ^5.2 | Vite + Vue |
| `vite` | ^6.3 | Build tool (Laravel 12 usa Vite 6) |
| `typescript` | ^5.8 | Tipado estricto |
| `tailwindcss` | ^4.1 | Oxide engine (Rust), 5x más rápido |
| `@tailwindcss/forms` | ^0.5 | Reset de formularios |
| `@tailwindcss/typography` | ^0.5 | Prose styles |
| `@headlessui/vue` | ^1.7 | Componentes accesibles |
| `@heroicons/vue` | ^2.2 | Iconos |
| `recharts` | ^2.15 | Gráficas de gastos |
| `dayjs` | ^1.11 | Fechas ligeras |
| `axios` | ^1.8 | HTTP client |
| `pinia` | ^3.0 | State management |
| `@vueuse/core` | ^13.0 | Composables utilitarios |
| `vue-sonner` | ^1.3 | Toast notifications |

### NPM — Desarrollo

| Paquete | Versión | Razón |
|---|---|---|
| `laravel-vite-plugin` | ^1.2 | Integración Laravel + Vite |
| `autoprefixer` | ^10.4 | CSS vendor prefixes |
| `postcss` | ^8.5 | Procesador CSS |
| `prettier` | ^3.5 | Formateo |
| `eslint` | ^9.25 | Linter flat config |
| `@typescript-eslint/parser` | ^8.30 | TS parser para ESLint |
| `@typescript-eslint/eslint-plugin` | ^8.30 | Reglas TS |
| `vue-tsc` | ^2.2 | Type-checking Vue SFC |

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

- [x] `laravel new garageos --using=laravel-presets/inertia` (Inertia nativo en Laravel 12)
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

- [x] Migrations: `users`, `garages`, `vehicles`, `vehicle_specs`, `km_history`
- [x] Service Providers por módulo
- [x] Models con relationships, casts, factories, Enums PHP 8.4
- [x] CRUD de vehículos (placa, VIN, marca, modelo, año)
- [x] Generación de QR único por vehículo
- [x] Dashboard con listado del garaje virtual
- [x] Vista pública por QR (sin login)
- [x] Tests: Pest Unit + Feature de Vehicle

**Semana 2: Documents**

- [x] Migration de `documents`
- [x] Upload de ficheros con Spatie MediaLibrary (S3 o local)
- [x] Visor de documentos en frontend (PDF/image preview)
- [x] Alertas básicas de caducidad (ITV, seguro) — rules manuales
- [x] Listado de docs por vehículo con filtro por tipo

**Semana 3: Maintenance**

- [x] Migrations: `workshops`, `maintenance_entries`, `maintenance_intervals`, `service_packs`
- [x] Timeline de vida del coche (ordenado por fecha/km desc)
- [x] Formulario para añadir entrada manual
- [x] Cálculo de próximo mantenimiento basado en `maintenance_intervals`
- [x] Sello "Verificado" cuando lo añade un taller

**Semana 4: Alerts + Notificaciones**

- [x] Migrations: `alert_rules`, `notifications`
- [x] Job programado: `EvaluateAlertsCommand` en `php artisan schedule:run`
- [x] Notificaciones in-app (badge en navbar)
- [x] Email de recordatorio con Laravel Mail
- [x] Push notifications con Capacitor + Firebase

### Fase 2 — Features de valor (3-4 semanas)

**Semana 5-6: Marketplace**

- [x] Migrations: `sale_reports`, `market_values`
- [x] Generación de informe PDF con DomPDF
- [x] Score de salud del vehículo (algoritmo basado en historial)
- [x] Link público temporal con token (expira en 30 días)
- [x] Vista pública del informe (sin login, para compradores)

**Semana 7-8: Stats + UX avanzada**

- [x] Calculadora de coste por km (suma de facturas / km recorridos)
- [x] Gráfica de gastos mensual (Recharts)
- [x] Historial de valor de mercado (scraper API Autocasión / coches.net)
- [x] OCR básico para facturas (Tesseract via shell o AWS Textract)
- [x] Notificaciones push funcionales en iOS + Android

### Fase 3 — Escala y monetización (continuo)

- [ ] Sistema de suscripción con Laravel Cashier + Stripe
- [ ] Panel de talleres colaboradores (dashboard separado)
- [ ] Afiliación de piezas (Autodoc / Amazon API)
- [ ] Service packs automáticos con enlaces de afiliado
- [ ] API pública para integraciones OBD
- [ ] Laravel Octane con FrankenPHP para máximo rendimiento
- [ ] Migración de módulos de alta carga a servicios independientes

---

## Testing strategy

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

**Estado actual: 50 tests pasando** (12 VehicleTest + 9 AlertTest + 4 unitarios nuevos)

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
# 1. Crear proyecto (Laravel 12 con Inertia)
laravel new garageos --using=laravel-presets/inertia
cd garageos

# 2. Instalar paquetes producción
composer require inertiajs/inertia-laravel:^2.0
composer require laravel/sanctum:^4.0
composer require laravel/breeze:^2.3 --dev
composer require laravel/horizon:^5.30
composer require laravel/pulse:^1.4
composer require laravel/scout:^10.8
composer require spatie/laravel-medialibrary:^11.12
composer require spatie/laravel-permission:^6.15
composer require spatie/laravel-activitylog:^4.11
composer require spatie/laravel-backup:^9.3
composer require chillerlan/php-qrcode:^5.0
composer require barryvdh/laravel-dompdf:^3.1
composer require maatwebsite/laravel-excel:^3.1
composer require predis/predis:^2.3

# 3. Paquetes desarrollo
composer require --dev barryvdh/laravel-debugbar:^3.14
composer require --dev barryvdh/laravel-ide-helper:^3.5
composer require --dev laravel/pint:^1.21
composer require --dev larastan/larastan:^3.0
composer require --dev pestphp/pest:^3.0 --with-all-dependencies
composer require --dev pestphp/pest-plugin-laravel:^3.0
composer require --dev nunomaduro/collision:^8.5

# 4. Frontend (Tailwind 4 + Vue 3.5)
npm install vue@^3.5 @inertiajs/vue3@^2.0
npm install tailwindcss@^4.1 @tailwindcss/forms @tailwindcss/typography
npm install @headlessui/vue @heroicons/vue@^2.2
npm install recharts dayjs axios pinia@^3.0
npm install @vueuse/core@^13.0 vue-sonner@^1.3
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

## MVP mínimo viable — mañana mismo

El orden importa. Esta es la secuencia mínima que da valor real:

1. **Login + registro de usuario** (Breeze ya lo da)
2. **Añadir un coche** (placa, marca, modelo, año, km actuales)
3. **Subir la ITV** → que genere automáticamente una alerta de caducidad
4. **Dashboard** con el coche y el aviso de ITV

Con eso tienes un producto que ya resuelve un dolor real y puedes enseñárselo a alguien. Todo lo demás viene después.

---

## Estado actual (Junio 2026)

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

**Total: 54 tests pasando**

### Próximos pasos

- [ ] `composer require laravel/cashier`
- [ ] Configurar `STRIPE_KEY/STRIPE_SECRET` en `.env`
- [ ] `php artisan migrate` para nuevas tablas
- [ ] Configurar Firebase credentials en `storage/app/firebase-credentials.json`
- [ ] Ejecutar `npx cap add android` para generar proyecto móvil
- [ ] Deploy con `docker-compose up -d`
