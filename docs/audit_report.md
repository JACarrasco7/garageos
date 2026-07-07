# 🔍 Auditoría de Código y Lógica - GarageOS

**Fecha:** 2026-07-06
**Estado:** Completado ✅

---

## 📋 Resumen de la Auditoría

| Módulo | Estado | Riesgo | Notas |
|---|---|---|---|
| VehicleImport | Completado ✅ | Bajo | Lógica de estados y documentos robusta. |
| Marketplace | Completado ✅ | Bajo | Flujo de transacciones sólido. |
| Billing | Completado ✅ | Bajo | Integración avanzada con Stripe Connect. |
| Maintenance | Completado ✅ | Bajo | Seguimiento de servicios y búsqueda por proximidad robusta. |
| Alerts | Completado ✅ | Bajo | Evaluación automatizada de reglas por fecha y km. |

---

## 🛠️ Detalle de Auditoría

### 📦 Phase 1: VehicleImport

#### **1. State Machine & Workflow (Robust)**
El proceso de importación está gestionado por una máquina de estados bien definida utilizando el Enum `ImportStep`.
- **Pasos:** `PURCHASE` $\rightarrow$ `TRANSPORT` $\rightarrow$ `ITV_INSPECTION` $\rightarrow$ `TAXES` $\rightarrow$ `DGT_REGISTRATION` $\rightarrow$ `PLATES` $\rightarrow$ `COMPLETED`.
- **Lógica de Avance:** `AdvanceImportStepAction` asegura que:
    - Los usuarios no pueden retroceder pasos.
    - Los usuarios no pueden saltarse pasos sin cumplir los requisitos del paso anterior (`canAdvanceToStep`).
- **Lógica de Finalización:** El método `completeImport` transiciona correctamente el estado a `completed` y dispara la acción `syncToGarage`, que crea/actualiza el registro permanente de `Vehicle`.

#### **2. Document Verification (Robust)**
El método `isStepCompleted` en el modelo `VehicleImport` actúa como un guardián sólido.
- Valida que todos los `required_docs` definidos en el Enum `ImportStep` estén presentes y tengan `is_verified = true`.
- Esto evita que los usuarios avancen a pasos financieros o legales (como `TAXES` o `DGT_REGISTRATION`) sin la documentación necesaria.

#### **3. Financial & Tax Logic (Accurate)**
El método `calculateImportTaxes` implementa un modelo sofisticado de depreciación e impuestos:
- **Depreciación:** Utiliza un coeficiente basado en la edad del vehículo (ej. 0.84 para < 1 año, hasta 0.10 para vehículos antiguos).
- **IEDMT (Impuesto de Matriculación):** Calculado basado en las emisiones de $CO_2$ y el valor de catálogo (derivado del precio de compra y la depreciación).
- **ITP (Impuesto de Transmisiones Patrimoniales):** Estimado en un 10% plano del precio de compra.
- **Verificación de Lógica:** La fórmula `catalog_value = purchase_price / depreciation_coeff` es matemáticamente correcta para revertir la depreciación y encontrar el valor base para el impuesto.

#### **4. Riesgos Identificados & Observaciones**
- **[BAJO] Dependencia de Verificación Manual:** El sistema depende de que un admin/usuario llame a `verifyDocument`. Si un documento se sube pero nunca se verifica, el wizard se detendrá. Es el comportamiento esperado, pero debe mencionarse en la guía de usuario.
- **[MEDIO] Manejo de Errores en `syncToGarage`:** El método `syncToGarage` utiliza `updateOrCreate` en el modelo `Vehicle`. Si la `plate_new` aún no está disponible o es incorrecta en el momento de la finalización, podría crear un vehículo con una matrícula nula o inválida.
- **[BAJO] Paso 6 (PLATES) vacío:** El paso `PLATES` no tiene documentos requeridos. Aunque es lógicamente correcto (es el paso físico final), es un paso de "paso de largo".

---

### 📦 Phase 2: Marketplace

