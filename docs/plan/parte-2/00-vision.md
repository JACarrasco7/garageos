# Parte II / 00 — Visión y Por Qué v2 Reemplaza al Borrador Anterior

## Tres asunciones del borrador original que no se sostienen

Antes de tocar una línea de código, conviene dejar por escrito qué se corrigió al planificar v2 — porque cambian el diseño de varios módulos:

### 1. AutoUncle no cobra comisión al comprador
Su negocio real es vender herramientas de generación de leads y analítica a concesionarios. El buscador es gratis para el usuario final y es un **imán de tráfico**, no la fuente de ingresos. Si GarageOS v2 quiere monetizar por comisión de servicio (revisor, transportista, gestoría), es un modelo de negocio *distinto* al de su referente — no una copia.

### 2. No existe un atajo gratuito y estable para tener un catálogo masivo desde el día 1
- **mobile.de** tiene Search-API oficial pero requiere acuerdo de partner comercial — no hay self-service. El patrón documentado (Seller-API) está reservado a concesionarios registrados y Transfer Service Providers.
- **AutoScout24** es peor: su API pública solo permite *publicar* anuncios, no leerlos. Extraer datos existentes solo es vía scraping, y su web está protegida con **Akamai Bot Manager** (fingerprinting TLS/JS, bloqueo agresivo).
- El propio mercado lo confirma: existen empresas (Apify, Scrapfly, Piloterr...) que cobran por scraping asumiendo el coste de mantenimiento y el riesgo de bloqueo.

### 3. El wizard de matriculación DE→ES real tiene 5-6 pasos, no 10
El trámite oficial (DGT + fuentes especializadas) es: **comprar → transportar → ITV de importación → impuestos → matricular → placas físicas**. La homologación solo aplica si falta el Certificado de Conformidad (COC) o el vehículo es anterior a 2002.

## Decisiones adoptadas (validadas)

- **Listings**: el catálogo propio nace vacío y crece orgánicamente — cada usuario que pega la URL de un anuncio lo añade. **No hay scraping masivo en el MVP.** Puerta abierta a feed/scraping de pago en [fase-8](../fases/fase-8-futuro.md) cuando haya tracción.
- **Valuation (tasador)**: combina dos señales — valor de mercado (anuncios ya analizados en la plataforma) **y** coste fiscal real de matricular en España (tabla Hacienda + tramos CO₂ del IEDMT). **Diferenciador real** frente a cualquier comparador genérico.
- **Import Wizard**: 6 pasos reales, basados en el trámite oficial. No un wizard genérico inventado.
- **Monetización**: comisión del 8% por servicio intermediado vía Stripe Connect. **Modelo distinto** al B2B-leads de AutoUncle.

## Países e idioma del MVP

- **Países:** empieza con **DE→ES** (los más relevantes y mejor documentados). AT/NL/FR como ampliación futura si la demanda lo justifica.
- **Idioma:** **ES** en el MVP. La infraestructura i18n ya existe (ES/EN/CA) para el Import Wizard si se decide activar.
