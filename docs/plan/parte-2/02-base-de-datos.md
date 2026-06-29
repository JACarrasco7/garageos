# Parte II / 02 — Base de Datos: Schema v2 Completo

## Identity (extensión)

```sql
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

> `users.role` (el enum `owner/workshop/admin` original) **se deja intacto** para no romper el módulo Identity existente. `user_roles` es la fuente de verdad para el nuevo sistema multi-rol. Helper `$user->hasRole('provider')`.

## Listings

```sql
CREATE TABLE listings (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_by_user_id  BIGINT UNSIGNED NOT NULL,
    source_url          VARCHAR(500) NOT NULL,
    source_portal       ENUM('mobile_de', 'autoscout24', 'other') NOT NULL DEFAULT 'other',
    source_listing_id   VARCHAR(100) NULL,
    brand               VARCHAR(50) NOT NULL,
    model               VARCHAR(80) NOT NULL,
    model_description   VARCHAR(200) NULL,
    year                YEAR NULL,
    mileage_km          INT UNSIGNED NULL,
    fuel_type           ENUM('gasolina','diesel','hibrido','electrico','glp','otro') NULL,
    power_hp            INT NULL,
    co2_emissions       INT NULL,
    gearbox             ENUM('manual','automatico') NULL,
    price_eur           DECIMAL(10,2) NULL,
    country             VARCHAR(2) DEFAULT 'DE',
    seller_type         ENUM('dealer','private') NULL,
    seller_name         VARCHAR(150) NULL,
    seller_location     VARCHAR(150) NULL,
    photos              JSON NULL,
    raw_extracted_data  JSON NULL,
    extraction_method   ENUM('opengraph','json_ld','portal_parser','manual') NOT NULL,
    extraction_status   ENUM('pending','success','partial','failed') DEFAULT 'pending',
    is_active           BOOLEAN DEFAULT TRUE,
    last_checked_at     TIMESTAMP NULL,
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
    criteria        JSON NOT NULL,
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

## Valuations

```sql
CREATE TABLE valuations (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id                  BIGINT UNSIGNED NOT NULL,
    market_value_estimate       DECIMAL(10,2) NULL,
    market_value_confidence     ENUM('low','medium','high') DEFAULT 'low',
    original_list_price_es      DECIMAL(10,2) NULL,
    fiscal_value                DECIMAL(10,2) NULL,
    depreciation_coefficient    DECIMAL(4,2) NULL,
    iedmt_rate                  DECIMAL(5,4) NULL,
    iedmt_amount                DECIMAL(10,2) NULL,
    estimated_iva_or_itp        DECIMAL(10,2) NULL,
    estimated_transport_cost    DECIMAL(10,2) NULL,
    estimated_itv_cost          DECIMAL(10,2) NULL,
    estimated_dgt_fees          DECIMAL(10,2) NULL,
    estimated_total_landed_cost DECIMAL(10,2) NULL,
    calculated_at               TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    INDEX idx_listing (listing_id)
);

CREATE TABLE iedmt_brackets (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    co2_min     INT NOT NULL,
    co2_max     INT NULL,
    rate        DECIMAL(5,4) NOT NULL,
    valid_from  DATE NOT NULL
);

CREATE TABLE depreciation_table (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    age_years_min   TINYINT UNSIGNED NOT NULL,
    age_years_max   TINYINT UNSIGNED NULL,
    coefficient     DECIMAL(4,2) NOT NULL,
    valid_from      DATE NOT NULL
);
```

## Providers (extiende `workshops`)

```sql
ALTER TABLE workshops
    ADD COLUMN provider_type      ENUM('inspector','transporter','gestoria','taller') NULL AFTER user_id,
    ADD COLUMN bio                TEXT NULL,
    ADD COLUMN stripe_account_id  VARCHAR(100) NULL,
    ADD COLUMN onboarding_status  ENUM('pending','docs_submitted','approved','rejected') DEFAULT 'pending',
    ADD COLUMN approved_at        TIMESTAMP NULL,
    ADD COLUMN approved_by        BIGINT UNSIGNED NULL,
    ADD COLUMN rating_avg         DECIMAL(3,2) DEFAULT NULL,
    ADD COLUMN rating_count       INT UNSIGNED DEFAULT 0,
    ADD FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

CREATE TABLE provider_services (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id     BIGINT UNSIGNED NOT NULL,
    type            ENUM('pre_purchase_inspection','transport_de_es','homologation',
                          'gestoria_dgt','gestoria_hacienda','itv_assistance','other') NOT NULL,
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
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id             BIGINT UNSIGNED NOT NULL,
    provider_service_id     BIGINT UNSIGNED NULL,
    starts_at               DATETIME NOT NULL,
    ends_at                 DATETIME NOT NULL,
    is_booked               BOOLEAN DEFAULT FALSE,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_service_id) REFERENCES provider_services(id) ON DELETE CASCADE,
    INDEX idx_workshop_date (workshop_id, starts_at),
    INDEX idx_booked (is_booked)
);

CREATE TABLE provider_reviews (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id     BIGINT UNSIGNED NOT NULL,
    transaction_id  BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    rating          TINYINT UNSIGNED NOT NULL,
    comment         TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_transaction_review (transaction_id)
);
```

## Transactions

```sql
CREATE TABLE transactions (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id                    BIGINT UNSIGNED NOT NULL,
    listing_id                  BIGINT UNSIGNED NULL,
    workshop_id                 BIGINT UNSIGNED NULL,
    type                        ENUM('service_booking','vehicle_offer') NOT NULL,
    status                      ENUM('pending','accepted','rejected','paid','completed','disputed','refunded','cancelled') DEFAULT 'pending',
    amount_eur                  DECIMAL(10,2) NOT NULL,
    platform_fee_eur            DECIMAL(10,2) NULL,
    stripe_payment_intent_id    VARCHAR(100) NULL,
    notes                       TEXT NULL,
    created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE SET NULL,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    INDEX idx_buyer_status (buyer_id, status),
    INDEX idx_workshop_status (workshop_id, status)
);

CREATE TABLE transaction_items (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id              BIGINT UNSIGNED NOT NULL,
    provider_service_id         BIGINT UNSIGNED NULL,
    provider_availability_id    BIGINT UNSIGNED NULL,
    description                 VARCHAR(255) NOT NULL,
    quantity                    SMALLINT UNSIGNED DEFAULT 1,
    unit_price_eur              DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_service_id) REFERENCES provider_services(id) ON DELETE SET NULL,
    FOREIGN KEY (provider_availability_id) REFERENCES provider_availabilities(id) ON DELETE SET NULL
);
```

## Messaging

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

## Import (wizard de matriculación)

```sql
CREATE TABLE import_cases (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             BIGINT UNSIGNED NOT NULL,
    listing_id          BIGINT UNSIGNED NULL,
    vehicle_id          BIGINT UNSIGNED NULL,
    origin_country      VARCHAR(2) DEFAULT 'DE',
    current_step        ENUM('purchase','transport','itv_inspection','taxes','dgt_registration','plates','completed') DEFAULT 'purchase',
    purchase_date       DATE NULL,
    arrival_date        DATE NULL,
    itv_deadline        DATE NULL,
    needs_homologation  BOOLEAN DEFAULT FALSE,
    co2_emissions       INT NULL,
    final_plate_number  VARCHAR(10) NULL,
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
    type            ENUM('compraventa','coc','ficha_tecnica_origen','tarjeta_itv_origen',
                          'seguro_transporte','ficha_itv_es','modelo_576','modelo_309_300',
                          'modelo_itp','justificante_ivtm','permiso_circulacion','otro') NOT NULL,
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
    expires_at      DATE NOT NULL,
    is_extended     BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_case_id) REFERENCES import_cases(id) ON DELETE CASCADE,
    INDEX idx_expiry (expires_at)
);
```