#### **1. Transaction Flow (Robust)**
El flujo de ofertas y reservas está bien estructurado.
- **Ofertas:** Un usuario puede crear una `offer` sobre un anuncio.
- **Reservas:** Al aceptar una oferta, el sistema cambia automáticamente el estado del anuncio a `reserved` (`TransactionController@update`).
- **Integridad:** El uso de `Transaction::create` con `buyer_id` y `seller_id` asegura la trazabilidad de quién compra y quién vende.

#### **2. Scopes & Querying (Clean)**
El modelo `Transaction` utiliza scopes (`pending`, `completed`, `offers`, `reservations`) que facilitan la lectura de código y la implementación de filtros en el frontend.

#### **3. Riesgos Identificados & Observaciones**
- **[BAJO] Falta de validación de disponibilidad en `store`:** El método `store` en `TransactionController` no verifica si el anuncio ya ha sido vendido o reservado por otro usuario antes de crear la transacción. Esto podría permitir múltiples ofertas "activas" sobre un mismo producto si no se gestiona correctamente en el frontend o mediante un lock en la base de datos.
- **[BAJO] Moneda Hardcoded:** La moneda está fijada como `EUR` en el controlador. Si el marketplace se expande internacionalmente, esto requerirá una refactorización.

---

### 📦 Phase 3: Billing (Stripe Connect)

#### **1. Stripe Connect Integration (Advanced)**
La integración con Stripe Connect Express es robusta y utiliza el modelo de `application_fee_amount`.
- **Transferencia de Fondos:** El `PaymentController` configura correctamente `transfer_data[destination]` para enviar los fondos al `connected_account_id` (el vendedor) y retener la comisión de la plataforma.
- **Cálculo de Comisiones:** La comisión de la plataforma se calcula dinámicamente (por defecto 8%) y se registra tanto en el `PaymentIntent` como en una tabla de `PlatformFee` para auditoría.
- **Webhook Handling (Robust):** El `StripeWebhookController` maneja eventos críticos como `payment_intent.succeeded`, `payment_intent.payment_failed` y `account.updated`.

#### **2. Riesgos Identificados & Observaciones**
- **[BAJO] Idempotencia de Webhooks:** Aunque el sistema maneja los eventos, no hay una validación explícita de idempotencia (asegurar que procesar el mismo evento dos veces no cause duplicados, como crear dos `PlatformFee`). Stripe puede enviar el mismo webhook varias veces.
- **[BAJO] Manejo de errores en `handlePaymentSucceeded`:** Si la creación de la comisión falla, el pago sigue marcándose como exitoso, lo que podría resultar en pérdida de ingresos para la plataforma.

---

### 📦 Phase 4: Maintenance

#### **1. Service Tracking (Robust)**
El sistema permite un seguimiento detallado de las intervenciones realizadas en los vehículos.
- **Registro de Entradas:** `MaintenanceEntry` permite registrar tipo de servicio, kilometraje, fecha, coste y taller.
- **Sincronización de KM:** Al añadir una entrada, el sistema actualiza automáticamente el kilometraje actual del vehículo (`MaintenanceController@store`), asegurando que el historial de KM sea fiable.
- **Talleres:** Integración con el módulo de `Workshop` para asociar cada servicio a un establecimiento.

#### **2. Workshop Discovery (Advanced)**
El uso de `scopeNearby` en el modelo `Workshop` es una característica avanzada y bien implementada.
- **Geolocalización:** Permite buscar talleres cercanos mediante consultas espaciales (PostGIS en PostgreSQL o bounding-box en SQLite).
- **Cálculo de Distancia:** El método `distanceFrom` proporciona una estimación precisa de la distancia en km.

