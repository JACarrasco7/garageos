# Modelo de Datos

> Esquema completo de base de datos PostgreSQL de GarageOS.

---

## 📊 Diagrama ER (resumido)

```
User (1) ──< (N) Garage (1) ──< (N) Vehicle (1) ──< (N) VehicleSpec
                                              │
                                              ├──< (N) MaintenanceEntry
                                              ├──< (N) Document
                                              ├──< (N) AlertRule
                                              ├──< (N) Valuation
                                              └──< (N) MarketplaceListing (1) ──< (N) Offer
                                                                            └──< (N) Transaction
```

---

## 🗄️ Tablas principales

### `users`
```sql
- id BIGSERIAL PRIMARY KEY
- name VARCHAR
- email VARCHAR UNIQUE
- email_verified_at TIMESTAMP
- password VARCHAR
- stripe_id VARCHAR (Cashier)
- card_brand VARCHAR
- card_last_four VARCHAR
- trial_ends_at TIMESTAMP
- remember_token VARCHAR
- created_at, updated_at
```

### `garages` (multi-tenant)
```sql
- id BIGSERIAL PRIMARY KEY
- user_id BIGINT REFERENCES users(id)
- name VARCHAR
- type VARCHAR (personal, professional, taller, dealership)
- address VARCHAR
- city VARCHAR
- postal_code VARCHAR
- country VARCHAR DEFAULT 'ES'
- phone VARCHAR
- email VARCHAR
- is_default BOOLEAN
- created_at, updated_at
```

### `vehicles`
```sql
- id BIGSERIAL PRIMARY KEY
- garage_id BIGINT REFERENCES garages(id)
- plate VARCHAR UNIQUE
- brand VARCHAR
- model VARCHAR
- year INT
- current_km INT
- vin VARCHAR(17)
- qr_token VARCHAR UNIQUE (UUID)
- color VARCHAR
- fuel_type VARCHAR (gasoline, diesel, hybrid, electric, lpg)
- purchase_date DATE
- created_at, updated_at
```

### `vehicle_specs`
```sql
- id BIGSERIAL PRIMARY KEY
- vehicle_id BIGINT REFERENCES vehicles(id)
- engine_cc INT
- power_hp INT
- power_kw INT
- co2_emissions INT
- transmission VARCHAR (manual, automatic)
- doors INT
- seats INT
- weight_kg INT
```

### `documents`
```sql
- id BIGSERIAL PRIMARY KEY
- vehicle_id BIGINT REFERENCES vehicles(id)
- type VARCHAR (itv, insurance, factura, permiso, ficha_tecnica, otro)
- title VARCHAR
- file_path VARCHAR
- mime_type VARCHAR
- size_bytes INT
- document_date DATE
- expiry_date DATE
- ocr_text TEXT
- ocr_extracted_data JSONB
- created_at, updated_at
```

### `maintenance_entries`
```sql
- id BIGSERIAL PRIMARY KEY
- vehicle_id BIGINT REFERENCES vehicles(id)
- type VARCHAR (aceite, filtros, distribucion, completo)
- title VARCHAR
- description TEXT
- service_date DATE
- km_at_service INT
- cost DECIMAL(10,2)
- workshop_name VARCHAR
- invoice_number VARCHAR
- created_at, updated_at
```

### `maintenance_intervals`
```sql
- id BIGSERIAL PRIMARY KEY
- maintenance_type VARCHAR UNIQUE
- km_interval INT
- months_interval INT
- description TEXT
```

### `alert_rules`
```sql
- id BIGSERIAL PRIMARY KEY
- vehicle_id BIGINT REFERENCES vehicles(id)
- type VARCHAR (km, date, days_before)
- target_type VARCHAR (itv, insurance, maintenance, etc)
- threshold_value INT
- is_active BOOLEAN DEFAULT true
- last_triggered_at TIMESTAMP
- notification_channels JSONB DEFAULT '["email","in_app"]'
- created_at, updated_at
```

---

## 🛒 Marketplace

### `marketplace_listings`
```sql
- id BIGSERIAL PRIMARY KEY
- vehicle_id BIGINT REFERENCES vehicles(id)
- seller_id BIGINT REFERENCES users(id)
- price DECIMAL(10,2)
- currency VARCHAR DEFAULT 'EUR'
- status VARCHAR (draft, active, reserved, sold, expired)
- description TEXT
- location VARCHAR
- images JSONB
- views_count INT DEFAULT 0
- search_vector tsvector  -- full-text search
- expires_at TIMESTAMP
- created_at, updated_at
```

### `transactions`
```sql
- id BIGSERIAL PRIMARY KEY
- listing_id BIGINT REFERENCES marketplace_listings(id)
- buyer_id BIGINT REFERENCES users(id)
- seller_id BIGINT REFERENCES users(id)
- amount DECIMAL(10,2)
- platform_fee DECIMAL(10,2)
- stripe_payment_intent_id VARCHAR
- status VARCHAR (pending, paid, completed, refunded, cancelled)
- completed_at TIMESTAMP
- created_at, updated_at
```

