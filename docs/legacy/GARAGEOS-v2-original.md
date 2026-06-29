# GarageOS v2 — Plataforma de Importación de Vehículos + Marketplace de Servicios

> **Stack:** Laravel 12 + Vue 3.5 + Inertia.js 2 + TypeScript 5.8 + Tailwind CSS 4 + MySQL 8.4 + Redis 7 + PHP 8.4
> **Basado en:** GarageOS Personal (plan original) — se integra como módulo "Mi garaje"
> **Última actualización:** 29 de Junio de 2026
> **Estado:** Plan v2 — reestructurado tras investigación de viabilidad legal/técnica

---

## Por qué este documento reemplaza al plan v2 anterior

El primer borrador del plan v2 (pivote a marketplace de importación) tenía tres asunciones que no se sostienen con los datos reales del mercado. Antes de tocar una línea de código conviene dejarlas escritas, porque cambian el diseño de varios módulos:

1. **AutoUncle no cobra comisión al comprador.** Su negocio real es vender herramientas de generación de leads y analítica a concesionarios — el buscador es gratis para el usuario final y es un imán de tráfico, no la fuente de ingresos. Si GarageOS v2 quiere monetizar por comisión de servicio (revisor, transportista, gestoría), es un modelo de negocio *distinto* al de su propio referente, no una copia.

2. **No existe un atajo gratuito y estable para tener un catálogo masivo de mobile.de/AutoScout24 desde el día 1.**
   - mobile.de tiene una Search-API oficial muy completa (precio, km, fotos, vendedor, geolocalización...) pero requiere autenticación de cuenta de partner — el patrón de acceso que existe documentado (Seller-API) está reservado a concesionarios registrados y Transfer Service Providers, lo que sugiere que el Search-API sigue una lógica de acuerdo comercial similar, no un alta self-service.
   - AutoScout24 es peor: su API pública solo permite *publicar* anuncios, no leerlos. Extraer datos de anuncios existentes solo es posible vía scraping, y su web está protegida con Akamai Bot Manager (fingerprinting TLS/JS, bloqueo agresivo) — nada de "low effort, rate-limit 3s" como decía el borrador anterior.
   - El propio mercado lo confirma: existen empresas (Apify, Scrapfly, Piloterr...) que cobran por scraping de estos portales asumiendo el coste de mantenimiento y el riesgo de bloqueo. Eso es una señal clara de que no es gratis ni trivial.

3. **El wizard de matriculación DE→ES real tiene 5-6 pasos, no 10.** El trámite oficial (DGT + fuentes especializadas) es: comprar → transportar (con placas temporales si hace falta circular) → pasar ITV de importación en España (plazo de 30 días desde la llegada) → liquidar impuestos (IEDMT + IVA/ITP + IVTM) → matricular en la DGT → colocar placas físicas. La homologación solo aplica si falta el Certificado de Conformidad (COC) o el vehículo es anterior a 2002.

**Decisión adoptada para este plan**, validada contigo:

- **Listings**: el catálogo propio nace vacío y crece orgánicamente — cada usuario que pega la URL de un anuncio (mobile.de, AutoScout24, o cualquier portal) lo añade al catálogo de GarageOS. No hay scraping masivo en el MVP. Esto es honesto sobre lo que es viable construir en solitario y sin presupuesto de terceros, y deja la puerta abierta a negociar un feed o pagar un servicio de scraping en una fase posterior, cuando haya tracción que lo justifique.
- **Valuation (tasador)**: combina dos señales — el valor de mercado calculado a partir de los anuncios ya analizados en la plataforma, **y** el coste fiscal real de matricular ese vehículo en España según la tabla oficial de depreciación de Hacienda y los tramos de CO₂ del IEDMT. Esto es un diferenciador real frente a cualquier comparador genérico: no solo "es buen precio", sino "esto es lo que te va a costar tenerlo circulando en España".
- **Import Wizard**: 6 pasos reales, basados en el trámite oficial, no un wizard genérico inventado.
- **Monetización**: comisión por servicio intermediado (inspección, transporte, gestoría), como decidiste. Es un modelo distinto al B2B-leads de AutoUncle, y el plan lo trata como tal — no como un movimiento que pueda derivar del catálogo automáticamente.

---

## Tabla de contenidos