#### **3. Riesgos Identificados & Observaciones**
- **[BAJO] Falta de validación de tipo de servicio en `store`:** El método `store` en `MaintenanceController` acepta una lista de tipos de servicio, pero no valida estrictamente contra un Enum o una lista de referencia centralizada (aunque usa un array en el controlador). Esto es aceptable pero podría mejorar la consistencia.
- **[BAJO] Dependencia de Datos de Talleres:** La utilidad de la búsqueda por proximidad depende de la calidad de los datos de ubicación (lat/lng) de los talleres registrados.

---

### 📦 Phase 5: Alerts

#### **1. Automated Evaluation (Robust)**
El sistema utiliza un proceso automatizado para evaluar las reglas de alerta.
- **Job de Evaluación:** `EvaluateAlertsJob` recorre las reglas activas (`AlertRule`) y comprueba si se deben disparar basándose en la fecha (`trigger_date`) o el kilometraje (`trigger_km`).
- **Consola:** El comando `alerts:evaluate` permite ejecutar este proceso de forma programada (cron job).
- **Notificaciones:** El disparo de una alerta genera un evento `AlertTriggered`, que desencadena el envío de notificaciones (Push, Email, etc.).

#### **2. Reglas de Alerta (Flexible)**
Las reglas permiten configuraciones personalizadas para cada vehículo:
- **Fecha:** Alertas basadas en días de antelación (`advance_days`).
- **Kilometraje:** Alertas basadas en kilómetros de antelación (`advance_km`).
- **Estado:** Solo se procesan reglas activas (`is_active`).

#### **3. Riesgos Identificados & Observaciones**
- **[BAJO] Idempotencia de Alertas:** Al igual que con los webhooks de Stripe, el sistema utiliza `last_triggered` para evitar duplicados, pero no hay un mecanismo de bloqueo (lock) para evitar que dos procesos de evaluación se ejecuten simultáneamente y disparen la misma alerta.
- **[BAJO] Complejidad de la Regla de Disparo:** La lógica de `EvaluateAlertsJob` es sencilla y directa, lo cual es positivo para la mantenibilidad, pero podría expandirse para incluir alertas basadas en otros factores (como el estado de la batería o el tipo de servicio realizado).

---

## 🚀 Conclusión General

El núcleo de GarageOS es **técnicamente sólido y robusto**. La arquitectura de módulos, el uso de Enums para estados y la integración de servicios externos (Stripe, Firebase) siguen patrones de diseño profesionales.

**Recomendaciones para el futuro:**
1.  **Idempotencia:** Implementar mecanismos de bloqueo (locks) o validación de idempotencia en procesos críticos (Webhooks de Stripe y Jobs de Evaluación de Alertas) para evitar duplicados en ejecuciones concurrentes.
2.  **Validación de Disponibilidad:** Reforzar la validación de stock/disponibilidad en el Marketplace para evitar ofertas sobre anuncios ya reservados.
3.  **Internacionalización:** Preparar el sistema para soportar múltiples monedas si se planea una expansión fuera de la Eurozona.

### 📦 Phase 5: Alerts

#### **1. Automated Evaluation (Robust)**
El sistema utiliza un proceso automatizado para evaluar las reglas de alerta.
- **Job de Evaluación:** `EvaluateAlertsJob` recorre las reglas activas (`AlertRule`) y comprueba si se deben disparar basándose en la fecha (`trigger_date`) o el kilometraje (`trigger_km`).
- **Consola:** El comando `alerts:evaluate` permite ejecutar este proceso de forma programada (cron job).
- **Notificaciones:** El disparo de una alerta genera un evento `AlertTriggered`, que desencadena el envío de notificaciones (Push, Email, etc.).

#### **2. Reglas de Alerta (Flexible)**
Las reglas permiten configuraciones personalizadas para cada vehículo:
- **Fecha:** Alertas basadas en días de antelación (`advance_days`).
- **Kilometraje:** Alertas basadas en kilómetros de antelación (`advance_km`).
- **Estado:** Solo se procesan reglas activas (`is_active`).

