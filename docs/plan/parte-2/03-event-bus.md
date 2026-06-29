# Parte II / 03 — Event Bus v2

Laravel Events + Listeners con colas Redis. Mismo patrón que Parte I, extendido a los 6 módulos nuevos.

## Listings

| Evento | Listeners |
|---|---|
| `ListingAnalyzed` | → `ExtractStructuredDataListener` (sync) <br> → `CalculateValuationListener` (queued — dispara el tasador automáticamente) |
| `ListingCreated` | → `EvaluateSearchAlertsListener` (queued — mira si matchea alertas activas) |

## Valuations

| Evento | Listeners |
|---|---|
| `ValuationCalculated` | → `NotifyFavoritedListeners` (queued — si alguien tenía el listing en favoritos) |

## Providers

| Evento | Listeners |
|---|---|
| `ProviderOnboarded` | → `NotifyAdminForReviewListener` (sync — email al admin) |
| `ProviderApproved` | → `EnableStripeConnectListener` (queued) |
| `SlotBooked` | → `SendBookingConfirmationJob` (queued — push + email a ambas partes) |

## Transactions

| Evento | Listeners |
|---|---|
| `OfferMade` | → `NotifyCounterpartyListener` (queued) |
| `OfferAccepted` | → `CreateConversationListener` (sync — si no existía ya) |
| `TransactionPaid` | → `UnlockProviderContactInfoListener` (sync) <br> → `ScheduleReviewReminderJob` (queued — +3 días tras completed) |
| `DisputeOpened` | → `NotifyAdminMediationListener` (sync) |

## Messaging

| Evento | Listeners |
|---|---|
| `MessageSent` | → `SendMessagePushJob` (queued) |

## Import

| Evento | Listeners |
|---|---|
| `ImportStepCompleted` | → `AdvanceImportCaseListener` (sync) <br> → `CheckIfReadyForVehicleSync` (sync — al completar `plates` crea el Vehicle en "Mi garaje") |
| `TemporaryPlateExpiringSoon` | → `SendPlateExpiryReminderJob` (queued — push + email, 7 días antes) |

## Convenciones

- **sync** = ejecución inmediata en el mismo request (operaciones rápidas o consistencia inmediata: cambio de estado, creación de entidades relacionadas).
- **queued** = encolado en Redis (operaciones pesadas o que pueden esperar: emails, push, recálculos).
- Patrón de nombres: `XxxListener`, `SendXxxJob`. Jobs serializan a `SendXxxJob` con `ShouldQueue`.
