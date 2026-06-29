# Parte I — GarageOS Personal (Plan Original)

> Versión compacta para referencia. El documento original completo vive en [docs/legacy/GARAGEOS-PLAN.md](../legacy/GARAGEOS-PLAN.md).

## Qué era GarageOS Personal

App de gestión de garaje personal: vehículos, documentos, mantenimiento, alertas, informes de venta por QR. Se integra como sección **"Mi garaje"** dentro de GarageOS v2.

## Stack definitivo

| Capa | Tecnología | Versión | Por qué |
|---|---|---|---|
| Runtime | PHP | 8.4 | Fibers, lazy objects, property hooks, JIT |
| Backend | Laravel | 12.x | Monolito modular, ORM, colas |
| Frontend | Vue | 3.5 | Composition API, Vapor mode |
| Bridge | Inertia.js | 2.x | SPA sin API boilerplate, SSR nativo |
| Tipado | TypeScript | 5.8 | Tipado estricto, decorators |
| Estilos | Tailwind CSS | 4.x | Motor Oxide (Rust), CSS-first config |
| BD | MySQL | 8.4 | JSON, InnoDB optimizado |
| Cache / Queues | Redis | 7.x | Colas de alertas, OCR |
| Mobile | Capacitor | 7.x | iOS + Android |
| Auth | Laravel Sanctum | 4.x | API tokens + SPA |
| Scaffolding | Breeze | 2.x | Auth + Inertia + Vue + TS |

> **Decisión arquitectónica:** monolito modular primero. Misma separación lógica que microservicios, sin el coste operacional hasta tener usuarios.

## Módulos existentes (`app/Modules/`)

- **Identity** — `User`, `Garage`, auth Breeze
- **Vehicle** — `Vehicle`, `VehicleSpec`, `KmHistory`, QR por vehículo
- **Documents** — upload + OCR de facturas (Tesseract / AWS Textract)
- **Maintenance** — `MaintenanceEntry`, `Workshop`, `MaintenanceInterval`, `ServicePack`
- **Alerts** — `AlertRule`, `Notification`, cron `EvaluateAlertsCommand`
- **Marketplace** — informes de venta (`SaleReport`, `MarketValue`) con QR público

## Schema principal (resumen)

```sql
-- Núcleo
users, garages, vehicles, vehicle_specs, km_history,
documents, workshops, maintenance_entries, maintenance_intervals, service_packs,
alert_rules, notifications, sale_reports, market_values
```

> Para el schema completo y detallado, ver [docs/legacy/GARAGEOS-PLAN.md](../legacy/GARAGEOS-PLAN.md).

## Roadmap Parte I

- [x] **Fase 0** — Setup Laravel 12 + Inertia + Breeze + Capacitor + Pint/Larastan/Pest
- [x] **Fase 1** — Identity + Vehicle + Documents + Maintenance + Alerts (4 semanas)
- [x] **Fase 2** — Marketplace informes + stats + OCR + push (3-4 semanas)
- [x] **Fase 3** — Stripe subscriptions + panel taller + service packs + Octane (continuo)

## Lo único que cambia en v2

- `User` gana soporte multi-rol (buyer + seller + provider simultáneo) → ver [parte-2/fases/fase-0-setup-multirol.md](./parte-2/fases/fase-0-setup-multirol.md).
- El módulo `Marketplace` de informes se mantiene tal cual; **no confundir** con el nuevo módulo `Providers` (servicios profesionales).
