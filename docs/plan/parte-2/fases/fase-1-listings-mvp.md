# Fase 1 — Listings + Búsqueda (MVP) ⭐

**Duración estimada:** 3 semanas
**Validación real:** ¿la gente quiere pegar enlaces de coches alemanes y ver qué le dice la plataforma?

## Semana 1 — Captura del anuncio

- [ ] Migraciones: `listings`, `search_alerts`, `favorites`
- [ ] `AnalyzeListingUrlAction`:
  1. Valida que la URL pertenece a un dominio conocido (mobile.de, autoscout24.*, o "otro" — no se bloquea, pero se marca el origen)
  2. **Un único `fetch` HTTP** de la página (User-Agent identificable, ej. `GarageOSBot/1.0 (+https://garageos.app/bot)`, sin reintentos agresivos)
  3. Extrae `<meta property="og:*">` con `OpenGraphParser`
  4. Si existe, extrae `<script type="application/ld+json">` con `JsonLdParser` (mobile.de y AutoScout24 usan structured data tipo `Product`/`Car`)
  5. Si el dominio es reconocido, aplica parser específico (`MobileDeParser`, `AutoScout24Parser`) que mapea a columnas de `listings`
  6. Guarda con `extraction_status` (`success`/`partial`/`failed`) y el payload crudo en `raw_extracted_data`
  7. Dispara `ListingAnalyzed`
- [ ] `Listings/Analyze.vue` — formulario "pegar enlace" con estado de carga y fallback manual si extracción falla o es parcial (precio, km, año, CO₂ como mínimo)
- [ ] Listener `CalculateValuationListener` que dispara la Fase 2 en cuanto se guarda un listing con datos mínimos

## Semana 2 — Catálogo y búsqueda

- [ ] `Listings/Search.vue` con filtros (marca, modelo, precio, año, km, combustible) usando `spatie/laravel-query-builder` — URL compartible (`?brand=BMW&price_max=20000`)
- [ ] `Listings/Show.vue` con galería de fotos, datos del vendedor, enlace de vuelta al anuncio original
- [ ] `ListingCard.vue`, `PriceBadge.vue` (verde/ámbar/rojo según comparación con `market_value_estimate` cuando exista)
- [ ] Favoritos (`FavoriteController`)

## Semana 3 — Alertas

- [ ] `CreateSearchAlertAction` + `SearchAlerts` UI
- [ ] `EvaluateSearchAlertsJob` — cada 6h compara criterios guardados contra listings nuevos o modificados
- [ ] Email de alerta cuando hay match (Laravel Mail)
- [ ] `RefreshListingDataJob` opcional — vuelve a analizar un listing existente para detectar si el anuncio original ya no está disponible

## Verificación

- [ ] Pegar URL real de mobile.de → aparece en `listings` con marca, modelo, precio, km y CO₂ rellenados (al menos vía OpenGraph, idealmente vía JSON-LD).
- [ ] URL de portal no reconocido con extracción fallida → formulario manual y el listing se guarda igualmente.
- [ ] `GET /listings?brand=BMW&price_max=20000` → grid filtrado con URL compartible.
- [ ] Crear alerta con criterios → tras pegar un listing nuevo que matchea, el job dispara email en la siguiente ejecución programada.

## Próxima fase

→ [fase-2-tasador-fiscal.md](./fase-2-tasador-fiscal.md)
