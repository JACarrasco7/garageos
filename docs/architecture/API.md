# API REST

> Endpoints REST de GarageOS para acceso externo (QR, API pública, integraciones).

---

## 🌐 Base URL

```
http://localhost:8000/api/v1
```

---

## 🔑 Autenticación

### Sanctum (API tokens)
```http
Authorization: Bearer {token}
```

### Generar token (login)
```http
POST /api/v1/auth/login
{
  "email": "user@example.com",
  "password": "secret"
}
```

Response:
```json
{
  "user": { ... },
  "token": "1|abc123..."
}
```

---

## 🚗 Vehicle API

### Decodificar VIN
```http
GET /api/v1/vehicles/decode-vin/{vin}
```

Response:
```json
{
  "make": "BMW",
  "model": "320i",
  "year": 2019,
  "engine": "2.0L",
  "fuel_type": "gasoline",
  "transmission": "automatic",
  "drivetrain": "RWD"
}
```

### Obtener vehículo por QR token (público)
```http
GET /api/v1/vehicles/{qr_token}
```

Response:
```json
{
  "plate": "1234ABC",
  "brand": "Toyota",
  "model": "Corolla",
  "year": 2020,
  "current_km": 45000,
  "specs": {
    "engine_cc": 1600,
    "power_hp": 120
  },
  "documents": [...],
  "maintenance": [...]
}
```

### Service Pack recomendado
```http
GET /api/v1/vehicles/{qr_token}/service-pack/{type}
```

`type`: aceite | filtros | distribucion | completo

---

## 🛒 Marketplace API

### Búsqueda de listings
```http
GET /api/v1/marketplace/search?q=BMW+320&min_price=5000&max_price=20000
```

Parámetros:
- `q` - Query full-text
- `min_price`, `max_price` - Rango de precio
- `brand`, `model` - Filtros exactos
- `year_from`, `year_to` - Rango de año
- `km_max` - Km máximo
- `location` - Ciudad/CP
- `page` - Paginación

### Listings destacados
```http
GET /api/v1/marketplace/featured?limit=10
```

### Detalle de listing
```http
GET /api/v1/marketplace/listings/{id}
```

---

## 📥 Import API

### Portfolio público
```http
GET /api/v1/public/imports/vehicles
GET /api/v1/public/imports/vehicles/{id}
```

---

## 🔐 Auth Endpoints

```http
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
GET    /api/v1/auth/me
```

---

## 📋 Convenciones de respuesta

### Éxito
```json
{
  "data": { ... },
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 142
  }
}
```

### Error
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "plate": ["The plate field is required."]
    }
  }
}
```

### Códigos HTTP
- `200` OK
- `201` Created
- `204` No Content
- `400` Bad Request
- `401` Unauthorized
- `403` Forbidden
- `404` Not Found
- `422` Validation Error
- `429` Too Many Requests
- `500` Server Error

---

## 🧪 Rate Limiting

```php
// routes/api.php
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests per minute
});
```

Endpoints públicos QR tienen rate limit más permisivo: `throttle:300,1`.

---

## 📚 Documentación autogenerada

Con Scribe:
- Genera docs OpenAPI automáticamente
- URL: `/docs` (en dev)

```bash
php artisan scribe:generate
```

---

## 🔄 Versionado

- Versión actual: `v1`
- Breaking changes → nueva versión
- Deprecated endpoints → mantienen 6 meses
