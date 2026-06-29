# Fase 6 — Pagos con Stripe Connect

**Duración estimada:** 2 semanas

## Checklist

- [ ] `composer require laravel/cashier`
- [ ] Onboarding Express de Stripe Connect integrado en `OnboardProviderAction` (se genera `stripe_account_id` tras aprobación admin)
- [ ] `application_fee_amount` = comisión de plataforma — **8% por defecto**, configurable por tipo de servicio si interesa diferenciar (ej. menor comisión en transporte, mayor en gestoría, según márgenes reales de cada vertical)
- [ ] Webhook `payment_intent.succeeded` → marca `Transaction` como `paid`, dispara `TransactionPaid`
- [ ] Facturación B2B de comisiones a proveedores (recibo simple, no hace falta sistema de facturación completo en MVP)

## Verificación

- [ ] Un proveedor aprobado completa el onboarding Express de Stripe sin salir de GarageOS.
- [ ] Un pago de prueba en modo sandbox genera el webhook correcto y actualiza el estado de la transacción.
- [ ] La comisión calculada coincide con el 8% configurado.

## Decisión ya tomada

- **Comisión:** 8% por defecto. Diferenciable por tipo de servicio si en el futuro interesa (menor en transporte, mayor en gestoría, según márgenes reales de cada vertical).
- **Identidad del proveedor:** verificación admin manual (sin Stripe Identity) hasta que el volumen lo justifique (ver [fase-3-providers.md](./fase-3-providers.md)).

## Próxima fase

→ [fase-7-mobile-push.md](./fase-7-mobile-push.md)
