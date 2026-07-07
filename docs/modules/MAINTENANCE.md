# Módulo Maintenance

> Service Packs, intervalos de mantenimiento y recomendaciones automáticas.

---

## 📋 Responsabilidades

- Service Packs predefinidos (aceite, filtros, revisión completa, etc.)
- Intervalos de mantenimiento estándar por tipo
- Recomendación automática según km/fecha del vehículo
- Integración con Marketplace (enlaces de afiliados)

---

## 📁 Estructura

```
app/Modules/Maintenance/
├── Models/
│   ├── MaintenanceInterval.php
│   ├── MaintenanceEntry.php
│   └── ServicePack.php
├── Actions/
│   ├── RecommendServicePackAction.php
│   └── RegisterMaintenanceAction.php
├── Content/                       # Documentación por service pack
└── Providers/
```

---

## 🗄️ Modelos

### ServicePack
```php
- id
- name (string)            # "Aceite básico", "Revisión completa"
- maintenance_type (string) # aceite, filtros, distribucion, completo
- items (json)             # Lista de items incluidos
- estimated_time_hours (decimal)
- difficulty (enum: easy, medium, hard)
```

### MaintenanceInterval
```php
- id
- maintenance_type (string) # aceite, filtros, etc
- km_interval (int)         # Cada X km
- months_interval (int)     # Cada X meses
- description (text)
```

### MaintenanceEntry
```php
- id
- vehicle_id (FK)
- type (string)             # aceite, filtros, etc
- title (string)
- description (text, nullable)
- service_date (date)
- km_at_service (int)
- cost (decimal)
- workshop_name (string, nullable)
- invoice_number (string, nullable)
```

---

## 🎯 Service Packs Predefinidos

| Pack | Tipo | Intervalo | Items |
|------|------|-----------|-------|
| Aceite básico | aceite | 10.000 km / 12 meses | Aceite 5W30, Filtro aceite |
| Filtros | filtros | 20.000 km / 24 meses | Filtro aire, filtro habitáculo |
| Distribución | distribucion | 120.000 km / 5 años | Kit correa distribución |
| Revisión completa | completo | 30.000 km / 24 meses | Todos los anteriores + bujías |
| Pastillas freno | frenos | 40.000 km | Pastillas delanteras/traseras |
| Líquido frenos | frenos | 60.000 km / 24 meses | DOT 4 |

---

## 🌐 API Endpoints

```http
# Público (vía QR token)
GET /api/v1/vehicles/{qr_token}/service-pack/{type}

# Autenticado
GET    /maintenance/intervals
GET    /maintenance/service-packs
POST   /vehicles/{id}/maintenance
GET    /vehicles/{id}/maintenance
```

---

## 🔗 Relaciones

```
ServicePack
└── (sin relaciones directas)

MaintenanceEntry
├── belongsTo Vehicle
└── (registros de servicios realizados)

MaintenanceInterval
└── (tabla de configuración, sin relaciones)
```

---

## 🤖 Recomendación Automática

`RecommendServicePackAction` analiza:
- Km actuales del vehículo
- Fecha del último servicio
- Tipo de mantenimiento más reciente
- Intervalos definidos en `MaintenanceInterval`

Devuelve el siguiente pack recomendado + enlaces de afiliados (AffiliateService).
