# GarageOS — Plan de Implementación (Índice Maestro)

> **Stack:** Laravel 12 + Vue 3.5 + Inertia.js 2 + TypeScript 5.8 + Tailwind CSS 4 + MySQL 8.4 + Redis 7 + PHP 8.4
> **Última actualización:** 29 de Junio de 2026
> **Estado:** Plan v2 — reestructurado tras investigación de viabilidad legal/técnica

Documentación modular por secciones/fases. Cada archivo es autocontenido pero el orden lógico de lectura es el del índice.

---

## Cómo navegar

- Si **arrancas hoy**: lee [parte-1-fundamentos.md](./parte-1-fundamentos.md) → [parte-2/00-vision.md](./parte-2/00-vision.md) → [mvp-semana-1.md](./mvp-semana-1.md).
- Si **implementas una fase concreta**: salta directo a `parte-2/fases/fase-N-*.md`.
- Si **dudas legales/técnicas del pivote**: [parte-2/decisiones-y-riesgos.md](./parte-2/decisiones-y-riesgos.md).

---

## Parte I — Fundamentos heredados (GarageOS Personal)

> Plan original ya implementado parcialmente. Se mantiene como sección **"Mi garaje"** dentro de la plataforma ampliada.

| # | Archivo | Contenido |
|---|---|---|
| 1 | [parte-1-fundamentos.md](./parte-1-fundamentos.md) | Stack, estructura módulos existentes, schema v1, paquetes, roadmap Fases 0-3 originales |

---

## Parte II — GarageOS v2 (Marketplace de Importación + Servicios)

> Núcleo del nuevo producto. Listado por módulos/fases, cada uno con su checklist, dependencias y criterios de verificación.

### Arquitectura y decisiones

| # | Archivo | Contenido |
|---|---|---|
| 2 | [parte-2/00-vision.md](./parte-2/00-vision.md) | Por qué v2 reemplaza al plan anterior, asunciones erróneas corregidas |
| 3 | [parte-2/01-arquitectura.md](./parte-2/01-arquitectura.md) | Estructura de módulos, stack y versiones, paquetes nuevos |
| 4 | [parte-2/02-base-de-datos.md](./parte-2/02-base-de-datos.md) | Schema completo v2 (todas las migraciones de los 6 módulos nuevos) |
| 5 | [parte-2/03-event-bus.md](./parte-2/03-event-bus.md) | Eventos y listeners v2 |

### Fases de implementación

| Fase | Archivo | Duración | Habilita |
|---|---|---|---|
| **0** | [fase-0-setup-multirol.md](./parte-2/fases/fase-0-setup-multirol.md) | 1 semana | Base multi-rol para buyer/seller/provider |
| **1** ⭐ | [fase-1-listings-mvp.md](./parte-2/fases/fase-1-listings-mvp.md) | 3 semanas | Captura de anuncios + búsqueda + alertas |
| **2** ⭐ | [fase-2-tasador-fiscal.md](./parte-2/fases/fase-2-tasador-fiscal.md) | 2 semanas | Diferenciador: cálculo fiscal real DE→ES |
| **3** ⭐ | [fase-3-providers.md](./parte-2/fases/fase-3-providers.md) | 3 semanas | **Habilita revenue** — marketplace de servicios |
| **4** | [fase-4-transactions-messaging.md](./parte-2/fases/fase-4-transactions-messaging.md) | 3 semanas | Ofertas, chat in-app, disputas |
| **5** ⭐ | [fase-5-import-wizard.md](./parte-2/fases/fase-5-import-wizard.md) | 2-3 semanas | Diferenciador único: 6 pasos reales DGT |
| **6** | [fase-6-stripe-connect.md](./parte-2/fases/fase-6-stripe-connect.md) | 2 semanas | Pagos a profesionales con comisión 8% |
| **7** | [fase-7-mobile-push.md](./parte-2/fases/fase-7-mobile-push.md) | 2 semanas | Capacitor + FCM |
| **8** | [fase-8-futuro.md](./parte-2/fases/fase-8-futuro.md) | — | Catálogo masivo (post-tracción) |

### Cierre

| # | Archivo | Contenido |
|---|---|---|
| X | [parte-2/decisiones-y-riesgos.md](./parte-2/decisiones-y-riesgos.md) | Decisiones tomadas + tabla de riesgos con mitigaciones |
| X | [parte-2/testing.md](./parte-2/testing.md) | Strategy de tests (Pest Unit/Feature/Browser) |
| — | [mvp-semana-1.md](./mvp-semana-1.md) | Recorte mínimo para tener algo enseñable mañana mismo |

---

## Estado actual (Junio 2026)

| Componente | Estado | Tests |
|------------|--------|-------|
| Vehicle Module | ✅ Completo | 12 tests |
| Alerts Module | ✅ Completo | 9 tests |
| Documents Module | ✅ Completo | — |
| Maintenance Module | ✅ Completo | — |
| Marketplace Module (informes) | ✅ Completo | — |
| FCM Push Notifications | ✅ Implementado | — |
| Dashboard Stats | ✅ Implementado | — |
| Pest Dusk | ✅ Scaffold | — |
| API pública OBD | ✅ Implementado | 2 tests |
| Stripe Subscriptions | ✅ Implementado | — |
| Panel Taller | ✅ Implementado | — |
| Service Pack Recommendations | ✅ Implementado | 2 tests |

**Total: 54 tests pasando** en Parte I. Pendiente: módulos nuevos de Parte II (Listings, Valuations, Providers, Transactions, Messaging, Import).

---

## Repositorios / archivos legacy

- `docs/legacy/GARAGEOS-PLAN.md` — copia histórica del plan Parte I (no editar, consultar).
- `docs/legacy/GARAGEOS-v2-original.md` — borrador previo del plan Parte II (sustituido por los archivos de `parte-2/`).
