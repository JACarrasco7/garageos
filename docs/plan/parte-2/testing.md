# Testing Strategy v2

Mismo patrón que Parte I (`Pest.php`, `Unit/Modules/`, `Feature/Modules/`), extendido a los módulos nuevos.

## Estructura

```
tests/
├── Unit/Modules/
│   ├── Listings/Actions/
│   │   ├── AnalyzeListingUrlActionTest.php       # mockea fetch HTTP, prueba los 3 parsers
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
│   │   ├── AnalyzeListingTest.php                # éxito, parcial, fallo total
│   │   └── SearchAlertTest.php
│   ├── Valuations/
│   │   └── ValuationTest.php
│   ├── Providers/
│   │   ├── OnboardingTest.php
│   │   └── BookingTest.php
│   ├── Transactions/
│   │   └── TransactionFlowTest.php               # oferta → aceptación → pago → completado
│   ├── Messaging/
│   │   └── ConversationTest.php
│   └── Import/
│       └── ImportWizardTest.php                  # recorre los 6 pasos completos
│
└── Browser/
    └── Listings/
        └── AnalyzeUrlBrowserTest.php
```

## Comandos

```bash
# Todos los tests
./vendor/bin/pest

# Con coverage
./vendor/bin/pest --coverage --min=80

# Parallel (más rápido)
./vendor/bin/pest --parallel

# Static analysis
vendor/bin/phpstan analyse --memory-limit=2G

# Lint
./vendor/bin/pint

# Type-check Vue
npx vue-tsc --noEmit
```

## Cobertura objetivo por fase

- **Fase 0**: ningún test nuevo requerido (infraestructura).
- **Fase 1**: tests de parsers (`OpenGraphParserTest`, etc.) + flow `AnalyzeListingTest`. Mínimo 80% en `Listings/Actions/`.
- **Fase 2**: `CalculateImportCostActionTest` con casos límite de cada tramo de CO₂ (g/km = 0, 110, 120, 180, 250). Mínimo 90% en `Valuations/Actions/`.
- **Fase 3**: `OnboardingTest` + `BookingTest` cubriendo doble reserva, estados, etc.
- **Fase 4**: `TransactionFlowTest` recorre camino completo.
- **Fase 5**: `ImportWizardTest` recorre los 6 pasos.
- **Fase 6**: tests de Stripe con `Stripe::fake()`.

## Estado actual (Parte I)

**54 tests pasando** según junio 2026. Módulos nuevos de Parte II sin tests aún.
