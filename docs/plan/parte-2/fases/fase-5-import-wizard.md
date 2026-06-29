# Fase 5 — Import Wizard (6 pasos reales) ⭐ (diferenciador único)

**Duración estimada:** 2-3 semanas

Los 6 pasos reales del trámite DE→ES según DGT y administracion.gob.es:

1. **Compra** (`purchase`) — usuario marca `purchase_date`, sube `compraventa`/factura y, si existe, el `coc`. Si vehículo anterior a 2002 o sin COC → `needs_homologation = true`, wizard avisa de que hará falta ingeniero homologador antes de la ITV.
2. **Transporte** (`transport`) — gestión de cómo llega a España: con placas temporales/de tránsito alemanas si va a circular por carretera, o en plataforma. Sugiere `provider_services` de tipo `transport_de_es` (Fase 3). Al marcar `arrival_date` → calcula `itv_deadline = arrival_date + 30 días` y crea `AlertRule` para avisar antes de que expire.
3. **ITV de importación** (`itv_inspection`) — checklist de documentación a llevar (ficha técnica origen, tarjeta ITV/TÜV origen, COC u homologación). Sugiere proveedores `itv_assistance`. Al aprobarse, se sube `ficha_itv_es`.
4. **Impuestos** (`taxes`) — el wizard usa la `Valuation` ya calculada en Fase 2 como punto de partida (mismo cálculo, ahora con datos confirmados tras la compra) y guía:
   - Modelo 576 (IEDMT) en la Agencia Tributaria
   - Modelo 309/300 (IVA) si aplica, o modelo de ITP si compra entre particulares
   - Justificante de IVTM del ayuntamiento
   Sugiere `gestoria_hacienda` para quien no quiera hacerlo por su cuenta.
5. **Matriculación DGT** (`dgt_registration`) — checklist final, enlace directo a sede electrónica DGT, sugerencia de `gestoria_dgt`. Al completarse, guarda `final_plate_number`.
6. **Placas físicas** (`plates`) — confirmación. Al completar, `CheckIfReadyForVehicleSync` crea automáticamente registro en `vehicles` (módulo Vehicle Parte I) con los datos ya conocidos del `import_case` → **el coche aparece en "Mi garaje" sin rellenar nada**.

## Checklist

- [ ] Migraciones: `import_cases`, `import_documents`, `temporary_plates`
- [ ] Enum `ImportStep` + `AdvanceImportStepAction`
- [ ] Contenido markdown por paso (ES/EN/CA, reutilizando patrón i18n existente) — fácil de actualizar sin deploy si cambia la normativa
- [ ] `ImportStepper.vue` — stepper visual de 6 pasos con checklist de documentos por paso
- [ ] `DocumentChecklist.vue` reutilizando patrón de subida de `Documents` Parte I
- [ ] `PlateExpiryBanner.vue` + `CheckTemporaryPlateExpiryCommand` (cron diario) — avisa 7 días antes de que caduquen las placas temporales
- [ ] Sugerencia automática de proveedores relevantes en cada paso (transporte paso 2, gestoría pasos 4-5), enlazando a flujo de `Transactions` Fases 3-4

## Verificación

- [ ] Crear `ImportCase` desde un listing con `co2_emissions` conocido → wizard arranca con datos fiscales ya precalculados.
- [ ] Marcar `arrival_date` → se crea automáticamente la alerta de los 30 días para la ITV.
- [ ] Subir documentos por paso → checklist se marca en verde.
- [ ] Completar paso `plates` → aparece nuevo `Vehicle` en "Mi garaje" con datos del import.

## Próxima fase

→ [fase-6-stripe-connect.md](./fase-6-stripe-connect.md) — pagos.