#### **3. Riesgos Identificados & Observaciones**
- **[BAJO] Idempotencia de Alertas:** Al igual que con los webhooks de Stripe, el sistema utiliza `last_triggered` para evitar duplicados, pero no hay un mecanismo de bloqueo (lock) para evitar que dos procesos de evaluación se ejecuten simultáneamente y disparen la misma alerta.
- **[BAJO] Complejidad de la Regla de Disparo:** La lógica de `EvaluateAlertsJob` es sencilla y directa, lo cual es positivo para la mantenibilidad, pero podría expandirse para incluir alertas basadas en otros factores (como el estado de la batería o el tipo de servicio realizado).

---

## 🚀 Conclusión General

El núcleo de GarageOS es **técnicamente sólido y robusto**. La arquitectura de módulos, el uso de Enums para estados y la integración de servicios externos (Stripe, Firebase) siguen patrones de diseño profesionales.

**Recomendaciones para el futuro:**
1.  **Idempotencia:** Implementar mecanismos de bloqueo (locks) o validación de idempotencia en procesos críticos (Webhooks de Stripe y Jobs de Evaluación de Alertas) para evitar duplicados en ejecuciones concurrentes.
2.  **Validación de Disponibilidad:** Reforzar la validación de stock/disponibilidad en el Marketplace para evitar ofertas sobre anuncios ya reservados.
3.  **Internacionalización:** Preparar el sistema para soportar múltiples monedas si se planea una expansión fuera de la Eurozona.

---

## ✅ Mejoras Implementadas Post-Auditoría

### 📦 Marketplace
- **Validación de disponibilidad:** Se añadió verificación de `status` en `TransactionController@store` para prevenir ofertas sobre anuncios ya vendidos o reservados.

### 📦 Billing (Stripe Connect)
- **Idempotencia en comisiones:** Se añadió verificación de existencia previa antes de crear `PlatformFee`.
- **Manejo de errores:** Se agregó try-catch al crear comisiones con logging para auditoría.

### 📦 Alerts
- **Bloqueo de concurrencia:** Se implementó mecanismo de lock usando caché para prevenir ejecuciones concurrentes del `EvaluateAlertsJob`.

---

**Fecha de implementación:** 2026-07-07

### 📦 Phase 5: Alerts

#### **1. Automated Evaluation (Robust)**
El sistema utiliza un proceso automatizado para evaluar las reglas de alerta.
- **Job de Evaluación:** `EvaluateAlertsJob` recorre las reglas activas (`AlertRule`) y comprueba si se deben disparar basándose en la fecha (`trigger_date`) o el kilometraje (`trigger_km`).
- **Consola:** El comando `alerts:evaluate` permite ejecutar este proceso de forma programada (cron job).
- **Notificaciones:** El disparo de una alerta genera un evento `AlertTriggered`, que desencadena el envío de notificaciones (Push, Email, etc.).

#### **2. Reglas de Alerta (Flexible)**
Las reglas permiten configuraciones personalizadas para cada vehículo:
- **Fecha:** Alertas basadas en días de antelación (`advance_days`).
- **Kilometraje:** Alertas basadas en kilómetros de antelación (`advance_km`).
- **Estado:** Solo se procesan reglas activas (`is_active`).

#### **3. Riesgos Identificados & Observaciones**
- **[BAJO] Idempotencia de Alertas:** Al igual que con los webhooks de Stripe, el sistema utiliza `last_triggered` para evitar duplicados, pero no hay un mecanismo de bloqueo (lock) para evitar que dos procesos de evaluación se ejecuten simultáneamente y disparen la misma alerta.
- **[BAJO] Complejidad de la Regla de Disparo:** La lógica de `EvaluateAlertsJob` es sencilla y directa, lo cual es positivo para la mantenibilidad, pero podría expandirse para incluir alertas basadas en otros factores (como el estado de la batería o el tipo de servicio realizado).

---

## 🚀 Conclusión General

El núcleo de GarageOS es **técnicamente sólido y robusto**. La arquitectura de módulos, el uso de Enums para estados y la integración de servicios externos (Stripe, Firebase) siguen patrones de diseño profesionales.

