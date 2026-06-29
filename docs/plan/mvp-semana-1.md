# MVP Mínimo Viable — Semana 1

Si quieres algo enseñable cuanto antes (mismo espíritu que el MVP "mañana mismo" del plan original), este es el recorte mínimo que **ya demuestra la idea central**:

1. **Login + registro** — Breeze ya lo da, reutilizado.
2. **Pegar URL de un anuncio** → extracción OpenGraph básica (sin parsers específicos de mobile.de/AutoScout24 todavía, sin fallback manual completo).
3. **Mostrar el listing guardado** con sus datos básicos (marca, modelo, precio, km, CO₂ si se pudo extraer).
4. **Cálculo del IEDMT en base al CO₂** — aunque sea con `original_list_price_es` introducido a mano y sin el resto de costes fijos todavía.
5. **Una pantalla con el desglose**: "esto cuesta en Alemania, esto es el impuesto de matriculación estimado".

Con eso ya tienes algo que demuestra el **diferenciador real** (la cifra fiscal, no solo el agregador) y se lo puedes enseñar a alguien sin necesitar nada de Providers, Transactions o Import todavía.

## Lo que NO está en este recorte

- Sin parsers específicos de mobile.de / AutoScout24 (solo OpenGraph genérico).
- Sin fallback manual completo (se asume URL válida o se reintenta).
- Sin costes fijos (transporte, ITV, DGT) — solo IEDMT.
- Sin alertas de búsqueda.
- Sin favoritos.
- Sin multi-rol (un usuario, un rol implícito = comprador).
- Sin marketplace, sin transacciones, sin providers, sin wizard de import.

## Estimación

1-2 días de trabajo sobre la base ya existente de GarageOS.

## Punto de partida en el plan

- Requisito previo: **Fase 0** completa (multi-rol, estructura de módulos, seeders IEDMT/depreciación).
- Implementación: subset de [fase-1-listings-mvp.md](./parte-2/fases/fase-1-listings-mvp.md) (solo semana 1: captura del anuncio + listado) + subset de [fase-2-tasador-fiscal.md](./parte-2/fases/fase-2-tasador-fiscal.md) (solo `CalculateImportCostAction` con IEDMT, no todo el cálculo B completo).
