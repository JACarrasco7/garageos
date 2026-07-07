# Módulo Identity

> Usuarios, autenticación y garajes (multi-tenant).

---

## 📋 Responsabilidades

- Registro y autenticación (Laravel Breeze)
- Multi-tenant: un usuario puede tener varios garajes
- Roles y permisos (Spatie Permission)
- Suscripciones (Stripe Cashier)
- API tokens (Sanctum)

---

## 📁 Estructura

```
app/Modules/Identity/
├── Models/
│   ├── Garage.php
│   └── User.php (extiende App\Models\User)
├── Providers/
│   └── IdentityServiceProvider.php
└── Policies/
```

---

## 🗄️ Modelos

### Garage
```php
- id
- user_id (FK, owner)
- name (string)
- type (enum: personal, professional, taller, dealership)
- address (string, nullable)
- city (string, nullable)
- postal_code (string, nullable)
- country (string, default 'ES')
- phone (string, nullable)
- email (string, nullable)
- is_default (boolean)
- created_at, updated_at
```

### User (extendido)
```php
- (campos estándar Laravel)
- stripe_id (string)
- card_brand, card_last_four
- trial_ends_at
- subscription_type (enum: free, pro, business)
- (relaciones de Spatie Permission)
```

---

## 👥 Roles Predefinidos

| Rol | Permisos |
|-----|----------|
| `owner` | Todo sobre sus vehículos y garajes |
| `mechanic` | Leer/escribir mantenimientos, sin borrar |
| `viewer` | Solo lectura, comparte QR |
| `admin` | Gestión de plataforma |

---

## 💳 Suscripciones (Stripe Cashier)

| Plan | Precio | Características |
|------|--------|-----------------|
| Free | 0€ | 1 vehículo, 3 alertas |
| Pro | 9.99€/mes | 5 vehículos, alertas ilimitadas, OCR facturas |
| Business | 29.99€/mes | Vehículos ilimitados, multi-usuario, API |

Configurable vía `config/subscription.php`.

---

## 🌐 API Endpoints

```http
# Auth
POST   /register
POST   /login
POST   /logout
POST   /forgot-password
POST   /reset-password

# Garages
GET    /garages
POST   /garages
PUT    /garages/{id}
DELETE /garages/{id}
POST   /garages/{id}/set-default

# Subscription
GET    /billing
POST   /billing/subscribe
POST   /billing/cancel
GET    /billing/invoices
```

---

## 🔐 Multi-tenant

Cada `Vehicle`, `Document`, etc. pertenece a un `Garage`. Todas las queries filtran por `garage_id` del usuario autenticado:

```php
// Scope global automático
public function boot(): void
{
    Vehicle::addGlobalScope('garage', function (Builder $builder) {
        $builder->whereHas('garage', function ($q) {
            $q->where('user_id', auth()->id());
        });
    });
}
```
