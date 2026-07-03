# PostgreSQL Setup — GarageOS

## Producción / Dev (Docker)

```bash
docker-compose up -d postgres
docker-compose up app
```

## Local (Laragon)

1. Instalar PostgreSQL 16+
2. Crear DB:
```sql
CREATE DATABASE garageos;
CREATE USER garageos WITH PASSWORD 'garageos';
GRANT ALL PRIVILEGES ON DATABASE garageos TO garageos;
```

3. Configurar `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=garageos
DB_USERNAME=garageos
DB_PASSWORD=garageos
```

4. Migrar:
```bash
php artisan migrate
```

## Tests (SQLite en memoria)

`phpunit.xml` mantiene SQLite para tests por velocidad y aislamiento.
```
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## Migraciones

- `enum()` se convierte a PostgreSQL `ENUM types` nativamente
- `json()` usa `JSONB` automáticamente
- `year()` funciona via `integer`
- Full-text search disponible con `to_tsvector`/`tsquery`