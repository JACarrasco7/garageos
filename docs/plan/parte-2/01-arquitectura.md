# Parte II / 01 — Arquitectura v2

## Stack (sin cambios)

Laravel 12, Vue 3.5, Inertia 2, TypeScript 5.8, Tailwind 4, MySQL 8.4, Redis 7, PHP 8.4. Los módulos nuevos siguen el mismo patrón de carpetas y convenciones — **no hay curva de aprendizaje adicional**, es la misma base de código creciendo.

## Módulos nuevos (`app/Modules/`)

```
app/Modules/
├── Identity/      [existente, sin cambios]
├── Vehicle/       [existente, sin cambios]
├── Documents/     [existente, sin cambios]
├── Maintenance/   [existente, sin cambios]
├── Alerts/        [existente, sin cambios]
├── Marketplace/   [existente, informes "Mi garaje", sin cambios]
│
├── Listings/         ← NUEVO — núcleo del MVP
│   ├── Models/                 Listing, ListingSource, SearchAlert, Favorite
│   ├── Actions/                AnalyzeListingUrlAction, CreateSearchAlertAction
│   ├── Parsers/                OpenGraphParser, JsonLdParser, MobileDeParser, AutoScout24Parser
│   ├── Http/                   Controllers, Requests, Resources
│   ├── Events/                 ListingAnalyzed, ListingCreated
│   ├── Jobs/                   EvaluateSearchAlertsJob, RefreshListingDataJob
│   ├── Console/                EvaluateSearchAlertsCommand (schedule cada 6h)
│   └── Providers/              ListingsServiceProvider
│
├── Valuations/      ← NUEVO — tasador fiscal (diferenciador)
│   ├── Models/                 Valuation, IedmtBracket, DepreciationTable
│   ├── Actions/                CalculateMarketValueAction, CalculateImportCostAction
│   ├── Data/                   iedmt-brackets.php, depreciation-table.php (seeders oficiales)
│   └── ...
│
├── Providers/       ← NUEVO — marketplace de servicios (extiende Workshop)
│   ├── Models/                 ServiceProvider, ProviderService, ProviderAvailability, ProviderReview
│   ├── Actions/                OnboardProviderAction, ApproveProviderAction, BookSlotAction
│   └── ...
│
├── Transactions/    ← NUEVO
│   ├── Models/                 Transaction, TransactionItem
│   ├── Actions/                CreateOfferAction, AcceptOfferAction, OpenDisputeAction
│   └── ...
│
├── Messaging/       ← NUEVO
│   ├── Models/                 Conversation, Message, MessageAttachment
│   └── ...
│
└── Import/          ← NUEVO — wizard (6 pasos reales)
    ├── Models/                 ImportCase, ImportDocument, TemporaryPlate
    ├── Actions/                CreateImportCaseAction, AdvanceImportStepAction
    ├── Enums/                  ImportStep (PURCHASE, TRANSPORT, ITV_INSPECTION, TAXES, DGT_REGISTRATION, PLATES)
    └── ...
```

## Frontend (`resources/js/`)

- **Layouts nuevos:** `BuyerLayout.vue`, `ProviderLayout.vue` (junto a `AppLayout.vue` existente).
- **Components nuevos:** `listings/`, `valuations/`, `providers/`, `import/` con `ListingCard`, `PriceBadge`, `ValuationBreakdown`, `ImportCostEstimate`, `ProviderCard`, `AvailabilityCalendar`, `ReviewStars`, `ImportStepper`, `DocumentChecklist`, `PlateExpiryBanner`.
- **Pages nuevos:** `Listings/`, `Valuations/`, `Providers/`, `Transactions/`, `Messaging/`, `Import/`.
- **Rutas por módulo:** `routes/modules/{listings,valuations,providers,transactions,messaging,import}.php`.

## Paquetes Composer adicionales

| Paquete | Versión | Razón |
|---|---|---|
| `laravel/cashier` | ^15.6 | Stripe Connect (Fase 6) |
| `spatie/laravel-query-builder` | ^6.2 | Filtros de búsqueda Listings vía query params (URL compartible) |
| `symfony/dom-crawler` | ^7.1 | Parseo HTML → OpenGraph/JSON-LD al pegar URL |
| `symfony/css-selector` | ^7.1 | Dependencia de `dom-crawler` |
| `league/commonmark` | ^2.6 | Render markdown del Import Wizard |

## Paquetes NPM

**No se añaden nuevos.** `recharts`, `dayjs`, `pinia`, `@vueuse/core`, `vue-sonner` del plan original cubren las necesidades de Listings, Valuations y wizard de Import.

> **Nota sobre scraping:** NO se usa Puppeteer/Playwright en el MVP. `fetch` HTTP único + `symfony/dom-crawler` para extraer `<meta property="og:*">` y `<script type="application/ld+json">` es suficiente. Es una operación **ligera, puntual y a petición explícita del usuario** — muy distinta de un crawler que recorre un catálogo entero sin parar.
