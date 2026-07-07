# Módulo Marketplace

> Compraventa de vehículos, valoraciones automáticas y transacciones B2C/B2B.

---

## 📋 Responsabilidades

- Listings (anuncios) de vehículos en venta
- Valoración automática de mercado (scraping + algoritmo)
- Búsqueda full-text con PostgreSQL `tsvector`
- Transacciones (compra/venta) con escrow
- Sistema de ofertas y negociación
- Comisiones para la plataforma (Stripe Connect)

---

## 📁 Estructura

```
app/Modules/Marketplace/
├── Models/
│   ├── MarketplaceListing.php
│   ├── Transaction.php
│   ├── Valuation.php
│   └── Offer.php
├── Actions/
│   ├── ScrapeMarketValueAction.php
│   ├── CreateListingAction.php
│   ├── CalculateScoreAction.php
│   └── GenerateCertificateAction.php
├── Services/
│   └── AffiliateService.php
├── Events/
│   └── OfferReceived.php
├── Http/Controllers/
│   ├── MarketplaceController.php
│   └── TransactionController.php
├── Policies/
│   ├── MarketplaceListingPolicy.php
│   └── TransactionPolicy.php
└── Providers/
```

---

## 🗄️ Modelos

### MarketplaceListing
```php
- id
- vehicle_id (FK)
- seller_id (FK)
- price (decimal)
- currency (string, default EUR)
- status (enum: draft, active, reserved, sold, expired)
- description (text)
- location (string)
- images (json)
- views_count (int, default 0)
- created_at, updated_at, expires_at
```

### Transaction
```php
- id
- listing_id (FK)
- buyer_id (FK)
- seller_id (FK)
- amount (decimal)
- platform_fee (decimal)         # 2.5% por defecto
- stripe_payment_intent_id (string)
- status (enum: pending, paid, completed, refunded, cancelled)
- completed_at (timestamp, nullable)
```

### Valuation
```php
- id
- vehicle_id (FK, nullable)
- brand, model, year (string, int)
- mileage_km (int)
- fuel_type (string)
- power_hp (int)
- estimated_value (decimal)
- confidence_score (decimal 0-1)
- source (enum: algorithm, manual, market)
- calculated_at (timestamp)
```

---

## 🔍 Búsqueda Full-Text

PostgreSQL `tsvector` con stemmer español:

```sql
SELECT * FROM marketplace_listings
WHERE search_vector @@ plainto_tsquery('spanish', 'BMW 320 diesel')
ORDER BY ts_rank(search_vector, query) DESC;
```

---

## 💰 Comisiones

| Tipo | Comisión |
|------|----------|
| Venta estándar | 2.5% |
| Venta destacada | 5% |
| Suscriptores Pro | 1.5% |

Configurable vía `STRIPE_PLATFORM_FEE_PERCENT` en `.env`.

---

## 🌐 API Endpoints

```http
# Público
GET    /api/v1/marketplace/search?q=...
GET    /api/v1/marketplace/featured

# Autenticado
GET    /marketplace/my-listings
POST   /marketplace/listings
POST   /marketplace/listings/{id}/offers
POST   /marketplace/transactions/{id}/pay
POST   /marketplace/transactions/{id}/complete
```

---

## 📊 Eventos

- `OfferReceived` → Notifica al vendedor
- `TransactionCompleted` → Genera factura B2B, dispara webhook Stripe

---

## 🧪 Tests

- Búsqueda full-text
- Cálculo de comisiones
- Flujo completo de transacción
