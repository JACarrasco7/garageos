# Fase 2 — Tasador Fiscal (Diferenciador) ⭐

**Duración estimada:** 2 semanas
**Qué hace único GarageOS:** no solo "es buen precio", sino **cuánto va a costar realmente tenerlo circulando en España**.

Combina dos cálculos independientes:

## Cálculo A — Valor de mercado (heurística, mejora con el tiempo)

- Compara el listing contra otros listings de la misma marca/modelo/año/km ya analizados en la plataforma.
- Si hay menos de 5 comparables, `market_value_confidence = 'low'` y la UI lo deja claro — **nunca se presenta una cifra con poco respaldo como si fuera certera**.
- Este cálculo mejora orgánicamente: cuantos más usuarios pegan anuncios, más comparables hay, más fiable se vuelve. **Es la métrica de éxito principal de Listings.**

## Cálculo B — Coste fiscal real (determinista, tablas oficiales)

1. `original_list_price_es`: si no se conoce el precio de catálogo original en España, se estima (campo a completar con datos públicos por marca/modelo; introducible a mano por usuario/admin mientras tanto).
2. `depreciation_coefficient`: se busca en `depreciation_table` según la antigüedad del vehículo.
3. `fiscal_value = original_list_price_es × depreciation_coefficient`.
4. `iedmt_rate`: se busca en `iedmt_brackets` según `co2_emissions` (0% / 4,75% / 9,75% / 14,75%).
5. `iedmt_amount = fiscal_value × iedmt_rate`.
6. `estimated_iva_or_itp`: 21% si intracomunitario nuevo/cuasi-nuevo (menos de 6 meses o 6.000 km), o ITP autonómico si usado entre particulares. Configurable por CCAA, Extremadura/Andalucía como valores por defecto razonables.
7. Costes fijos: transporte estimado (rango por distancia), ITV (~60-80€), tasa DGT (~100€), placas (~30-50€).
8. **`estimated_total_landed_cost = price_eur + iedmt_amount + estimated_iva_or_itp + estimated_transport_cost + estimated_itv_cost + estimated_dgt_fees`**.

## Checklist

- [ ] Migraciones: `valuations`, `iedmt_brackets`, `depreciation_table` (seeders ya creados en Fase 0)
- [ ] `CalculateMarketValueAction` — cálculo A
- [ ] `CalculateImportCostAction` — cálculo B, núcleo fiscal
- [ ] `ValuationBreakdown.vue` — desglose visual línea a línea (no solo el total: la gente quiere ver de dónde sale cada cifra, especialmente el IEDMT que es el que más sorprende)
- [ ] Badge "precio justo / caro / chollo" en `ListingCard` y `PriceBadge`, basado en cuánto se desvía `estimated_total_landed_cost` del valor de mercado en España
- [ ] Job semanal `RecalculateValuationsJob` — recalcula valuations existentes si entran nuevos comparables o cambian tramos fiscales

## Verificación

- [ ] Listing con CO₂ = 110g/km → `iedmt_rate = 0`, badge muestra "0€ de impuesto de matriculación".
- [ ] Listing con CO₂ = 180g/km y 7 años de antigüedad → desglose muestra coeficiente de depreciación correcto y tramo del 9,75%.
- [ ] Cambiar `co2_emissions` de un listing manualmente recalcula la valuation.

## Limitación que hay que comunicar en la UI sin tapujos

Esto es una **estimación**, no una liquidación oficial. El cálculo real de Hacienda puede variar según CCAA, modelo exacto y documentación aportada. El tasador da una cifra de referencia para decidir si comprar, no un justificante de pago.

## Próxima fase

→ [fase-3-providers.md](./fase-3-providers.md) — habilita revenue.
