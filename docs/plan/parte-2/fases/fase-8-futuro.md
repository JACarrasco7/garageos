# Fase 8 — Futuro: Catálogo Masivo (Post-Tracción)

> **No forma parte del MVP.** Se documenta aquí para no perder el hilo cuando llegue el momento.

Cuando la plataforma tenga tracción suficiente para justificar el gasto (varios cientos de listings activos generados por usuarios, evidencia de que la gente vuelve a buscar), hay tres caminos para ampliar el catálogo más allá de "pegar URL":

## Camino 1 — Servicio de scraping gestionado (más rápido de implementar)

Contratar servicio externo: **Apify**, **Scrapfly**, **Piloterr**... — pagar por resultado, sin mantener infraestructura de scraping propia ni asumir directamente el riesgo de bloqueo.

- ✅ Implementación rápida
- ✅ Sin riesgo de bloqueo (lo asume el proveedor)
- ❌ Coste recurrente variable según volumen

## Camino 2 — Negocio mobile.de Search-API (camino "correcto" a largo plazo)

Acceso de partner a la Search-API oficial de mobile.de. Requiere:

- Contacto comercial directo con mobile.de GmbH
- Probablemente condiciones de volumen mínimo o cuota mensual
- Datos limpios y estables, sin depender de terceros que scrapean por ti

## Camino 3 — Acuerdos de afiliación / redirección

Con gestorías/importadoras ya establecidas (tipo NeedCarHelp, Odden Cars) que ya tienen flujo de anuncios. Modelo:

- Comisión por lead
- Convierte a un competidor potencial en un canal

## Cuándo decidir

Ninguna se presupuesta ni se diseña en detalle ahora. La decisión correcta depende de métricas que **todavía no existen**:

- Cuántos listings llegan orgánicamente
- Qué tasa de conversión a transacción tienen
- Cuánto margen deja la comisión del 8%

---

## Cierre del plan completo

Volver al [README.md](../../README.md) o explorar:

- [decisiones-y-riesgos.md](../decisiones-y-riesgos.md)
- [testing.md](../testing.md)
- [mvp-semana-1.md](../../mvp-semana-1.md)
