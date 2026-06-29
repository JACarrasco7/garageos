
Buen giro. Lo que propones es bastante más ambicioso que el GarageOS inicial: pasas de "gestionar mi coche" a una plataforma de importación de vehículos con coordinación de actores + marketplace de servicios.

Antes de armar el plan, necesito clavar el alcance porque hay decisiones que cambian todo el diseño. Te pregunto:

Analizando las respuestas...

Q: ¿Qué hacemos con lo ya construido (gestión de tu propio coche, mantenimiento, alertas, informes)?
A: seria una intyegracion nuevo al polan que ya tenemos
Q: ¿Cómo obtenemos los anuncios de coches europeos?
A: lo ideal seria hacer ocmo https://www.autouncle.de/es/pagina-inicial, pero que el sue rtamben peude poenr el enlacde del anunciia y peuda calcuilar y demas estaria bein.
Q: ¿Cómo monetiza la plataforma?
A: Comisión por servicio - Cobramos % al revisor/transportista cuando el comprador les contrata a través de la plataforma.
Q: ¿Por dónde empezamos? (lo que está antes se hace primero)
A: por donde mejor veas, anets vamos a crea un poan completo por fases con todoo especiificado
Voy a investigar el referente (AutoUncle) y el estado real de los módulos existentes en paralelo.

Tengo lo que necesito. Voy a estructurar el plan completo por fases y guardarlo en memoria antes de mostrarlo.

Plan: GarageOS v2 — Plataforma de Importación + Marketplace de Servicios
TL;DR: Ampliamos el scope del GarageOS existente con 7 nuevos módulos. El core pasa a ser Listings (agregador tipo AutoUncle) + Providers (marketplace de profesionales), todo conectado por Transactions/Messaging. El GarageOS personal se mantiene como sección bonus "Mi garaje". Monetización: comisión por servicio intermediado.

Steps (agrupados por fase)
Fase 0 — Setup base + multi-rol (1 sem)

Migración user_roles pivot + extender User con roles múltiples
Middleware EnsureUserHasRole
Crear estructura de carpetas de los 7 módulos nuevos
Layouts Vue por rol (BuyerLayout, SellerLayout, ProviderLayout)
Sidebar adaptativo con switcher de rol
Fase 1 — Listings + búsqueda ⭐ MVP (3-4 sem)

Migraciones: listings, listing_sources, search_alerts, favorites
AnalyzeListingUrlAction — pegar enlace y extraer datos (OpenGraph + JSON-LD)
Scraper base para Mobile.de (1 portal, rate-limit + user-agent identificable)
Listings/Search.vue con filtros completos y URL compartible
Listings/Show.vue con galería + mapa + valoración
Favoritos + alertas de búsqueda (EvaluateSearchAlertsJob cada 6h)
Fase 2 — Valuations (tasador) (2 sem)

Migración valuations + seeder inicial con marcas populares
CalculateValuationAction con heurística de depreciación
Job semanal RecalculateValuationsJob con datos reales
Badge "precio justo" en cada listing + gráfico de distribución
Fase 3 — Providers + marketplace ⭐ habilita revenue (3 sem)

Extender tabla workshops con tipo (inspector|transporter|gestor|taller), bio, rating, Stripe account
Migraciones: provider_services, provider_availabilities, provider_reviews
Wizard de onboarding /become-provider con subida de docs
Dashboard /provider/services + calendario de disponibilidad
Marketplace público /marketplace/services filtrable por zona
Fase 4 — Transactions + messaging (3 sem)

Migraciones: transactions, transaction_items, conversations, messages
Flujos: oferta al vendedor, reserva de servicio con slots
Chat in-app con adjuntos (polling 10s, WebSockets después)
Sistema de disputas y mediación admin
Fase 5 — Import Wizard (matriculación España) ⭐ diferenciador único (2-3 sem)

Migraciones: import_cases, import_documents, temporary_plates
Wizard de 10 pasos: planificación → compra → baja origen → transporte → placas DE → placas ES → ITV → impuestos → DGT → matrícula definitiva
Contenido en markdown (fácil de actualizar sin deploy)
Sugerencia automática de gestorías partner
Recordatorios de caducidad de placas temporales
Fase 6 — Pagos (Stripe Connect) (2 sem)

