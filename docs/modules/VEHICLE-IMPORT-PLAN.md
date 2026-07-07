# Plan de Implementación: Módulo de Sourcing para Vehicle Import

## 🎯 Objetivo
Transformar el módulo `VehicleImport` de una herramienta de gestión de trámites a un Marketplace completo de importación. El flujo permitirá a los clientes solicitar vehículos y a los proveedores ofertar, integrando el proceso de importación una vez aceptada la oferta.

---

## 🏗️ Arquitectura de Datos

### 1. Nuevos Modelos y Tablas

#### `VehicleImportRequest` (La Solicitud)
*   `user_id` (FK a `users`) - Cliente que solicita.
*   `brand` (string)
*   `model` (string)
*   `year` (integer, nullable)
*   `fuel_type` (string, nullable)
*   `mileage` (integer, nullable)
*   `budget_min` (decimal, nullable)
*   `budget_max` (decimal, nullable)
*   `url_link` (string, nullable)
*   `description` (text, nullable)
*   `status` (enum: `open`, `closed`, `cancelled`)

#### `VehicleImportOffer` (La Oferta)
*   `vehicle_import_request_id` (FK a `vehicle_import_requests`)
*   `user_id` (FK a `users`) - Proveedor que oferta.
*   `price` (decimal)
*   `delivery_time_days` (integer, nullable)
*   `warranty_months` (integer, default 0)
*   `description` (text, nullable)
*   `status` (enum: `pending`, `accepted`, `rejected`)

#### `VehicleImportRating` (La Valoración)
*   `vehicle_import_offer_id` (FK a `vehicle_import_offers`)
*   `user_id` (FK a `users`) - Cliente que valora.
*   `rating` (integer 1-5)
*   `comment` (text, nullable)

### 2. Modificaciones en Tablas Existentes

#### `workshops`
*   Añadir `country` (string)
*   Añadir `province` (string)

#### `vehicle_imports` (El Proceso de 6 pasos)
*   Añadir `vehicle_import_offer_id` (FK a `vehicle_import_offers`, nullable) - Para saber qué oferta originó el trámite.

---

## 🛠️ Implementación Backend

### Fase 1: Base de Datos
- [ ] Migración: Añadir `country` y `province` a `workshops`.
- [ ] Migración: Crear `vehicle_import_requests`.
- [ ] Migración: Crear `vehicle_import_offers`.
- [ ] Migración: Crear `vehicle_import_ratings`.
- [ ] Migración: Añadir `vehicle_import_offer_id` a `vehicle_imports`.

### Fase 2: Modelos y Relaciones
- [ ] Modelo `VehicleImportRequest` + Factory.
- [ ] Modelo `VehicleImportOffer` + Factory.
- [ ] Modelo `VehicleImportRating` + Factory.
- [ ] Actualizar `VehicleImport` para incluir relación con `Offer`.

### Fase 3: Lógica de Negocio (Actions & Requests)
- [ ] `StoreVehicleImportRequest` (FormRequest).
- [ ] `StoreVehicleImportOffer` (FormRequest).
- [ ] `AcceptImportOfferAction` (Crea el `VehicleImport` y cierra la solicitud/oferta).

### Fase 4: Controladores y Rutas
- [ ] `VehicleImportRequestController` (API/Web).
- [ ] `VehicleImportOfferController` (API/Web).
- [ ] `PublicPortfolioController` (Vista pública de proveedores).
- [ ] Registrar rutas en `VehicleImportServiceProvider`.

---

## 🎨 Implementación Frontend (Vue 3 + Inertia)

### Para el Cliente (Usuario X)
- [ ] `VehicleImport/Requests/Index.vue` (Mis solicitudes).
- [ ] `VehicleImport/Requests/Create.vue` (Formulario de nueva solicitud).
- [ ] `VehicleImport/Offers/Show.vue` (Ver ofertas recibidas y aceptar).
- [ ] `VehicleImport/Tracking/Show.vue` (Timeline de seguimiento del proceso).

### Para el Proveedor (Usuario Y)
- [ ] `VehicleImport/Offers/Index.vue` (Ver solicitudes abiertas para ofertar).
- [ ] `VehicleImport/Offers/Create.vue` (Formulario de oferta).
- [ ] `VehicleImport/Management/Wizard.vue` (Gestión de los 6 pasos - Integración con lo existente).

### Público
- [ ] `VehicleImport/Public/Portfolio.vue` (Listado de proveedores con ratings).

---

## 🧪 Testing
- [ ] Tests de integración para el flujo: Solicitud $\rightarrow$ Oferta $\rightarrow$ Aceptación $\rightarrow$ Creación de Import.
- [ ] Tests de validación de FormRequests.
- [ ] Tests de la lógica de cálculo de ratings.