### `valuations`
```sql
- id BIGSERIAL PRIMARY KEY
- vehicle_id BIGINT REFERENCES vehicles(id)
- brand VARCHAR
- model VARCHAR
- year INT
- mileage_km INT
- fuel_type VARCHAR
- power_hp INT
- estimated_value DECIMAL(10,2)
- confidence_score DECIMAL(3,2)  -- 0.00-1.00
- source VARCHAR (algorithm, manual, market)
- calculated_at TIMESTAMP
```

---

## 📥 Importación DE→ES

### `vehicle_imports`
```sql
- id BIGSERIAL PRIMARY KEY
- user_id BIGINT REFERENCES users(id)
- vehicle_id BIGINT REFERENCES vehicles(id)
- plate_original VARCHAR (alemana)
- plate_new VARCHAR (española definitiva)
- brand VARCHAR
- model VARCHAR
- year INT
- engine_cc INT
- power_kw INT
- co2_emissions INT
- origin_country VARCHAR(2) DEFAULT 'DE'
- purchase_date DATE
- arrival_date DATE
- itv_deadline DATE  -- arrival + 30 días
- current_step VARCHAR (purchase, transport, itv_inspection, taxes, dgt, plates, completed)
- needs_homologation BOOLEAN
- status VARCHAR
- documents JSONB
- rejection_reason TEXT
- created_at, updated_at
```

### `import_documents`
```sql
- id BIGSERIAL PRIMARY KEY
- import_id BIGINT REFERENCES vehicle_imports(id)
- step VARCHAR
- type VARCHAR (compraventa, coc, ficha_tecnica_origen, etc)
- file_path VARCHAR
- is_verified BOOLEAN DEFAULT false
- uploaded_at TIMESTAMP
```

### `temporary_plates`
```sql
- id BIGSERIAL PRIMARY KEY
- import_id BIGINT REFERENCES vehicle_imports(id)
- plate_number VARCHAR
- issued_at DATE
- expires_at DATE  -- issued + 2 meses
- is_extended BOOLEAN
- created_at TIMESTAMP
```

---

## 🔐 Auth & Permisos

### `personal_access_tokens` (Sanctum)
```sql
- id BIGSERIAL PRIMARY KEY
- tokenable_type VARCHAR
- tokenable_id BIGINT
- name VARCHAR
- token VARCHAR UNIQUE
- abilities TEXT
- last_used_at TIMESTAMP
- expires_at TIMESTAMP
- created_at, updated_at
```

### Tablas de Spatie Permission
- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

---

## 📈 Índices importantes

```sql
-- Búsqueda full-text en marketplace
CREATE INDEX idx_listings_search ON marketplace_listings USING GIN(search_vector);

-- Búsquedas frecuentes
CREATE INDEX idx_vehicles_plate ON vehicles(plate);
CREATE INDEX idx_vehicles_qr_token ON vehicles(qr_token);
CREATE INDEX idx_vehicles_garage ON vehicles(garage_id);

-- Alertas
CREATE INDEX idx_alerts_active ON alert_rules(vehicle_id, is_active) WHERE is_active = true;

-- Placas temporales
CREATE INDEX idx_temp_plates_expiry ON temporary_plates(expires_at);

-- Notificaciones
CREATE INDEX idx_notifications_unread ON notifications(user_id, read_at) WHERE read_at IS NULL;
```

---

## 🔄 Migraciones

```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2024_07_01_000000_create_vehicle_imports_table.php
├── 2024_07_01_000001_create_garages_table.php
├── 2024_07_01_000002_create_vehicles_table.php
├── 2024_07_01_000003_create_vehicle_specs_table.php
├── 2024_07_01_000004_create_listings_table.php
├── 2024_07_01_000005_create_documents_table.php
├── 2024_07_01_000006_create_maintenance_tables.php
├── 2024_07_01_000007_create_alert_rules_table.php
├── 2024_07_01_000008_create_marketplace_tables.php
├── 2024_07_01_000009_add_garage_id_to_vehicles.php
└── ... (15+ migraciones)
```

---

## 🧪 Seeders

```bash
php artisan db:seed
```

Crea:
- `MaintenanceIntervalSeeder` - Intervalos estándar
- `RolesAndPermissionsSeeder` - Roles y permisos base
- `DemoUserSeeder` (solo local) - Usuario demo

---

## 📊 Vistas SQL (opcional)

```sql
-- Vista de vehículos con su valoración
CREATE VIEW vehicles_with_valuation AS
SELECT
    v.*,
    val.estimated_value,
    val.confidence_score
FROM vehicles v
LEFT JOIN LATERAL (
    SELECT * FROM valuations
    WHERE vehicle_id = v.id
    ORDER BY calculated_at DESC
    LIMIT 1
) val ON true;
```
