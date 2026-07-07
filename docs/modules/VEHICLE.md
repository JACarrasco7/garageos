# Módulo Vehicle

> Gestión completa de vehículos del usuario.

---

## 📋 Responsabilidades

- CRUD de vehículos
- Especificaciones técnicas (VehicleSpec)
- VIN decoder (NHTSA API)
- Documentos asociados
- Mantenimientos
- QR público para compartir
- Valoración de mercado (Marketplace integration)

---

## 📁 Estructura

```
app/Modules/Vehicle/
├── Models/
│   ├── Vehicle.php
│   └── VehicleSpec.php
├── Http/Controllers/
│   ├── VehicleController.php
│   ├── VehiclePhotoController.php
│   ├── PublicVehicleController.php
│   └── Api/VehicleApiController.php
├── Events/
│   └── VehicleRegistered.php
├── Resources/
│   ├── VehicleResource.php
│   ├── VehicleSpecResource.php
│   └── VehiclePhotoResource.php
├── Services/
│   └── CarDataService.php        # VIN decoder (NHTSA)
├── Actions/
│   └── RegisterVehicleAction.php
├── Providers/
│   └── VehicleServiceProvider.php
└── Content/
```

---

## 🗄️ Modelos

### Vehicle
```php
- id
- user_id (FK)
- garage_id (FK, nullable)
- plate (string, unique)
- brand (string)
- model (string)
- year (int)
- current_km (int)
- vin (string, nullable)
- qr_token (string, unique)
- color (string, nullable)
- fuel_type (enum: gasoline, diesel, hybrid, electric, lpg)
- purchase_date (date, nullable)
- created_at, updated_at
```

### VehicleSpec
```php
- id
- vehicle_id (FK)
- engine_cc (int, nullable)
- power_hp (int, nullable)
- power_kw (int, nullable)
- co2_emissions (int, nullable)  # g/km
- transmission (enum: manual, automatic)
- doors (int, nullable)
- seats (int, nullable)
- weight_kg (int, nullable)
```

---

## 🌐 API Endpoints

### Públicos (con QR token)
```http
GET /api/v1/vehicles/{qr_token}
GET /api/v1/vehicles/{qr_token}/service-pack/{type}
GET /api/v1/vehicles/decode-vin/{vin}
```

### Autenticados
```http
GET    /vehicles                   # Listar mis vehículos
POST   /vehicles                   # Crear
GET    /vehicles/{id}              # Detalle
PUT    /vehicles/{id}              # Actualizar
DELETE /vehicles/{id}              # Eliminar
POST   /vehicles/{id}/photos       # Subir foto
```

---

## 🧪 Tests

```
tests/Feature/Vehicle/
tests/Unit/Vehicle/
```

---

## 🔗 Relaciones

```
Vehicle
├── belongsTo User
├── belongsTo Garage
├── hasMany VehicleSpec
├── hasMany Document
├── hasMany MaintenanceEntry
├── hasMany AlertRule
├── hasMany Valuation
└── hasOne Listing (Marketplace)
```

---

## 📌 Notas

- QR token se genera automáticamente al crear (UUID v4)
- VIN decoder usa API pública NHTSA
- Especificaciones se autocompletan al hacer VIN decode
