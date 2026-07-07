# 📚 Documentación de GarageOS

> Índice completo de toda la documentación del proyecto.

---

## 🚀 Setup y Entorno

| Documento | Descripción |
|-----------|-------------|
| [Setup inicial](setup/SETUP.md) | Primeros pasos |
| [Docker](setup/DOCKER.md) | Entorno con Docker |
| [WSL](setup/WSL-SETUP.md) | Entorno con WSL2 |
| [PostgreSQL](setup/POSTGRESQL.md) | Setup de base de datos |

---

## 🏗️ Arquitectura

| Documento | Descripción |
|-----------|-------------|
| [Stack](architecture/STACK.md) | Stack tecnológico completo |
| [Módulos](architecture/MODULAR.md) | Arquitectura modular |
| [Base de datos](architecture/DATABASE.md) | Modelo de datos |
| [API REST](architecture/API.md) | Endpoints |
| [Plan general](architecture/PLAN.md) | Roadmap completo |
| [Update frontend](architecture/FRONTEND-UPDATE.md) | Migración TW3→TW4 |

---

## 🧩 Módulos

| Módulo | Documento |
|--------|-----------|
| Vehicle | [modules/VEHICLE.md](modules/VEHICLE.md) |
| VehicleImport | [modules/VEHICLE-IMPORT.md](modules/VEHICLE-IMPORT.md) |
| VehicleImport (Guía DE→ES) | [modules/VEHICLE-IMPORT-GUIDE.md](modules/VEHICLE-IMPORT-GUIDE.md) |
| VehicleImport (Plan) | [modules/VEHICLE-IMPORT-PLAN.md](modules/VEHICLE-IMPORT-PLAN.md) |
| Maintenance | [modules/MAINTENANCE.md](modules/MAINTENANCE.md) |
| Marketplace | [modules/MARKETPLACE.md](modules/MARKETPLACE.md) |
| Documents | [modules/DOCUMENTS.md](modules/DOCUMENTS.md) |
| Alerts | [modules/ALERTS.md](modules/ALERTS.md) |
| Identity | [modules/IDENTITY.md](modules/IDENTITY.md) |

---

## 💻 Desarrollo

| Documento | Descripción |
|-----------|-------------|
| [Quick start](development/QUICK-START.md) | Arranque rápido |
| [Convenciones](development/CONVENTIONS.md) | Estilo de código |
| [Testing](development/TESTING.md) | Tests con Pest |
| [Git workflow](development/GIT-WORKFLOW.md) | Flujo de Git |
| [Debugging](development/DEBUGGING.md) | Resolución de problemas |

---

## 🎨 Diseño

| Documento | Descripción |
|-----------|-------------|
| [Design System](design/DESIGN-SYSTEM.md) | Tokens y componentes |
| [Mejoras visuales](design/VISUAL-IMPROVEMENTS.md) | Plan de mejoras |
| [Dark mode](design/DARK-MODE.md) | Modo oscuro |
| [Design improvements 2025](design/DESIGN-IMPROVEMENTS-2025.md) | Mejoras 2025 |
| [OpenDesigner](design/OPENDESIGNER.md) | Workflow de diseño |
| [Web redesign 2025](design/WEB-REDESIGN-2025.md) | Rediseño web |

---

## 📱 Mobile

| Documento | Descripción |
|-----------|-------------|
| [Capacitor](mobile/CAPACITOR.md) | Configuración |
| [Implementación](mobile/IMPLEMENTATION.md) | Plan de implementación |
| [Estado](mobile/STATUS.md) | Estado actual |

---

## 🚀 Despliegue

| Documento | Descripción |
|-----------|-------------|
| [Deploy gratis](deployment/FREE-DEPLOY.md) | Opciones gratuitas |
| [Producción](deployment/PRODUCTION.md) | Deploy en producción |

---

## 🗂️ Estructura de la documentación

```
docs/
├── README.md                  # ← Este archivo
├── setup/                     # Configuración del entorno
│   ├── SETUP.md
│   ├── DOCKER.md
│   ├── WSL-SETUP.md
│   └── POSTGRESQL.md
├── architecture/              # Arquitectura del sistema
│   ├── STACK.md
│   ├── MODULAR.md
│   ├── DATABASE.md
│   ├── API.md
│   ├── PLAN.md
│   └── FRONTEND-UPDATE.md
├── modules/                   # Documentación por módulo
│   ├── VEHICLE.md
│   ├── VEHICLE-IMPORT.md
│   ├── VEHICLE-IMPORT-GUIDE.md
│   ├── VEHICLE-IMPORT-PLAN.md
│   ├── MAINTENANCE.md
│   ├── MARKETPLACE.md
│   ├── DOCUMENTS.md
│   ├── ALERTS.md
│   └── IDENTITY.md
├── development/               # Guías para desarrolladores
│   ├── QUICK-START.md
│   ├── CONVENTIONS.md
│   ├── TESTING.md
│   ├── GIT-WORKFLOW.md
│   └── DEBUGGING.md
├── design/                    # Sistema de diseño
│   ├── DESIGN-SYSTEM.md
│   ├── VISUAL-IMPROVEMENTS.md
│   ├── DARK-MODE.md
│   ├── DESIGN-IMPROVEMENTS-2025.md
│   ├── OPENDESIGNER.md
│   └── WEB-REDESIGN-2025.md
├── mobile/                    # Mobile (Capacitor)
│   ├── CAPACITOR.md
│   ├── IMPLEMENTATION.md
│   └── STATUS.md
└── deployment/                # Despliegue en producción
    ├── FREE-DEPLOY.md
    └── PRODUCTION.md
```

---

## 📖 Cómo usar esta documentación

1. **Nuevo en el proyecto?** → Empieza por [Setup inicial](setup/SETUP.md)
2. **¿Cómo funciona X módulo?** → Ve a `modules/`
3. **¿Cómo escribo código?** → Lee [Convenciones](development/CONVENTIONS.md)
4. **¿Cómo despliego?** → Consulta [Deployment](deployment/)
5. **¿Cómo diseño UI?** → Revisa [Design System](design/DESIGN-SYSTEM.md)

---

## ✏️ Contribuir a la documentación

Para añadir o mejorar documentación:

1. Crea/edita el archivo `.md` correspondiente
2. Actualiza este README si es un documento nuevo
3. Usa el formato de las secciones existentes
4. Commitea con prefijo `docs:`