Onboarding Express de profesionales
application_fee_amount = comisión plataforma (8% default)
Webhook payment_intent.succeeded → marca transacción como paid
Facturación B2B de comisiones
Fase 7 — Mobile + push (2 sem)

Capacitor 7 + FCM
Push en: ofertas, mensajes, alertas búsqueda, recordatorios import
Relevant files (a modificar/crear)
Estructura nueva (carpetas vacías primero):

app/Modules/Listings/ — Models, Actions, Http, Events, Jobs, Console, Providers, Routes, Tests
app/Modules/Valuations/ — mismo patrón
app/Modules/Providers/ — extiende Workshops existente
app/Modules/Transactions/
app/Modules/Messaging/
app/Modules/Import/
Backend crítico:

app/Models/User.php — añadir roles(), helpers
app/Modules/Vehicle/Http/Controllers/VehicleController.php — patrón CRUD a replicar
app/Modules/Listings/Actions/AnalyzeListingUrlAction.php — núcleo del MVP
app/Modules/Listings/Sources/MobileDeSource.php — primer scraper
app/Modules/Listings/Models/Listing.php — modelo principal con scopes
Frontend crítico:

resources/js/Layouts/AuthenticatedLayout.vue — patrón a renombrar a OwnerLayout.vue
resources/js/Pages/Vehicle/Index.vue — patrón de card grid a replicar
resources/js/Pages/Listings/Search.vue, Show.vue, Analyze.vue — núcleo MVP
resources/js/Components/listings/ListingCard.vue, FilterPanel.vue, PriceBadge.vue
Migraciones existentes (referencia):

database/migrations/2026_06_14_000010_create_workshops_table.php — base a extender en Fase 3
database/migrations/2026_06_14_000002_create_vehicles_table.php — patrón de enums y FKs
Verification
Fase 1 verificada con:

Pegar URL de Mobile.de → listing normalizado en listings con todos los campos
GET /listings?brand=BMW&price_max=20000 → grid filtrado + URL compartible
Favorito añadido → fila en favorites + count actualizado
Crear alerta con criterios → job dispara email tras nuevas detecciones
Fase 3 verificada con:

Onboarding de provider → admin aprueba → estado verified
Reserva de cita → slot ocupado en provider_availabilities
Review post-transacción → rating actualizado
Fase 5 verificada con:

Crear ImportCase DE→ES → wizard recorre 10 pasos
Subir docs por paso → check verde en checklist
Caducidad de placa temporal → alerta 7 días antes
Global:

php artisan test con Pest (unit + feature por módulo)
vendor/bin/phpstan analyse sin errores nivel 6+
Probar flujo completo: pegar anuncio → analizar → contactar revisor → contratar → reservar transporte → wizard import
Decisions (asumidas para arrancar)
Scraping ético: 1 portal el MVP (Mobile.de), rate-limit 3s, no agresivo
Multi-rol: un usuario puede ser comprador + vendedor + profesional simultáneamente
Multi-país: empieza con DE + ES (los más relevantes para importación a España)
Idioma: solo ES en MVP, i18n no condiciona arquitectura
Pagos: Stripe Connect Express para profesionales
Mensajería: polling 10s en MVP, WebSockets (Laravel Reverb) en fase 2
Further Considerations
Verifier de profesionales — opción A: admin verifica manual con docs subidos (rápido); opción B: Stripe Identity automatizado (costes). Recomiendo A para MVP
Multi-idioma del marketplace — ¿añadir DE desde día 1 para que los proveedores alemanes se sientan cómodos? Recomiendo empezar solo ES (más rápido, mercado objetivo claro)
App móvil dedicada vs web responsive — para Fase 7, ¿solo build Android/iOS con Capacitor, o PWA instalable también? Recomiendo ambas: PWA + Capacitor
¿Verificar dominios de portales antes de scraping? — sí, empezar con Mobile.de que tiene feed oficial de pago (partner program)
¿Quieres que arranque con la Fase 0 (multi-rol + estructura), o prefieres que valide/ajuste alguna decisión antes?