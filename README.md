# 🚗 GarageOS

> Plataforma SaaS modular para gestión de vehículos, mantenimientos, marketplace e importación de vehículos Alemania → España.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Vue-3.5-4FC08D?logo=vue.js" alt="Vue 3.5">
  <img src="https://img.shields.io/badge/TypeScript-5.8-3178C6?logo=typescript" alt="TypeScript">
  <img src="https://img.shields.io/badge/Tailwind-4-06B6D4?logo=tailwindcss" alt="Tailwind 4">
  <img src="https://img.shields.io/badge/PostgreSQL-16-336791?logo=postgresql" alt="PostgreSQL">
</p>

---

## 📚 Índice de Documentación

### 🚀 [Setup y Entorno de Desarrollo](docs/setup/)
| Documento | Descripción |
|-----------|-------------|
| **[Laragon (recomendado)](docs/setup/LARAGON.md)** | **Setup oficial: Laragon + Docker DB** |
| [Docker](docs/setup/DOCKER.md) | TODO: Setup legacy con Docker completo |
| [WSL](docs/setup/WSL-SETUP.md) | Alternativa con WSL2 + PHP nativo |
| [PostgreSQL](docs/setup/POSTGRESQL.md) | Configuración de base de datos |
| [Setup inicial](docs/setup/SETUP.md) | Primeros pasos en el proyecto |

### 🏗️ [Arquitectura](docs/architecture/)
| Documento | Descripción |
|-----------|-------------|
| [Stack](docs/architecture/STACK.md) | Stack tecnológico completo |
| [Módulos](docs/architecture/MODULAR.md) | Arquitectura modular |
| [Base de datos](docs/architecture/DATABASE.md) | Modelo de datos |
| [API REST](docs/architecture/API.md) | Endpoints |
| [Plan general](docs/architecture/PLAN.md) | Roadmap completo |

### 🧩 [Módulos del Sistema](docs/modules/)
| Módulo | Descripción |
|--------|-------------|
| [Vehicle](docs/modules/VEHICLE.md) | Gestión de vehículos |
| [VehicleImport](docs/modules/VEHICLE-IMPORT.md) | Importación DE→ES |
| [Maintenance](docs/modules/MAINTENANCE.md) | Service Packs |
| [Marketplace](docs/modules/MARKETPLACE.md) | Compraventa |
| [Documents](docs/modules/DOCUMENTS.md) | Documentos + OCR |
| [Alerts](docs/modules/ALERTS.md) | Reglas de alertas |
| [Identity](docs/modules/IDENTITY.md) | Usuarios y garajes |
| **[Sistema de Roles y Suscripciones](docs/SYSTEM_ROLES_SUBSCRIPTIONS.md)** | Roles, planes y features |

#### 🔑 Credenciales SuperAdmin
- **Email:** `jacararsco@garageos.com`
- **Password:** `admin`
- **Rol:** `superadmin`
- **Acceso:** `/admin`

### 💻 [Desarrollo](docs/development/)
| Documento | Descripción |
|-----------|-------------|
| [Convenciones](docs/development/CONVENTIONS.md) | Estilo de código |
| [Testing](docs/development/TESTING.md) | Tests con Pest |
| [Git workflow](docs/development/GIT-WORKFLOW.md) | Flujo de Git |
| [Debugging](docs/development/DEBUGGING.md) | Resolución de problemas |

### 🎨 [Diseño](docs/design/)
| Documento | Descripción |
|-----------|-------------|
| [Design System](docs/design/DESIGN-SYSTEM.md) | Tokens y componentes |
| [Mejoras visuales](docs/design/VISUAL-IMPROVEMENTS.md) | Plan de mejoras |
| [Dark mode](docs/design/DARK-MODE.md) | Modo oscuro |
| [OpenDesigner](docs/design/OPENDESIGNER.md) | Workflow de diseño |

### 📱 [Mobile](docs/mobile/)
| Documento | Descripción |
|-----------|-------------|
| [Capacitor](docs/mobile/CAPACITOR.md) | Configuración |
| [Implementación](docs/mobile/IMPLEMENTATION.md) | Plan de implementación |
| [Estado](docs/mobile/STATUS.md) | Estado actual |

### 🚀 [Despliegue](docs/deployment/)
| Documento | Descripción |
|-----------|-------------|
| [Deploy gratis](docs/deployment/FREE-DEPLOY.md) | Opciones gratuitas |
| [Producción](docs/deployment/PRODUCTION.md) | Deploy en producción |

---

## ⚡ Quick Start (100% Laragon nativo)

```powershell
# 1. Instala PostgreSQL 16 en C:\laragon\bin\postgresql\16
#    Descarga: https://www.postgresql.org/download/windows/

# 2. Configura DB
powershell -ExecutionPolicy Bypass -File scripts\setup-postgresql-native.ps1

# 3. Instala deps
composer install
npm install

# 4. Setup Laravel
cp .env.example .env
php artisan key:generate
php artisan migrate

# 5. Arranca (2 terminales)
php artisan serve      # http://localhost:8000
npm run dev            # http://localhost:5173
```

📖 Ver guía completa: [docs/setup/LARAGON.md](docs/setup/LARAGON.md)

---

## 🛠️ Stack

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 13 + PHP 8.4 |
| Frontend | Vue 3.5 + Inertia.js 2 + TypeScript 5.8 |
| Estilos | Tailwind CSS 4 + shadcn-vue |
| Base de datos | PostgreSQL 16 + PostGIS |
| Cache/Queue | Redis 7 |
| Mobile | Capacitor 7 |
| Tests | Pest PHP 3 |
| Server | Octane + FrankenPHP |
| DevOps | Docker + Docker Compose |

---

## 📦 Estructura del Proyecto

```
app_garage/
├── app/
│   ├── Modules/              # Módulos del sistema
│   │   ├── Vehicle/         # Gestión vehículos
│   │   ├── VehicleImport/   # Importación DE→ES
│   │   ├── Maintenance/     # Service Packs
│   │   ├── Marketplace/     # Compraventa
│   │   ├── Documents/       # Documentos + OCR
│   │   ├── Alerts/          # Alertas
│   │   └── Identity/        # Usuarios/Garajes
│   ├── Http/                 # Controllers
│   ├── Models/               # Modelos globales
│   ├── Services/             # Servicios compartidos
│   ├── Console/              # Comandos Artisan
│   └── Providers/            # Service Providers
├── database/
│   ├── migrations/           # 15+ migraciones
│   ├── seeders/              # Datos iniciales
│   └── factories/            # Factories para tests
├── resources/
│   ├── js/                   # Frontend Vue + Inertia
│   │   ├── Components/       # shadcn-vue
│   │   ├── Pages/            # Páginas Inertia
│   │   ├── Layouts/          # Layouts
│   │   └── types/            # TypeScript types
│   └── views/                # Vistas Blade
├── routes/                   # Rutas
├── tests/                    # Tests Pest
├── docs/                     # 📚 Esta documentación
└── docker/                   # Configuración Docker
```

---

## 🤝 Contribución

Ver [workflow de desarrollo](docs/development/GIT-WORKFLOW.md).

---

## 📄 Licencia

Propietario - Todos los derechos reservados.
