# Módulo Alerts

> Reglas de alertas automáticas para vehículos (ITV, seguro, mantenimiento, etc).

---

## 📋 Responsabilidades

- CRUD de reglas de alerta por vehículo
- Evaluación periódica (cron diario)
- Notificaciones in-app, email, push
- Tipos: km, fecha, días restantes

---

## 📁 Estructura

```
app/Modules/Alerts/
├── Models/
│   └── AlertRule.php
├── Events/
│   └── AlertTriggered.php
├── Listeners/
│   ├── CreateDefaultAlertRules.php
│   └── EvaluateMaintenanceAlerts.php
├── Jobs/
│   └── EvaluateAlertsJob.php
├── Console/
│   └── EvaluateAlertsCommand.php
└── Providers/
```

---

## 🗄️ Modelo AlertRule

```php
- id
- vehicle_id (FK)
- type (enum: km, date, days_before, custom)
- target_type (enum: itv, insurance, maintenance, matriculation, custom)
- threshold_value (int)     # km o días
- is_active (boolean)
- last_triggered_at (timestamp, nullable)
- notification_channels (json) # ["email", "push", "in_app"]
- created_at, updated_at
```

---

## 🔄 Flujo de Evaluación

```
Cron diario (03:00)
  ↓
EvaluateAlertsJob
  ↓
Para cada AlertRule activa:
  - ¿Condición cumplida?
    - Sí → AlertTriggered event
      - Envía notificación por canal
      - Actualiza last_triggered_at
    - No → skip
```

---

## 🎯 Tipos de Alerta Predefinidas

| Tipo | Default | Trigger |
|------|---------|---------|
| ITV | Cada año desde última ITV | 30 días antes |
| Seguro | Anual | 15 días antes |
| Mantenimiento aceite | 10.000 km | 500 km antes |
| Distribución | 120.000 km | 2.000 km antes |
| Matriculación temporal | 6 meses | 7 días antes |

`CreateDefaultAlertRules` listener crea estas reglas al registrar un vehículo nuevo.

---

## 🌐 API Endpoints

```http
GET    /vehicles/{id}/alerts
POST   /vehicles/{id}/alerts
PUT    /alerts/{id}
DELETE /alerts/{id}
POST   /alerts/{id}/toggle
```

---

## 📅 Schedule (Cron)

```php
// app/Console/Kernel.php
$schedule->command('alerts:evaluate')->dailyAt('03:00');
$schedule->command('alerts:evaluate-search')->weekly();
$schedule->command('plates:check-expiry')->dailyAt('08:00');
```

---

## 🔔 Canales de Notificación

- **Email** - Mail templates
- **Push (FCM)** - Firebase Cloud Messaging
- **In-app** - Bell icon con badge
- **SMS** - Twilio (futuro)