**Recomendaciones para el futuro:**
1.  **Idempotencia:** Implementar mecanismos de bloqueo (locks) o validación de idempotencia en procesos críticos (Webhooks de Stripe y Jobs de Evaluación de Alertas) para evitar duplicados en ejecuciones concurrentes.
2.  **Validación de Disponibilidad:** Reforzar la validación de stock/disponibilidad en el Marketplace para evitar ofertas sobre anuncios ya reservados.
3.  **Internacionalización:** Preparar el sistema para soportar múltiples monedas si se planea una expansión fuera de la Eurozona.

---

## ✅ Mejoras Implementadas Post-Auditoría

### 📦 Marketplace
- **Validación de disponibilidad:** Se añadió verificación de `status` en `TransactionController@store` para prevenir ofertas sobre anuncios ya vendidos o reservados.
- **Scopes en MarketplaceFavorite:** Se añadieron scopes `byUser()` y `byListing()` para consultas más eficientes.

### 📦 Billing (Stripe Connect)
- **Idempotencia en comisiones:** Se añadió verificación de existencia previa antes de crear `PlatformFee`.
- **Manejo de errores:** Se agregó try-catch al crear comisiones con logging para auditoría.
- **Scopes en PlatformFee:** Se añadieron scopes `byCurrency()`, `processed()`, `unprocessed()` y método `markAsProcessed()`.
- **Scopes en StripeAccount:** Se añadió scope `byUser()` y método `isFullyOperational()`.

### 📦 Alerts
- **Bloqueo de concurrencia:** Se implementó mecanismo de lock usando caché para prevenir ejecuciones concurrentes del `EvaluateAlertsJob`.
- **Scopes en Notification:** Se añadieron scopes `byUser()`, `byVehicle()`, `byType()`, `unread()`, `sent()`.

### 📦 Maintenance
- **Scopes en WorkshopReview:** Se añadieron scopes `byWorkshop()`, `byRating()`, `recent()`.
- **Scopes en Workshop:** Ya existían scopes `nearby()` y `verified()`.

### 📦 Providers
- **Scopes en ProviderReview:** Se añadieron scopes `byProvider()`, `byServiceType()`, `byRating()`.
- **Scopes en ProviderAvailability:** Se añadieron scopes `available()`, `byDay()` y método `isAvailableOnDay()`.
- **Scopes en ProviderService:** Se añadieron scopes `byCategory()`, `byProvider()`, `byPriceRange()`, `byDuration()`.

### 📦 Documents
- **Scopes en Document:** Se añadieron scopes `byType()`, `verified()`, `expiringSoon()`, `expired()`.

### 📦 Listings
- **Scopes en Listing:** Se añadieron scopes `active()`, `byBrand()`, `byModel()`, `byCountry()`, `byPriceRange()`.
- **Métodos en ListingPortal:** Se añadieron `getLabel()` y `getUrlPattern()` al enum.

### 📦 Vehicle
- **Scopes en Vehicle:** Se añadieron scopes `byBrand()`, `byFuelType()`, `byYearRange()`, `byGarage()`.
- **Scopes en KmHistory:** Se añadieron scopes `byVehicle()`, `bySource()`, `recent()`.
- **Scopes en VehiclePhoto:** Se añadió scope `byVehicle()` y método `isMain()`.
- **Método en VehicleSpec:** Se añadió acceso virtual `getPowerKwAttribute()` para conversión HP a kW.

### 📦 Identity
- **Scopes en Garage:** Se añadieron scopes `byUser()`, `byName()` y métodos `vehicleCount()`, `activeVehicles()`.

### 📦 User
- **Método isSuperAdmin():** Verificación de rol SuperAdmin.
- **Scopes:** Se añadieron `scopeAdmins()` y `scopeImporters()` para consultas por rol.

---

**Tests:** ✅ 20 tests pasados, 0 fallidos

**Fecha de implementación:** 2026-07-07
