# Decisiones Tomadas y Riesgos Identificados

## Decisiones tomadas

| Decisión | Razón |
|---|---|
| **Catálogo** nace vacío, crece con cada URL pegada por usuario | Sin scraping masivo en MVP. Ver [fase-8](./fases/fase-8-futuro.md) para futuro. |
| **Extracción**: `fetch` puntual + OpenGraph/JSON-LD + parsers específicos | Honestidad técnica: no es viable tener catálogo masivo gratis. Fallback manual si falla. |
| **Monetización**: comisión 8% por servicio intermediado vía Stripe Connect | No réplica del modelo B2B-leads de AutoUncle. Ver [fase-6](./fases/fase-6-stripe-connect.md). |
| **Tasador**: valor de mercado heurístico + coste fiscal determinista | Único diferenciador real frente a comparadores genéricos. Ver [fase-2](./fases/fase-2-tasador-fiscal.md). |
| **Import Wizard**: 6 pasos reales basados en trámite oficial DGT | No un wizard genérico inventado. Ver [fase-5](./fases/fase-5-import-wizard.md). |
| **Multi-rol**: un usuario = buyer + seller + provider simultáneo | Mercado real lo exige (el vendedor también compra, el profesional también es dueño). Ver [fase-0](./fases/fase-0-setup-multirol.md). |
| **Países**: DE→ES primero, AT/NL/FR futuro | Los mejor documentados y más relevantes. |
| **Idioma**: ES en MVP, infraestructura EN/CA lista para Import Wizard | Reutilización del i18n existente. |
| **Verificación de proveedores**: admin manual | Rápido, sin coste de Stripe Identity. Revisar si volumen lo justifica. |
| **Mensajería**: polling 10s | WebSockets (Reverb) solo si volumen lo justifica. |
| **Mobile**: PWA + Capacitor en paralelo | Sin coste adicional relevante. |

## Tabla de riesgos

| Riesgo | Impacto | Mitigación |
|---|---|---|
| El catálogo crece muy despacio si no hay usuarios pegando URLs desde día 1 | **Alto** — sin listings no hay tasador, sin tasador no hay diferenciador | Lanzar con handful de listings sembrados a mano (los propios coches del shortlist de importación) para que la plataforma no se vea vacía el día 1 |
| `original_list_price_es` difícil de obtener para modelos antiguos/poco comunes | **Medio** — afecta precisión del cálculo B del tasador | Permitir introducción manual + marcar visualmente cuándo dato es estimado vs confirmado |
| Extracción OpenGraph/JSON-LD falla si el portal cambia su HTML | **Medio** — recurrente | Fallback manual evita bloqueo; revisar parsers específicos cuando se detecten fallos repetidos |
| Onboarding manual de proveedores no escala si crece volumen | **Bajo a medio** plazo | Stripe Identity u otra automatización cuando volumen de solicitudes lo justifique |
| Normativa IEDMT/tramos CO₂ cambia con leyes de presupuestos | **Medio** — tasador daría cifras desactualizadas | Tablas (`iedmt_brackets`, `depreciation_table`) versionadas por `valid_from`, revisar anualmente |
| Confusión nomenclatura entre `Marketplace` (informes Parte I) y nuevo `Providers` (servicios) | **Bajo**, deuda técnica | Nomenclatura clara en código: `Marketplace` solo para informes "Mi garaje", `Providers` para servicios profesionales |
| Disputas sin resolución automatizada pueden acumularse | **Bajo** en MVP | Panel de mediación admin simple primero, automatizar reglas básicas (reembolso automático si proveedor no responde en X días) más adelante |
| Cambio en ToS de portales (mobile.de, AutoScout24) cierra la extracción | **Medio** | Diseño ya prevén fallback manual. Monitorizar y ajustar parsers. |

## Próximos pasos inmediatos

- [ ] Decidir `original_list_price_es`: ¿manual al 100% o dedicar tiempo a recopilar precios de catálogo de modelos del shortlist propio (Q60/Q50, M-series, Focus RS, Civic Type R, RS3, RS5)?
- [ ] Confirmar tramos IEDMT y tabla depreciación vigentes contra fuente oficial Agencia Tributaria/BOE antes de producción.
- [ ] `git checkout -b feature/v2-fase-0` y empezar por migración `user_roles`.
- [ ] Sembrar a mano 3-5 listings reales (los propios coches del shortlist) para que Listings/Valuations no arranquen vacíos en demo.
