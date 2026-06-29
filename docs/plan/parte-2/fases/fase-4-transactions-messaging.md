# Fase 4 — Transactions + Messaging

**Duración estimada:** 3 semanas

## Checklist

- [ ] Migraciones: `transactions`, `transaction_items`, `conversations`, `conversation_participants`, `messages`, `message_attachments`
- [ ] `CreateOfferAction` — un comprador puede contactar a un proveedor para reservar un servicio (`type = 'service_booking'`) ligado opcionalmente a un listing
- [ ] `AcceptOfferAction` — el proveedor confirma, se crea la reserva en `provider_availabilities` y se dispara `OfferAccepted`
- [ ] Chat in-app con `StartConversationAction` y `SendMessageAction` — **polling cada 10s en el MVP**, nada de WebSockets todavía (Laravel Reverb se evalúa si volumen de mensajería lo justifica)
- [ ] Adjuntos en mensajes (fotos del coche, documentos) vía `MessageAttachment`
- [ ] `OpenDisputeAction` — marca la transacción como `disputed`, notifica a admin para mediación manual (sin automatizar resolución en MVP)

## Estados de `Transaction`

`pending → accepted → paid → completed` (camino feliz)
`pending → rejected` (oferta rechazada)
`paid → disputed → refunded|cancelled` (caminos de conflicto)

## Verificación

- [ ] Comprador contacta a un inspector para revisión pre-compra → se crea `Transaction` con `status = 'pending'` y `Conversation` asociada.
- [ ] Proveedor acepta → `status = 'accepted'`, slot reservado, ambas partes reciben notificación.
- [ ] Abrir una disputa cambia el estado y genera alerta en el panel de admin.

## Decisión ya tomada

- **Mensajería:** polling 10s en el MVP. WebSockets (Laravel Reverb) solo si volumen de mensajes lo justifica.

## Próxima fase

→ [fase-5-import-wizard.md](./fase-5-import-wizard.md) — diferenciador único.
