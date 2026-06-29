# Fase 3 — Providers + Marketplace de Servicios ⭐ (habilita revenue)

**Duración estimada:** 3 semanas
**Objetivo:** inspectores, transportistas y gestorías dados de alta y reservables.

## Checklist

- [ ] Migración que extiende `workshops` con `provider_type`, `bio`, `stripe_account_id`, `onboarding_status`, `rating_avg`, `rating_count`
- [ ] Migraciones: `provider_services`, `provider_availabilities`, `provider_reviews`
- [ ] `OnboardProviderAction` + wizard `/become-provider`:
  1. Tipo de proveedor (inspector / transportista / gestoría / taller)
  2. Datos de contacto y ubicación
  3. Subida de documentación (DNI/NIF, seguro RC si aplica) vía Spatie MediaLibrary
  4. Queda en `onboarding_status = 'docs_submitted'`
- [ ] `ApproveProviderAction` — **verificación manual por admin** (rápido de implementar, sin coste de Stripe Identity; se reevalúa si volumen lo justifica)
- [ ] `/provider/services` — dashboard para crear y editar `ProviderService`
- [ ] `AvailabilityCalendar.vue` — calendario simple de slots (franjas fecha/hora que el proveedor abre manualmente; sin motor complejo en MVP)
- [ ] `/marketplace/services` — búsqueda pública filtrable por tipo de servicio y zona
- [ ] `ReviewStars.vue` + actualización de `rating_avg`/`rating_count` al guardar `provider_review`

## Verificación

- [ ] Onboarding de un proveedor de prueba → queda en `docs_submitted` → admin lo aprueba → estado pasa a `approved` y aparece en el buscador público.
- [ ] Reservar un slot marca `provider_availabilities.is_booked = true` y no permite doble reserva.
- [ ] Tras transacción completada, una review actualiza el rating medio.

## Decisiones ya tomadas

- **Verificación de proveedores:** admin manual con documentos subidos (rápido, sin coste de Stripe Identity). Se reevalúa si el volumen lo justifica.
- **Monetización:** comisión del **8%** por servicio intermediado vía Stripe Connect (ver [fase-6-stripe-connect.md](./fase-6-stripe-connect.md)).

## Próxima fase

→ [fase-4-transactions-messaging.md](./fase-4-transactions-messaging.md)