**PARTE I — Fundamentos heredados**
1. [Qué se mantiene del plan original](#qué-se-mantiene-del-plan-original)
2. [Stack y versiones](#stack-y-versiones)

**PARTE II — Arquitectura v2**
3. [Estructura de módulos nuevos](#estructura-de-módulos-nuevos)
4. [Base de datos — Schema completo v2](#base-de-datos--schema-completo-v2)
5. [Event Bus v2](#event-bus-v2)
6. [Paquetes nuevos](#paquetes-nuevos)

**PARTE III — Roadmap por fases**
7. [Fase 0 — Setup multi-rol](#fase-0--setup-multi-rol)
8. [Fase 1 — Listings + búsqueda (MVP)](#fase-1--listings--búsqueda-mvp)
9. [Fase 2 — Tasador fiscal (diferenciador)](#fase-2--tasador-fiscal-diferenciador)
10. [Fase 3 — Providers + marketplace de servicios](#fase-3--providers--marketplace-de-servicios)
11. [Fase 4 — Transactions + messaging](#fase-4--transactions--messaging)
12. [Fase 5 — Import Wizard (6 pasos reales)](#fase-5--import-wizard-6-pasos-reales)
13. [Fase 6 — Pagos con Stripe Connect](#fase-6--pagos-con-stripe-connect)
14. [Fase 7 — Mobile + push](#fase-7--mobile--push)
15. [Fase 8 — Futuro: catálogo masivo (post-tracción)](#fase-8--futuro-catálogo-masivo-post-tracción)

**PARTE IV — Decisiones y riesgos**
16. [Decisiones tomadas](#decisiones-tomadas)
17. [Riesgos identificados](#riesgos-identificados)
18. [Testing strategy v2](#testing-strategy-v2)
19. [MVP mínimo viable — semana 1](#mvp-mínimo-viable--semana-1)
20. [Próximos pasos inmediatos](#próximos-pasos-inmediatos)

---

## Qué se mantiene del plan original

GarageOS Personal **no se descarta** — se integra como sección "Mi garaje" dentro de la plataforma ampliada. Cualquier usuario (comprador, vendedor o profesional) puede tener uno o varios coches registrados con su historial de mantenimiento, documentos y alertas, exactamente como en el plan original. Esto da una razón para que la gente vuelva a la plataforma aunque no esté comprando un coche en ese momento, y conecta de forma natural con el Import Wizard: cuando un import termina, el vehículo importado puede pasar a "Mi garaje" automáticamente.

Se mantienen sin cambios:

- Módulos `Identity`, `Vehicle`, `Documents`, `Maintenance`, `Alerts` — con su schema, sus eventos y sus rutas tal y como están en el plan original.
- El patrón de carpetas por módulo (`Models/Actions/Http/Events/Providers/Tests`).
- La filosofía de monolito modular (sin microservicios hasta que haga falta).
- Los tests ya escritos (50 tests pasando según el estado de junio 2026).

Lo único que cambia de lo existente:

- `User` gana soporte multi-rol (un usuario puede ser comprador, vendedor y profesional a la vez) — ver Fase 0.
- El módulo `Marketplace` original (informes de venta con QR) se mantiene tal cual para "Mi garaje", pero no se confunde con el nuevo marketplace de servicios profesionales (que vive en el módulo `Providers`). Son cosas distintas con nombres parecidos — hay que vigilar la nomenclatura en el código para no mezclarlas.

---

## Stack y versiones

Sin cambios respecto al plan original — Laravel 12, Vue 3.5, Inertia 2, TypeScript 5.8, Tailwind 4, MySQL 8.4, Redis 7, PHP 8.4. Los módulos nuevos siguen exactamente el mismo patrón de carpetas y convenciones que los módulos existentes, así que no hay curva de aprendizaje adicional de stack — es la misma base de código creciendo.

Paquetes adicionales específicos de v2 se detallan en la sección [Paquetes nuevos](#paquetes-nuevos).

---

## Estructura de módulos nuevos

Se añaden 6 módulos nuevos bajo `app/Modules/`, siguiendo el mismo patrón que los existentes:

```
garageos/
├── app/
│   └── Modules/
│       ├── Identity/          [existente — sin cambios]
│       ├── Vehicle/           [existente — sin cambios]
│       ├── Documents/         [existente — sin cambios]
│       ├── Maintenance/       [existente — sin cambios]
│       ├── Alerts/            [existente — sin cambios]
│       ├── Marketplace/       [existente — informes de venta "Mi garaje", sin cambios]
│       │
│       ├── Listings/                          ← NUEVO — núcleo del MVP
│       │   ├── Models/                         Listing, ListingSource, SearchAlert, Favorite
│       │   ├── Actions/                        AnalyzeListingUrlAction, CreateSearchAlertAction
│       │   ├── Parsers/                        OpenGraphParser, JsonLdParser, MobileDeParser, AutoScout24Parser
│       │   ├── Http/
│       │   │   ├── Controllers/                ListingController, AnalyzeController, FavoriteController
│       │   │   ├── Requests/                   AnalyzeListingRequest, StoreSearchAlertRequest
│       │   │   └── Resources/                  ListingResource
│       │   ├── Events/                         ListingAnalyzed, ListingCreated
│       │   ├── Jobs/                           EvaluateSearchAlertsJob, RefreshListingDataJob
│       │   ├── Console/                        EvaluateSearchAlertsCommand (schedule cada 6h)
│       │   ├── Providers/                      ListingsServiceProvider
│       │   └── Tests/
│       │
│       ├── Valuations/                         ← NUEVO — tasador fiscal (diferenciador)
│       │   ├── Models/                         Valuation, IedmtBracket, DepreciationTable
│       │   ├── Actions/                        CalculateMarketValueAction, CalculateImportCostAction
│       │   ├── Data/                           iedmt-brackets.php, depreciation-table.php (seeders de datos oficiales)
│       │   ├── Http/
│       │   │   ├── Controllers/                ValuationController
│       │   │   └── Resources/                  ValuationResource
│       │   ├── Events/                         ValuationCalculated
│       │   ├── Jobs/                           RecalculateValuationsJob
│       │   ├── Providers/                      ValuationsServiceProvider
│       │   └── Tests/
│       │
│       ├── Providers/                          ← NUEVO — marketplace de servicios (extiende Workshop)
│       │   ├── Models/                         ServiceProvider, ProviderService, ProviderAvailability, ProviderReview
│       │   ├── Actions/                        OnboardProviderAction, ApproveProviderAction, BookSlotAction
│       │   ├── Http/
│       │   │   ├── Controllers/                ProviderOnboardingController, ProviderServiceController, ProviderSearchController
│       │   │   ├── Requests/                   OnboardProviderRequest, BookSlotRequest
│       │   │   └── Resources/                  ProviderResource, ProviderServiceResource
│       │   ├── Events/                         ProviderOnboarded, ProviderApproved, SlotBooked
│       │   ├── Providers/                      ProvidersServiceProvider
│       │   └── Tests/
│       │
│       ├── Transactions/                       ← NUEVO
│       │   ├── Models/                         Transaction, TransactionItem
│       │   ├── Actions/                         CreateOfferAction, AcceptOfferAction, OpenDisputeAction
│       │   ├── Http/
│       │   │   ├── Controllers/                TransactionController, DisputeController
│       │   │   └── Resources/                  TransactionResource
│       │   ├── Events/                         OfferMade, OfferAccepted, TransactionPaid, DisputeOpened
│       │   ├── Providers/                      TransactionsServiceProvider
│       │   └── Tests/
│       │
│       ├── Messaging/                          ← NUEVO
│       │   ├── Models/                         Conversation, Message, MessageAttachment
│       │   ├── Actions/                         SendMessageAction, StartConversationAction
│       │   ├── Http/
│       │   │   ├── Controllers/                ConversationController, MessageController
│       │   │   └── Resources/                  ConversationResource, MessageResource
│       │   ├── Events/                         MessageSent
│       │   ├── Providers/                      MessagingServiceProvider
│       │   └── Tests/
│       │
│       └── Import/                             ← NUEVO — wizard de matriculación (6 pasos reales)
│           ├── Models/                         ImportCase, ImportDocument, TemporaryPlate
│           ├── Actions/                         CreateImportCaseAction, AdvanceImportStepAction, UploadImportDocumentAction
│           ├── Enums/                           ImportStep (PURCHASE, TRANSPORT, ITV_INSPECTION, TAXES, DGT_REGISTRATION, PLATES)
│           ├── Content/                         markdown por paso (es/en/ca), editable sin deploy
│           ├── Http/
│           │   ├── Controllers/                ImportCaseController, ImportDocumentController
│           │   └── Resources/                  ImportCaseResource
│           ├── Events/                          ImportStepCompleted, TemporaryPlateExpiringSoon
│           ├── Jobs/                            CheckTemporaryPlateExpiryJob
│           ├── Console/                         CheckTemporaryPlateExpiryCommand (schedule diario)
│           ├── Providers/                       ImportServiceProvider
│           └── Tests/
│
├── resources/js/
│   ├── Layouts/
│   │   ├── AppLayout.vue              [existente, renombrado conceptualmente a "OwnerLayout" en uso]
│   │   ├── BuyerLayout.vue            ← NUEVO
│   │   ├── ProviderLayout.vue         ← NUEVO
│   │   └── PublicLayout.vue           [existente]
│   ├── Components/
│   │   ├── listings/                  ← NUEVO — ListingCard, FilterPanel, PriceBadge, AnalyzeUrlForm
│   │   ├── valuations/                ← NUEVO — ValuationBreakdown, ImportCostEstimate
│   │   ├── providers/                 ← NUEVO — ProviderCard, AvailabilityCalendar, ReviewStars
│   │   └── import/                    ← NUEVO — ImportStepper, DocumentChecklist, PlateExpiryBanner
│   ├── Pages/
│   │   ├── Listings/                  Search.vue, Show.vue, Analyze.vue
│   │   ├── Valuations/                Detail.vue
│   │   ├── Providers/                 Onboarding.vue, Dashboard.vue, PublicSearch.vue, PublicShow.vue
│   │   ├── Transactions/              Show.vue, MyOffers.vue
│   │   ├── Messaging/                 Inbox.vue, Conversation.vue
│   │   └── Import/                    Wizard.vue, CaseList.vue
│   └── Types/
│       ├── listing.ts, valuation.ts, provider.ts, transaction.ts, message.ts, importCase.ts
│
└── routes/modules/
    ├── listings.php
    ├── valuations.php
    ├── providers.php
    ├── transactions.php
    ├── messaging.php
    └── import.php
```

---

## Base de datos — Schema completo v2

### Módulo: Identity (extensión)

```sql
-- Tabla nueva: roles múltiples por usuario (un usuario puede ser buyer + seller + provider a la vez)
CREATE TABLE user_roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    role        ENUM('buyer', 'seller', 'provider', 'admin') NOT NULL,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_user_role (user_id, role)
);
```

> Nota: `users.role` (el enum original `owner/workshop/admin`) se deja intacto para no romper el módulo Identity existente; `user_roles` es la fuente de verdad para el nuevo sistema multi-rol del marketplace. El helper `$user->hasRole('provider')` consulta esta tabla.

### Módulo: Listings

```sql
CREATE TABLE listings (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_by_user_id  BIGINT UNSIGNED NOT NULL,
    source_url          VARCHAR(500) NOT NULL,
    source_portal       ENUM('mobile_de', 'autoscout24', 'other') NOT NULL DEFAULT 'other',
    source_listing_id   VARCHAR(100) NULL COMMENT 'ID del anuncio en el portal origen, si se pudo extraer',
    brand               VARCHAR(50) NOT NULL,
    model               VARCHAR(80) NOT NULL,
    model_description   VARCHAR(200) NULL,
    year                YEAR NULL,
    mileage_km          INT UNSIGNED NULL,
    fuel_type           ENUM('gasolina','diesel','hibrido','electrico','glp','otro') NULL,
    power_hp            INT NULL,
    co2_emissions       INT NULL COMMENT 'g/km, clave para el tasador fiscal',
    gearbox             ENUM('manual','automatico') NULL,
    price_eur           DECIMAL(10,2) NULL,
    country             VARCHAR(2) DEFAULT 'DE' COMMENT 'ISO-3166-1 alpha-2 del país de origen',
    seller_type         ENUM('dealer','private') NULL,
    seller_name         VARCHAR(150) NULL,
    seller_location     VARCHAR(150) NULL,
    photos              JSON NULL COMMENT 'array de URLs de imágenes extraídas',
    raw_extracted_data  JSON NULL COMMENT 'payload completo de OpenGraph/JSON-LD/parser específico, para depurar y reprocesar sin re-fetch',
    extraction_method   ENUM('opengraph','json_ld','portal_parser','manual') NOT NULL,
    extraction_status    ENUM('pending','success','partial','failed') DEFAULT 'pending',
    is_active           BOOLEAN DEFAULT TRUE COMMENT 'el usuario puede archivar listings que ya no le interesan',
    last_checked_at     TIMESTAMP NULL COMMENT 'última vez que se confirmó que el anuncio sigue activo',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_brand_model (brand, model),
    INDEX idx_price (price_eur),
    INDEX idx_source_url (source_url(255)),
    INDEX idx_active (is_active)
);

CREATE TABLE search_alerts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    name            VARCHAR(100) NOT NULL,
    criteria        JSON NOT NULL COMMENT 'brand, model, price_max, year_min, mileage_max, etc',
    last_notified_at TIMESTAMP NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_active (user_id, is_active)
);

CREATE TABLE favorites (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    listing_id  BIGINT UNSIGNED NOT NULL,
    notes       VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_user_listing (user_id, listing_id)
);
```

### Módulo: Valuations (tasador fiscal)

```sql
CREATE TABLE valuations (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id              BIGINT UNSIGNED NOT NULL,
    market_value_estimate   DECIMAL(10,2) NULL COMMENT 'basado en listings comparables ya en la plataforma',
    market_value_confidence ENUM('low','medium','high') DEFAULT 'low' COMMENT 'low si hay <5 comparables',
    original_list_price_es  DECIMAL(10,2) NULL COMMENT 'precio de catálogo original en España, si se conoce o estima',
    fiscal_value            DECIMAL(10,2) NULL COMMENT 'base imponible IEDMT tras aplicar coeficiente de depreciación Hacienda',
    depreciation_coefficient DECIMAL(4,2) NULL COMMENT 'ej. 0.47 para un vehículo de 5 años',
    iedmt_rate              DECIMAL(5,4) NULL COMMENT '0, 0.0475, 0.0975 o 0.1475 según CO2',
    iedmt_amount            DECIMAL(10,2) NULL,
    estimated_iva_or_itp    DECIMAL(10,2) NULL COMMENT 'IVA 21% si nuevo/intracomunitario, ITP si usado entre particulares',
    estimated_transport_cost DECIMAL(10,2) NULL,
    estimated_itv_cost      DECIMAL(10,2) NULL,
    estimated_dgt_fees      DECIMAL(10,2) NULL COMMENT 'tasa 1.050 + placas, ronda los 100-140€ fijos',
    estimated_total_landed_cost DECIMAL(10,2) NULL COMMENT 'precio anuncio + todos los costes de importación, la cifra clave para decidir',
    calculated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    INDEX idx_listing (listing_id)
);

-- Datos de referencia oficiales (seeders, no se editan desde la UI)
CREATE TABLE iedmt_brackets (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    co2_min     INT NOT NULL COMMENT 'g/km, inclusive',
    co2_max     INT NULL COMMENT 'g/km, inclusive, NULL = sin tope superior',
    rate        DECIMAL(5,4) NOT NULL COMMENT '0.0000, 0.0475, 0.0975, 0.1475',
    valid_from  DATE NOT NULL COMMENT 'los tramos pueden cambiar por ley de presupuestos, versionar por fecha'
);

CREATE TABLE depreciation_table (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    age_years_min   TINYINT UNSIGNED NOT NULL,
    age_years_max   TINYINT UNSIGNED NULL,
    coefficient     DECIMAL(4,2) NOT NULL COMMENT 'ej. 0.84 para <1 año, 0.28 para 8+ años, tabla Hacienda Anexo IV',
    valid_from      DATE NOT NULL
);
```

### Módulo: Providers (marketplace de servicios)

```sql
-- Extiende la tabla `workshops` existente del plan original con columnas nuevas
ALTER TABLE workshops
    ADD COLUMN provider_type ENUM('inspector','transporter','gestoria','taller') NULL AFTER user_id,
    ADD COLUMN bio TEXT NULL,
    ADD COLUMN stripe_account_id VARCHAR(100) NULL,
    ADD COLUMN onboarding_status ENUM('pending','docs_submitted','approved','rejected') DEFAULT 'pending',
    ADD COLUMN approved_at TIMESTAMP NULL,
    ADD COLUMN approved_by BIGINT UNSIGNED NULL,
    ADD COLUMN rating_avg DECIMAL(3,2) DEFAULT NULL,
    ADD COLUMN rating_count INT UNSIGNED DEFAULT 0,
    ADD FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

CREATE TABLE provider_services (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id     BIGINT UNSIGNED NOT NULL COMMENT 'FK a workshops, que ahora hace de tabla "provider"',
    type            ENUM(
                        'pre_purchase_inspection','transport_de_es','homologation',
                        'gestoria_dgt','gestoria_hacienda','itv_assistance','other'
                    ) NOT NULL,
    title           VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    price_eur       DECIMAL(10,2) NOT NULL,
    price_type      ENUM('fixed','from','quote_required') DEFAULT 'fixed',
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    INDEX idx_workshop_type (workshop_id, type)
);

CREATE TABLE provider_availabilities (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id     BIGINT UNSIGNED NOT NULL,
    provider_service_id BIGINT UNSIGNED NULL COMMENT 'NULL = disponibilidad general del proveedor',
    starts_at       DATETIME NOT NULL,
    ends_at         DATETIME NOT NULL,
    is_booked       BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_service_id) REFERENCES provider_services(id) ON DELETE CASCADE,
    INDEX idx_workshop_date (workshop_id, starts_at),
    INDEX idx_booked (is_booked)
);

CREATE TABLE provider_reviews (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id     BIGINT UNSIGNED NOT NULL,
    transaction_id  BIGINT UNSIGNED NOT NULL COMMENT 'solo se puede valorar tras una transacción completada',
    user_id         BIGINT UNSIGNED NOT NULL,
    rating          TINYINT UNSIGNED NOT NULL COMMENT '1-5',
    comment         TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_transaction_review (transaction_id)
);
```

### Módulo: Transactions

```sql
CREATE TABLE transactions (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id            BIGINT UNSIGNED NOT NULL,
    listing_id          BIGINT UNSIGNED NULL COMMENT 'NULL si la transacción es solo de un servicio sin coche ligado',
    workshop_id         BIGINT UNSIGNED NULL COMMENT 'proveedor contratado, si aplica',
    type                ENUM('service_booking','vehicle_offer') NOT NULL,
    status              ENUM('pending','accepted','rejected','paid','completed','disputed','refunded','cancelled') DEFAULT 'pending',
    amount_eur          DECIMAL(10,2) NOT NULL,
    platform_fee_eur    DECIMAL(10,2) NULL COMMENT 'comisión calculada, application_fee_amount de Stripe Connect',
    stripe_payment_intent_id VARCHAR(100) NULL,
    notes               TEXT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE SET NULL,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    INDEX idx_buyer_status (buyer_id, status),
    INDEX idx_workshop_status (workshop_id, status)
);

CREATE TABLE transaction_items (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id  BIGINT UNSIGNED NOT NULL,
    provider_service_id BIGINT UNSIGNED NULL,
    provider_availability_id BIGINT UNSIGNED NULL COMMENT 'slot reservado, si aplica',
    description     VARCHAR(255) NOT NULL,
    quantity        SMALLINT UNSIGNED DEFAULT 1,
    unit_price_eur  DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_service_id) REFERENCES provider_services(id) ON DELETE SET NULL,
    FOREIGN KEY (provider_availability_id) REFERENCES provider_availabilities(id) ON DELETE SET NULL
);
```

### Módulo: Messaging

```sql
CREATE TABLE conversations (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id      BIGINT UNSIGNED NULL,
    transaction_id  BIGINT UNSIGNED NULL,
    last_message_at TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE SET NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    INDEX idx_last_message (last_message_at)
);

CREATE TABLE conversation_participants (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    last_read_at    TIMESTAMP NULL,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_conversation_user (conversation_id, user_id)
);

CREATE TABLE messages (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_id       BIGINT UNSIGNED NOT NULL,
    body            TEXT NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation_date (conversation_id, created_at)
);

CREATE TABLE message_attachments (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id  BIGINT UNSIGNED NOT NULL,
    file_path   VARCHAR(255) NOT NULL,
    mime_type   VARCHAR(50) NULL,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
);
```

### Módulo: Import (wizard de matriculación — 6 pasos reales)

```sql
CREATE TABLE import_cases (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             BIGINT UNSIGNED NOT NULL,
    listing_id          BIGINT UNSIGNED NULL,
    vehicle_id          BIGINT UNSIGNED NULL COMMENT 'se rellena al completar el import y pasar el coche a "Mi garaje"',
    origin_country      VARCHAR(2) DEFAULT 'DE',
    current_step        ENUM('purchase','transport','itv_inspection','taxes','dgt_registration','plates','completed') DEFAULT 'purchase',
    purchase_date       DATE NULL,
    arrival_date        DATE NULL COMMENT 'fecha de llegada a España, dispara el plazo de 30 días para la ITV',
    itv_deadline         DATE NULL COMMENT 'calculado: arrival_date + 30 días',
    needs_homologation   BOOLEAN DEFAULT FALSE COMMENT 'TRUE si no hay COC o el vehículo es anterior a 2002',
    co2_emissions        INT NULL COMMENT 'copiado del listing o introducido manualmente, clave para el IEDMT',
    final_plate_number   VARCHAR(10) NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    INDEX idx_user_step (user_id, current_step)
);

CREATE TABLE import_documents (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    import_case_id  BIGINT UNSIGNED NOT NULL,
    step            ENUM('purchase','transport','itv_inspection','taxes','dgt_registration','plates') NOT NULL,
    type            ENUM(
                        'compraventa','coc','ficha_tecnica_origen','tarjeta_itv_origen',
                        'seguro_transporte','ficha_itv_es','modelo_576','modelo_309_300',
                        'modelo_itp','justificante_ivtm','permiso_circulacion','otro'
                    ) NOT NULL,
    file_path       VARCHAR(255) NOT NULL,
    is_verified     BOOLEAN DEFAULT FALSE,
    uploaded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_case_id) REFERENCES import_cases(id) ON DELETE CASCADE,
    INDEX idx_case_step (import_case_id, step)
);

CREATE TABLE temporary_plates (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    import_case_id  BIGINT UNSIGNED NOT NULL,
    plate_number    VARCHAR(20) NOT NULL,
    issued_at       DATE NOT NULL,
    expires_at      DATE NOT NULL COMMENT 'normalmente issued_at + 2 meses, prorrogable',
    is_extended     BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_case_id) REFERENCES import_cases(id) ON DELETE CASCADE,
    INDEX idx_expiry (expires_at)
);
```

---

## Event Bus v2

Se añaden a los eventos existentes (Vehicle, Documents, Maintenance, Alerts), siguiendo el mismo patrón de Laravel Events + Listeners con colas Redis:

```
Listings
  ListingAnalyzed       → Listener: ExtractStructuredDataListener (sync, parsea OpenGraph/JSON-LD)
                          Listener: CalculateValuationListener (queued, dispara el tasador automáticamente)
  ListingCreated        → Listener: EvaluateSearchAlertsListener (queued, mira si matchea alertas activas)

Valuations
  ValuationCalculated   → Listener: NotifyFavoritedListeners (queued, si alguien tenía el listing en favoritos)

Providers
  ProviderOnboarded     → Listener: NotifyAdminForReviewListener (sync, email al admin)
  ProviderApproved      → Listener: EnableStripeConnectListener (queued)
  SlotBooked            → Listener: SendBookingConfirmationJob (queued: push + email a ambas partes)

Transactions
  OfferMade             → Listener: NotifyCounterpartyListener (queued)
  OfferAccepted         → Listener: CreateConversationListener (sync, si no existía ya)
  TransactionPaid       → Listener: UnlockProviderContactInfoListener (sync)
                          Listener: ScheduleReviewReminderJob (queued, +3 días tras completed)
  DisputeOpened         → Listener: NotifyAdminMediationListener (sync)

Messaging
  MessageSent           → Listener: SendMessagePushJob (queued)

Import
  ImportStepCompleted   → Listener: AdvanceImportCaseListener (sync)
                          Listener: CheckIfReadyForVehicleSync (sync, al completar 'plates' crea el Vehicle en "Mi garaje")
  TemporaryPlateExpiringSoon → Listener: SendPlateExpiryReminderJob (queued: push + email, 7 días antes)
```

---

## Paquetes nuevos

Solo se listan los paquetes **adicionales** a los del plan original (que se mantienen todos).

### PHP / Composer

| Paquete | Versión | Razón |
|---|---|---|
| `laravel/cashier` | ^15.6 | Stripe Connect para pagos a profesionales (Fase 6) |
| `spatie/laravel-query-builder` | ^6.2 | Filtros de búsqueda de Listings vía query params, URL compartible |
| `symfony/dom-crawler` | ^7.1 | Parseo de HTML para extraer OpenGraph/JSON-LD al pegar una URL |
| `symfony/css-selector` | ^7.1 | Selectores CSS para `dom-crawler` |
| `league/commonmark` | ^2.6 | Renderizado del contenido markdown del Import Wizard |

### NPM — Frontend

No se añaden paquetes nuevos de frontend — `recharts`, `dayjs`, `pinia`, `@vueuse/core` y `vue-sonner` del plan original cubren las necesidades de Listings, Valuations y el wizard de Import (gráficos de coste, fechas de caducidad de placas, estado global del flujo multi-rol, notificaciones toast).

> Nota sobre `AnalyzeListingUrlAction`: NO se usa ningún paquete de scraping pesado (Puppeteer, Playwright) en el MVP. Un `fetch` HTTP simple + `symfony/dom-crawler` para extraer metadatos `<meta property="og:*">` y bloques `<script type="application/ld+json">` es suficiente para una página de anuncio individual — es una operación ligera, puntual y a petición explícita del usuario, muy distinta de un crawler que recorre un catálogo entero sin parar.

---

## Fase 0 — Setup multi-rol

**Duración estimada: 1 semana**

- [ ] Migración `user_roles` + modelo `UserRole`
- [ ] Trait `HasRoles` en `User`: `hasRole('buyer')`, `assignRole('provider')`, `roles()`
- [ ] Middleware `EnsureUserHasRole` para proteger rutas de proveedor/admin
- [ ] Crear estructura de carpetas vacía de los 6 módulos nuevos (`Listings`, `Valuations`, `Providers`, `Transactions`, `Messaging`, `Import`)
- [ ] `BuyerLayout.vue` y `ProviderLayout.vue` — copiar patrón de `AppLayout.vue` existente
- [ ] Sidebar adaptativo con switcher de rol (un dropdown simple: "Comprador" / "Soy profesional")
- [ ] Seeders de datos oficiales: `iedmt_brackets` y `depreciation_table` (los datos del Anexo IV de Hacienda y los tramos de CO₂, para que la Fase 2 no tenga que recopilarlos desde cero)
- [ ] Commit: `git add -A && git commit -m "feat: setup multi-rol + estructura v2"`

**Verificación:**
- Un usuario nuevo puede registrarse como comprador y luego activar el rol de proveedor sin crear una cuenta nueva
- El sidebar cambia de opciones según el rol activo
- `php artisan tinker` → `IedmtBracket::count()` devuelve 4, `DepreciationTable::count()` devuelve las filas del Anexo IV

---

## Fase 1 — Listings + búsqueda (MVP) ⭐

**Duración estimada: 3 semanas**

Esta es la fase que de verdad valida si el producto tiene sentido: ¿la gente quiere pegar enlaces de coches alemanes y ver qué le dice la plataforma?

**Semana 1: Captura del anuncio**

- [ ] Migraciones: `listings`, `search_alerts`, `favorites`
- [ ] `AnalyzeListingUrlAction`:
  1. Valida que la URL pertenece a un dominio conocido (mobile.de, autoscout24.*, o "otro" — no se bloquea, pero se marca el origen)
  2. Hace un único `fetch` HTTP de la página (con User-Agent identificable, ej. `GarageOSBot/1.0 (+https://garageos.app/bot)`, y *sin reintentos agresivos* — si falla, falla, el usuario puede reintentar manualmente)
  3. Extrae `<meta property="og:*">` con `OpenGraphParser`
  4. Si existe, extrae `<script type="application/ld+json">` con `JsonLdParser` (mobile.de y AutoScout24 usan structured data tipo `Product`/`Car` en muchas páginas, esto da campos más fiables que OpenGraph)
  5. Si el dominio es reconocido, aplica un parser específico (`MobileDeParser`, `AutoScout24Parser`) que sabe mapear sus campos concretos (precio, km, año, CO₂) a las columnas de `listings`
  6. Guarda el resultado con `extraction_status` (`success`/`partial`/`failed`) y el payload crudo en `raw_extracted_data` para poder reprocesar sin volver a pedir la página
  7. Dispara el evento `ListingAnalyzed`
- [ ] `Listings/Analyze.vue` — formulario de "pegar enlace", con estado de carga y fallback manual: si la extracción falla o es parcial, se muestra un formulario para que el usuario rellene a mano los campos que falten (precio, km, año, CO₂ como mínimo, porque son los que necesita el tasador de la Fase 2)
- [ ] Listener `CalculateValuationListener` que dispara automáticamente la Fase 2 en cuanto se guarda un listing con los datos mínimos

**Semana 2: Catálogo y búsqueda**

- [ ] `Listings/Search.vue` con filtros (marca, modelo, precio, año, km, combustible) usando `spatie/laravel-query-builder` — URL compartible (`?brand=BMW&price_max=20000`)
- [ ] `Listings/Show.vue` con galería de fotos, datos del vendedor, enlace de vuelta al anuncio original (siempre se mantiene el link al origen — GarageOS nunca pretende ser el vendedor, es un agregador y panel de decisión)
- [ ] `ListingCard.vue`, `PriceBadge.vue` (verde/ámbar/rojo según comparación con `market_value_estimate` cuando exista)
- [ ] Favoritos (`FavoriteController`)

**Semana 3: Alertas**

- [ ] `CreateSearchAlertAction` + `SearchAlerts` UI
- [ ] `EvaluateSearchAlertsJob` — cada 6h compara criterios guardados contra listings nuevos o modificados desde la última ejecución
- [ ] Email de alerta cuando hay match (Laravel Mail)
- [ ] `RefreshListingDataJob` opcional — vuelve a analizar un listing existente para detectar si el anuncio original ya no está disponible (esto es honesto: como no scrapeamos el catálogo entero, no sabemos si un anuncio se vendió a menos que el usuario o un job puntual lo vuelva a comprobar)

**Verificación:**
- Pegar la URL de un anuncio real de mobile.de → aparece en `listings` con marca, modelo, precio, km y CO₂ rellenados (al menos vía OpenGraph, idealmente vía JSON-LD)
- Si la URL es de un portal no reconocido y la extracción falla → se ofrece el formulario manual y el listing se guarda igualmente
- `GET /listings?brand=BMW&price_max=20000` → grid filtrado con URL compartible
- Crear una alerta con criterios → tras pegar un listing nuevo que matchea, el job dispara el email en la siguiente ejecución programada

---

## Fase 2 — Tasador fiscal (diferenciador) ⭐

**Duración estimada: 2 semanas**

Esto es lo que ningún comparador genérico hace bien: decir no solo si el precio es bueno en Alemania, sino **cuánto va a costar realmente tenerlo circulando en España**. Combina dos cálculos independientes:

**Cálculo A — Valor de mercado** (heurística, mejora con el tiempo):
- Compara el listing contra otros listings de la misma marca/modelo/año/km ya analizados en la plataforma
- Si hay menos de 5 comparables, `market_value_confidence = 'low'` y la UI lo deja claro — nunca se presenta una cifra heurística con poco respaldo como si fuera certera
- Este cálculo mejora orgánicamente: cuantos más usuarios pegan anuncios, más comparables hay, más fiable se vuelve. Es la métrica de éxito principal de Listings.

**Cálculo B — Coste fiscal real** (determinista, basado en tablas oficiales):
1. `original_list_price_es`: si no se conoce el precio de catálogo original en España, se estima (campo a completar con datos públicos por marca/modelo a medida que se documenten; de momento puede quedar como introducible a mano por el usuario o el admin)
2. `depreciation_coefficient`: se busca en `depreciation_table` según la antigüedad del vehículo
3. `fiscal_value = original_list_price_es × depreciation_coefficient`
4. `iedmt_rate`: se busca en `iedmt_brackets` según `co2_emissions` del listing (0% / 4,75% / 9,75% / 14,75%)
5. `iedmt_amount = fiscal_value × iedmt_rate`
6. `estimated_iva_or_itp`: 21% sobre el precio si es intracomunitario nuevo/cuasi-nuevo (menos de 6 meses o 6.000 km), o el ITP autonómico correspondiente si es usado comprado a un particular — se modela como un valor configurable por comunidad autónoma, con Extremadura/Andalucía como valores por defecto razonables al inicio
7. Costes fijos: transporte estimado (rango por distancia, no exacto), ITV de importación (~60-80€), tasa DGT (~100€), placas (~30-50€)
8. `estimated_total_landed_cost = price_eur + iedmt_amount + estimated_iva_or_itp + estimated_transport_cost + estimated_itv_cost + estimated_dgt_fees`

- [ ] Migraciones: `valuations`, `iedmt_brackets`, `depreciation_table` (seeders ya creados en Fase 0)
- [ ] `CalculateMarketValueAction` — cálculo A
- [ ] `CalculateImportCostAction` — cálculo B, el núcleo fiscal
- [ ] `ValuationBreakdown.vue` — desglose visual línea a línea (no solo el total: la gente quiere ver de dónde sale cada cifra, especialmente el IEDMT que es el que más sorprende)
- [ ] Badge "precio justo / caro / chollo" en `ListingCard` y `PriceBadge`, basado en cuánto se desvía `estimated_total_landed_cost` del valor de mercado en España (cuando exista referencia)
- [ ] Job semanal `RecalculateValuationsJob` — recalcula valuations existentes si entran nuevos comparables o cambian los tramos fiscales

**Verificación:**
- Un listing con CO₂ = 110g/km → `iedmt_rate = 0`, badge muestra "0€ de impuesto de matriculación"
- Un listing con CO₂ = 180g/km y 7 años de antigüedad → el desglose muestra el coeficiente de depreciación correcto y el tramo del 9,75%
- Cambiar `co2_emissions` de un listing manualmente recalcula la valuation correspondiente

> Limitación que hay que comunicar en la UI sin tapujos: esto es una **estimación**, no una liquidación oficial. El cálculo real de Hacienda puede variar según la comunidad autónoma, el modelo exacto y la documentación aportada. El tasador da una cifra de referencia para decidir si comprar, no un justificante de pago.

---

## Fase 3 — Providers + marketplace de servicios ⭐ habilita revenue

**Duración estimada: 3 semanas**

Aquí entra el dinero. El objetivo es tener inspectores, transportistas y gestorías dados de alta y reservables.

- [ ] Migración que extiende `workshops` con `provider_type`, `bio`, `stripe_account_id`, `onboarding_status`, `rating_avg`, `rating_count`
- [ ] Migraciones: `provider_services`, `provider_availabilities`, `provider_reviews`
- [ ] `OnboardProviderAction` + wizard `/become-provider`:
  1. Tipo de proveedor (inspector / transportista / gestoría / taller)
  2. Datos de contacto y ubicación
  3. Subida de documentación (DNI/NIF, seguro de responsabilidad civil si aplica) vía Spatie MediaLibrary
  4. Queda en `onboarding_status = 'docs_submitted'`
- [ ] `ApproveProviderAction` — **verificación manual por admin** (opción A del análisis original: rápido de implementar, sin coste de Stripe Identity; se reevalúa si el volumen lo justifica más adelante)
- [ ] `/provider/services` — dashboard para crear y editar `ProviderService` (qué ofrece y a qué precio)
- [ ] `AvailabilityCalendar.vue` — calendario simple de slots (no hace falta un motor de calendario complejo en el MVP, basta con franjas de fecha/hora que el proveedor abre manualmente)
- [ ] `/marketplace/services` — búsqueda pública filtrable por tipo de servicio y zona
- [ ] `ReviewStars.vue` + lógica de actualización de `rating_avg`/`rating_count` al guardar una `provider_review`

**Verificación:**
- Onboarding de un proveedor de prueba → queda en `docs_submitted` → admin lo aprueba → estado pasa a `approved` y aparece en el buscador público
- Reservar un slot marca `provider_availabilities.is_booked = true` y no permite doble reserva del mismo slot
- Tras una transacción completada, dejar una review actualiza el rating medio del proveedor

---

## Fase 4 — Transactions + messaging

**Duración estimada: 3 semanas**

- [ ] Migraciones: `transactions`, `transaction_items`, `conversations`, `conversation_participants`, `messages`, `message_attachments`
- [ ] `CreateOfferAction` — un comprador puede contactar a un proveedor para reservar un servicio (`type = 'service_booking'`) ligado opcionalmente a un listing
- [ ] `AcceptOfferAction` — el proveedor confirma, se crea la reserva en `provider_availabilities` y se dispara `OfferAccepted`
- [ ] Chat in-app con `StartConversationAction` y `SendMessageAction` — **polling cada 10s en el MVP**, nada de WebSockets todavía (Laravel Reverb se evalúa en una fase posterior si el volumen de mensajería lo justifica)
- [ ] Adjuntos en mensajes (fotos del coche, documentos) vía `MessageAttachment`
- [ ] `OpenDisputeAction` — marca la transacción como `disputed`, notifica a admin para mediación manual (sin automatizar resolución de disputas en el MVP)

**Verificación:**
- Comprador contacta a un inspector para una revisión pre-compra → se crea `Transaction` con `status = 'pending'` y una `Conversation` asociada
- Proveedor acepta → `status = 'accepted'`, slot reservado, ambas partes reciben notificación
- Abrir una disputa cambia el estado y genera una alerta al panel de admin

---

## Fase 5 — Import Wizard (6 pasos reales) ⭐ diferenciador único

**Duración estimada: 2-3 semanas**

Los 6 pasos reales del trámite DE→ES, según fuentes oficiales (DGT, administracion.gob.es) y guías especializadas contrastadas:

1. **Compra** (`purchase`) — el usuario marca `purchase_date`, sube `compraventa`/factura y, si existe, el `coc` (Certificado de Conformidad Europeo). Si el vehículo es anterior a 2002 o no tiene COC, se marca `needs_homologation = true` y el wizard avisa de que hará falta un ingeniero homologador antes de la ITV.
2. **Transporte** (`transport`) — gestión de cómo llega el coche a España: con placas temporales/de tránsito alemanas si va a circular por carretera, o en plataforma si no. Aquí se sugieren los `provider_services` de tipo `transport_de_es` ya dados de alta en la Fase 3. Al marcar `arrival_date`, el sistema calcula automáticamente `itv_deadline = arrival_date + 30 días` y crea una `AlertRule` para avisar antes de que expire.
3. **ITV de importación** (`itv_inspection`) — checklist de documentación a llevar (ficha técnica origen, tarjeta ITV/TÜV origen, COC u homologación). Se sugieren proveedores `itv_assistance` si el usuario quiere acompañamiento. Al aprobarse, se sube `ficha_itv_es`.
4. **Impuestos** (`taxes`) — el wizard usa la `Valuation` ya calculada en la Fase 2 como punto de partida (mismo cálculo, ahora con los datos reales confirmados tras la compra) y guía la presentación de:
   - Modelo 576 (IEDMT) en la Agencia Tributaria
   - Modelo 309/300 (IVA) si aplica, o modelo de ITP si es compra entre particulares
   - Justificante de IVTM del ayuntamiento de residencia
   Se sugieren proveedores `gestoria_hacienda` para quien no quiera hacerlo por su cuenta.
5. **Matriculación DGT** (`dgt_registration`) — checklist final con todos los justificantes de pago + documentación del vehículo, enlace directo a la sede electrónica de la DGT, sugerencia de `gestoria_dgt` si se prefiere delegar. Al completarse, se guarda `final_plate_number`.
6. **Placas físicas** (`plates`) — paso final, confirmación de que las placas están colocadas. Al completar este paso, `CheckIfReadyForVehicleSync` crea automáticamente un registro en `vehicles` (módulo Vehicle del plan original) con los datos ya conocidos del `import_case`, para que el coche aparezca directamente en "Mi garaje" sin que el usuario tenga que rellenar el formulario de alta otra vez.

- [ ] Migraciones: `import_cases`, `import_documents`, `temporary_plates`
- [ ] Enum `ImportStep` + `AdvanceImportStepAction`
- [ ] Contenido en markdown por paso (ES/EN/CA, reutilizando el patrón i18n ya existente en Aktive) — fácil de actualizar sin deploy si cambia la normativa
- [ ] `ImportStepper.vue` — stepper visual de 6 pasos con checklist de documentos por paso
- [ ] `DocumentChecklist.vue` reutilizando el patrón de subida de `Documents` del plan original
- [ ] `PlateExpiryBanner.vue` + `CheckTemporaryPlateExpiryCommand` (cron diario) — avisa 7 días antes de que caduquen las placas temporales
- [ ] Sugerencia automática de proveedores relevantes en cada paso (transporte en el paso 2, gestoría en los pasos 4-5), enlazando directamente al flujo de `Transactions` de la Fase 3-4

**Verificación:**
- Crear un `ImportCase` desde un listing con `co2_emissions` conocido → el wizard arranca con los datos fiscales ya precalculados
- Marcar `arrival_date` → se crea automáticamente la alerta de los 30 días para la ITV
- Subir documentos por paso → checklist se marca en verde
- Completar el paso `plates` → aparece un nuevo `Vehicle` en "Mi garaje" con los datos del import

---

## Fase 6 — Pagos con Stripe Connect

**Duración estimada: 2 semanas**

- [ ] `composer require laravel/cashier`
- [ ] Onboarding Express de Stripe Connect integrado en `OnboardProviderAction` (se genera `stripe_account_id` tras la aprobación admin)
- [ ] `application_fee_amount` = comisión de plataforma — **8% por defecto**, configurable por tipo de servicio si en el futuro interesa diferenciar (ej. menor comisión en transporte, mayor en gestoría, según márgenes reales de cada vertical)
- [ ] Webhook `payment_intent.succeeded` → marca la `Transaction` como `paid`, dispara `TransactionPaid`
- [ ] Facturación B2B de comisiones a los proveedores (recibo simple, no hace falta un sistema de facturación completo en el MVP)

**Verificación:**
- Un proveedor aprobado completa el onboarding Express de Stripe sin salir de GarageOS
- Un pago de prueba en modo sandbox genera el webhook correcto y actualiza el estado de la transacción
- La comisión calculada coincide con el 8% configurado

---

## Fase 7 — Mobile + push

**Duración estimada: 2 semanas**

- [ ] Capacitor 7 + Firebase Cloud Messaging (reutilizando la configuración ya prevista en el plan original)
- [ ] Push en: nuevas ofertas, mensajes nuevos, alertas de búsqueda con match, recordatorios de caducidad de placas temporales, pasos del import case pendientes
- [ ] PWA instalable además del build nativo Android/iOS (cubre ambas opciones que se dejaron abiertas en el análisis original, sin coste adicional relevante)

**Verificación:**
- Notificación push llega al completar cada uno de los triggers anteriores, tanto en Android como en la PWA

---

## Fase 8 — Futuro: catálogo masivo (post-tracción)

**No forma parte del MVP. Se documenta aquí para no perder el hilo cuando llegue el momento.**

Cuando la plataforma tenga tracción suficiente para justificar el gasto (varios cientos de listings activos generados por usuarios, evidencia de que la gente vuelve a buscar), hay tres caminos para ampliar el catálogo más allá de "pegar URL", de menor a mayor compromiso:

1. **Contratar un servicio de scraping gestionado** (Apify, Scrapfly, Piloterr...) — pagar por resultado, sin mantener infraestructura de scraping propia ni asumir directamente el riesgo de bloqueo. Es el camino más rápido de implementar, pero tiene coste recurrente variable según volumen.
2. **Negociar acceso de partner a la Search-API de mobile.de** — requiere contacto comercial directo con mobile.de GmbH, probablemente con condiciones de volumen mínimo o cuota mensual. Es el camino "correcto" a largo plazo si GarageOS crece, porque da datos limpios y estables sin depender de terceros que scrapean por ti.
3. **Acuerdos de afiliación o redirección** con gestorías/importadoras ya establecidas (tipo NeedCarHelp, Odden Cars, las mismas que aparecen como fuente en la investigación de este plan) que ya tienen flujo de anuncios, a cambio de una comisión por lead — convierte a un competidor potencial en un canal.

Ninguna de las tres se presupuesta ni se diseña en detalle ahora — la decisión correcta depende de métricas que todavía no existen (cuántos listings llegan orgánicamente, qué tasa de conversión a transacción tienen, cuánto margen deja la comisión del 8%).

---

## Decisiones tomadas

- **Catálogo**: nace vacío, crece con cada URL pegada por un usuario. Sin scraping masivo en el MVP (ver Fase 8 para el futuro).
- **Extracción de datos**: `fetch` puntual + OpenGraph/JSON-LD + parsers específicos por portal cuando se reconoce el dominio. Fallback manual si falla.
- **Monetización**: comisión del 8% por servicio intermediado (inspección, transporte, gestoría) vía Stripe Connect — no réplica del modelo B2B-leads de AutoUncle.
- **Tasador**: combina valor de mercado heurístico (mejora con más listings) + coste fiscal determinista (tablas oficiales de Hacienda/IEDMT).
- **Import Wizard**: 6 pasos reales basados en el trámite oficial DGT, no un wizard genérico.
- **Multi-rol**: un usuario puede ser comprador + vendedor + profesional simultáneamente.
- **Países**: empieza con DE→ES (los más relevantes y los mejor documentados en este plan). AT/NL/FR como ampliación futura si la demanda lo justifica.
- **Idioma**: ES en el MVP. La infraestructura i18n de Aktive (ES/EN/CA) ya existe y se puede reutilizar para el contenido del Import Wizard sin gran esfuerzo, pero no es bloqueante para lanzar.
- **Verificación de proveedores**: admin manual con documentos subidos (rápido, sin coste de Stripe Identity). Se reevalúa si el volumen de onboarding lo justifica.
- **Mensajería**: polling 10s en el MVP. WebSockets (Laravel Reverb) solo si el volumen de mensajes lo justifica.
- **Mobile**: PWA + Capacitor en paralelo, sin coste adicional relevante de mantener ambos.

---

## Riesgos identificados

| Riesgo | Impacto | Mitigación |
|---|---|---|
| El catálogo crece muy despacio si no hay usuarios pegando URLs desde el día 1 | Alto — sin listings no hay tasador, sin tasador no hay diferenciador | Lanzar con un puñado de listings sembrados a mano (los propios coches que tú mismo estás mirando para tu compra) para que la plataforma no se vea vacía el primer día |
| `original_list_price_es` (precio de catálogo original) es difícil de obtener para modelos antiguos o poco comunes | Medio — afecta a la precisión del cálculo B del tasador | Permitir introducción manual + marcar visualmente cuándo el dato es estimado vs confirmado |
| Extracción OpenGraph/JSON-LD falla si el portal cambia su HTML | Medio — recurrente, hay que mantenerlo | El fallback manual evita que esto bloquee al usuario; revisar parsers específicos cuando se detecten fallos repetidos |
| Onboarding manual de proveedores no escala si crece mucho el volumen | Bajo a medio plazo | Revisar Stripe Identity u otra automatización cuando el volumen de solicitudes lo justifique |
| Normativa de IEDMT/tramos de CO₂ cambia con leyes de presupuestos | Medio — el tasador daría cifras desactualizadas | Las tablas (`iedmt_brackets`, `depreciation_table`) están versionadas por `valid_from`, revisar anualmente |
| Confusión de nomenclatura entre el módulo `Marketplace` original (informes de venta) y el nuevo marketplace de servicios profesionales | Bajo, pero genera deuda técnica si no se vigila | Nomenclatura clara en código: `Marketplace` solo para informes "Mi garaje", `Providers` para todo lo de servicios profesionales |
| Disputas sin resolución automatizada pueden acumularse si crece el volumen de transacciones | Bajo en el MVP | Panel de mediación admin simple primero, automatizar reglas básicas (reembolso automático si el proveedor no responde en X días) más adelante |

---

## Testing strategy v2

Mismo patrón que el plan original (`Pest.php`, `Unit/Modules/`, `Feature/Modules/`), extendido a los módulos nuevos:

```
tests/
├── Unit/Modules/
│   ├── Listings/Actions/
│   │   ├── AnalyzeListingUrlActionTest.php       # mockea el fetch HTTP, prueba los 3 parsers
│   │   └── OpenGraphParserTest.php
│   ├── Valuations/Actions/
│   │   ├── CalculateMarketValueActionTest.php
│   │   └── CalculateImportCostActionTest.php     # casos límite de cada tramo de CO2
│   ├── Providers/Actions/
│   │   └── OnboardProviderActionTest.php
│   └── Import/Actions/
│       └── AdvanceImportStepActionTest.php
│
├── Feature/Modules/
│   ├── Listings/
│   │   ├── AnalyzeListingTest.php                # casos: éxito, parcial, fallo total
│   │   └── SearchAlertTest.php
│   ├── Valuations/
│   │   └── ValuationTest.php
│   ├── Providers/
│   │   ├── OnboardingTest.php
│   │   └── BookingTest.php
│   ├── Transactions/
│   │   └── TransactionFlowTest.php                # oferta → aceptación → pago → completado
│   ├── Messaging/
│   │   └── ConversationTest.php
│   └── Import/
│       └── ImportWizardTest.php                   # recorre los 6 pasos completos
│
└── Browser/
    └── Listings/
        └── AnalyzeUrlBrowserTest.php
```

Comandos (sin cambios respecto al plan original):

```bash
./vendor/bin/pest
./vendor/bin/pest --coverage --min=80
./vendor/bin/pest --parallel
vendor/bin/phpstan analyse --memory-limit=2G
./vendor/bin/pint
npx vue-tsc --noEmit
```

---

## MVP mínimo viable — semana 1

Si quieres algo enseñable cuanto antes (igual que el plan original tenía su "mañana mismo"), este es el recorte mínimo que ya demuestra la idea central:

1. **Login + registro** (Breeze ya lo da, reutilizado del plan original)
2. **Pegar URL de un anuncio** → extracción OpenGraph básica (sin necesidad todavía de los parsers específicos de mobile.de/AutoScout24, ni del fallback manual completo)
3. **Mostrar el listing guardado** con sus datos básicos (marca, modelo, precio, km, CO₂ si se pudo extraer)
4. **Cálculo del IEDMT en base al CO₂** — aunque sea con `original_list_price_es` introducido a mano y sin el resto de costes fijos todavía
5. **Una pantalla con el desglose**: "esto cuesta en Alemania, esto es el impuesto de matriculación estimado"

Con eso ya tienes algo que demuestra el diferenciador real (la cifra fiscal, no solo el agregador) y se lo puedes enseñar a alguien sin necesitar nada de Providers, Transactions o Import todavía.

---

## Próximos pasos inmediatos

- [ ] Decidir `original_list_price_es`: ¿se empieza con introducción 100% manual, o merece la pena dedicar un rato a recopilar precios de catálogo de los modelos de tu propio shortlist (Q60/Q50, M-series, Focus RS, Civic Type R, RS3, RS5) para tener el tasador ya útil con tu propia búsqueda de coche?
- [ ] Confirmar los tramos de IEDMT y la tabla de depreciación vigentes a fecha de hoy contra la fuente oficial de la Agencia Tributaria (este plan usa los tramos y coeficientes encontrados en fuentes especializadas de 2026, conviene una pasada de verificación directa contra el BOE/Hacienda antes de lanzar a producción)
- [ ] `git checkout -b feature/v2-fase-0` y empezar por la migración de `user_roles`
- [ ] Sembrar a mano 3-5 listings reales (los propios coches del shortlist de importación) para que Listings y Valuations no arranquen vacíos el día de la demo