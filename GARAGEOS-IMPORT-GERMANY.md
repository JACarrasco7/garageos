# GarageOS — Importación de Vehículos (Alemania → España)

> **Guía completa del trámite oficial DE→ES**
> **Última actualización:** Julio 2026
> **Base legal:** Reglamento (UE) 2018/858 + Ley 21/2001 + BOE IEDMT tramos CO₂

---

## Tabla de contenidos

1. [Resumen del proceso](#resumen-del-proceso)
2. [Paso 1: Compra en Alemania](#paso-1-compra-en-alemania)
3. [Paso 2: Transporte a España](#paso-2-transporte-a-españa)
4. [Paso 3: ITV de importación](#paso-3-itv-de-importación)
5. [Paso 4: Liquidación de impuestos](#paso-4-liquidación-de-impuestos)
6. [Paso 5: Matriculación DGT](#paso-5-matriculación-dgt)
7. [Paso 6: Placas físicas](#paso-6-placas-físicas)
8. [Costes totales estimados](#costes-totales-estimados)
9. [Plazos clave](#plazos-clave)
10. [Documentos checklist](#documentos-checklist)
11. [Casos especiales](#casos-especiales)
12. [URLs oficiales](#urls-oficiales-completas)
13. [Implementación en GarageOS](#implementación-en-garageos)

---

## Resumen del proceso

El proceso completo de importar un vehículo desde Alemania a España consiste en 6 pasos secuenciales:

```
Compra DE → Transporte → ITV ES → Impuestos → DGT → Placas → "Mi garaje"
    ↓          ↓          ↓          ↓       ↓       ↓
  1 día      1-3 días   1 día      5-7 días  2-3 días  1 día
```

**Tiempo total estimado:** 2-4 semanas desde la compra hasta tener el coche matriculado en España.

**Coste total estimado:** 1.500-5.000€ (sin incluir el precio del vehículo)

---

## Paso 1: Compra en Alemania

### Documentos a obtener

#### 1.1 Contrato de compraventa (Kaufvertrag)
- **Obligatorio:** Sí
- **Quién lo emite:** Vendedor (concesionario o particular)
- **Coste:** 0-50€
- **Contenido mínimo:**
  - Datos del comprador y vendedor
  - Marca, modelo, VIN, matrícula original
  - Precio de compra
  - Fecha de compra
  - Kilometraje real
  - Firmas de ambas partes
- **URL plantilla oficial ADAC:** https://www.adac.de/rund-ums-fahrzeug/autoverkauf-kauf/kaufvertrag/

#### 1.2 Certificado de Conformidad (COC)
- **Obligatorio:** Recomendado (evita homologación)
- **Quién lo emite:** Fabricante del vehículo
- **Coste:** 0-150€
- **Qué incluye:** Datos técnicos homologados UE, emisiones CO₂, peso, potencia

#### 1.3 Ficha técnica alemana (Fahrzeugbrief)
- **Obligatorio:** Sí
- **Quién lo emite:** Kraftfahrt-Bundesamt (KBA)
- **Coste:** Incluido en el vehículo

#### 1.4 Tarjeta ITV/TÜV alemana (TÜV-Bericht)
- **Obligatorio:** Sí
- **Quién la emite:** TÜV, DEKRA, GTÜ, KÜS
- **Coste:** Incluido

### Verificaciones antes de pagar

- **Historial kilometraje:** https://www.dat.de/ (~10-15€)
- **Historial siniestros:** https://www.carfax.eu/ (~20-30€)
- **Inspección pre-compra:** ~100-200€ (opcional)

### URLs útiles

- **ADAC - Guía compra:** https://www.adac.de/rund-ums-fahrzeug/autokauf/
- **Verificar vehículo DE:** https://www.kba.de/DE/Service/Fahrzeug/Fahrzeug.html
- **Precios de mercado:** https://www.mobile.de/

---

## Paso 2: Transporte a España

### Opción A: Placas temporales alemanas (Ausfuhrkennzeichen)

**Proceso:**
1. Vendedor gestiona placas en Zulassungsstelle
2. Placas rojas temporales (04, 05, 06...)
3. Seguro obligatorio incluido (1 mes)

**Costes:**
- Placas: ~25-35€
- Seguro: ~100-150€
- Tasa: ~20-30€
- **Total:** ~150-215€

**Validez:** 1-2 meses (prorrogable 1 vez)

**URL:** https://www.adac.de/rund-ums-fahrzeug/zulassung/kennzeichen/ausfuerrkennzeichen/

### Opción B: Plataleta/Transportista

**Costes:**
- Plataleta con conductor: ~500-900€
- Grúa sin conductor: ~300-600€
- Transportista profesional: ~800-1.500€

**Distancias típicas:**
- Berlín → Madrid: ~1.200-1.500€
- Múnich → Barcelona: ~800-1.200€
- Frankfurt → Sevilla: ~600-900€

**Plataformas:**
- https://www.clicktrans.es/
- https://www.uShip.es/

### Llegada a España

- Registrar fecha de llegada en GarageOS como `arrival_date`
- Tienes **30 días** para pasar ITV de importación

---

## Paso 3: ITV de importación

### Documentación obligatoria

1. Ficha técnica alemana (Fahrzeugbrief)
2. Tarjeta ITV/TÜV alemana
3. COC o certificado homologación
4. Contrato compraventa
5. DNI/NIE titular

### Proceso

**1. Cita previa**
- URL general: https://www.itv.es/
- Citas online por comunidad

**2. En la estación ITV**
- Inspección: ~1-2 horas
- Coste: ~60-80€
- Resultados: 4-5 días laborales

### Si NO tienes COC

**Homologación por ingeniero:**
- Coste: ~300-600€
- Plazo: ~1-2 semanas
- Lista ingenierías: https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/homologacion-de-vehiculos/

### URLs por comunidad

- Madrid: https://www.itv-citaprevia.es/
- Cataluña: https://www.gencat.cat/
- Andalucía: https://www.juntadeandalucia.es/transporte/movilidad/itv/
- (ver comunidad específica)

---

## Paso 4: Liquidación de impuestos

### 4.1 IEDMT - Impuesto Especial sobre Determinados Medios de Transporte

**Base imponible:** Valor fiscal (precio catálogo ES × depreciación)

**Coeficientes depreciación (Hacienda Anexo IV):**

| Antigüedad | Coeficiente |
|------------|-------------|
| < 1 año    | 0,84        |
| 1-2 años   | 0,67        |
| 2-3 años   | 0,56        |
| 3-4 años   | 0,47        |
| 4-5 años   | 0,39        |
| 5-6 años   | 0,33        |
| 6-7 años   | 0,28        |
| 7-8 años   | 0,24        |
| 8-9 años   | 0,18        |
| 9-10 años  | 0,14        |
| 10+ años   | 0,10        |

**Tramos CO₂ (g/km):**

| Emisiones CO₂ | Tipo impositivo |
|---------------|-----------------|
| 0-120         | 0,00%           |
| 121-159       | 4,75%           |
| 160-199       | 9,75%           |
| 200+          | 14,75%          |

**Cálculo:**
```
Valor fiscal = Precio catálogo ES × Coeficiente
IEDMT = Valor fiscal × Tipo según CO₂
```

**URL tramos oficiales:** https://www.boe.es/buscar/act.php?id=BOE-A-2023-12921

**Gestión:**
- **Modelo:** 576
- **URL:** https://sede.agenciatributaria.gob.es/acciona05i/Inicio.html
- **Plazo:** 30 días desde entrada en España

**Exenciones:**
- Vehículos históricos (> 25 años)
- Vehículos adaptados discapacidad
- Vehículos eléctricos (0%)

### 4.2 IVA o ITP

**A) Vehículo nuevo (< 6 meses o < 6.000 km)**
- **Impuesto:** IVA 21%
- **Modelo:** 309/300
- **URL:** https://sede.agenciatributaria.gob.es/

**B) Vehículo usado (≥ 6 meses y ≥ 6.000 km)**
- **Impuesto:** ITP autonómico (10-12%)
- **URL por comunidad:**
  - Andalucía: https://www.juntadeandalicia.es/serviciosonline/
  - Madrid: https://sede.comunidad.madrid/
  - Cataluña: https://sede.gencat.cat/
  - Comunidad Valenciana: https://sede.gva.es/

### 4.3 IVTM - Impuesto municipal anual

- **Gestión:** Ayuntamiento residencia
- **Coste:** 50-200€/año

### Ejemplo práctico

BMW 320i (2019, 6 años, CO₂ 160 g/km, precio 18.000€):
- Valor fiscal: 35.000€ × 0,28 = 9.800€
- IEDMT: 9.800€ × 9,75% = 955€
- ITP: 18.000€ × 10% = 1.800€
- IVTM: ~120€
- **Total impuestos:** 2.875€

---

## Paso 5: Matriculación DGT

### Documentación final

1. ✅ Contrato compraventa (original)
2. ✅ Ficha técnica española (ITV)
3. ✅ COC (original)
4. ✅ Justificante pago IEDMT
5. ✅ Justificante pago IVA/ITP
6. ✅ Justificante pago IVTM
7. ✅ DNI/NIE titular
8. ✅ Tarjeta ITV/TÜV alemana
9. ✅ Ficha técnica alemana
10. ✅ Seguro obligatorio contratado

### Proceso

**Opción A: Presencial**
1. Cita previa: https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/matriculacion/
2. Presentar documentación
3. Pagar tasa: ~52€
4. Obtener permiso circulación

**Opción B: Online**
- Requiere certificado digital o Cl@ve
- URL: https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/matriculacion/matriculacion-ordinaria/

### Seguro obligatorio

- Terceros: ~250-400€/año
- Terceros ampliado: ~300-500€/año
- Todo riesgo: ~600-1.200€/año

**Comparadores:**
- https://www.rastreator.com/
- https://www.kelisto.es/

---

## Paso 6: Placas físicas

### Proceso

1. **Encargar placas**
   - Taller autorizado o cerrajería
   - Coste: ~30-50€

2. **Colocar placas**
   - Delantera y trasera
   - Obligatorio en vehículo matriculado
   - Plazo: máx 5 días

3. **Registro final**
   - Fotos del coche
   - ¡LISTO PARA CIRCULAR!

---

## Costes totales estimados

### Desglose por paso

| Paso | Concepto | Coste estimado |
|------|----------|----------------|
| 1 - Compra | Contrato, verificaciones | 0-100€ |
| 1 - Compra | COC | 0-150€ |
| 2 - Transporte | Placas temporales + seguro | 150-215€ |
| 2 - Transporte | Plataleta | 500-1.500€ |
| 3 - ITV | ITV importación | 60-80€ |
| 3 - ITV | Homologación (si sin COC) | 300-600€ |
| 4 - Impuestos | IEDMT | 0-3.000€ |
| 4 - Impuestos | IVA/ITP | 1.000-3.000€ |
| 4 - Impuestos | IVTM | 50-200€ |
| 5 - DGT | Tasa matriculación | ~52€ |
| 5 - DGT | Seguro | 250-1.200€ |
| 6 - Placas | Placas físicas | 30-50€ |

### Rangos totales

**Sin homologación (con COC):**
- Mínimo: ~1.500€ (coche pequeño, bajo CO₂, transporte propio)
- Medio: ~2.500€ (coche medio, CO₂ medio)
- Máximo: ~5.000€ (coche grande, alto CO₂)

**Con homologación:**
- Añadir ~300-600€

### Ejemplos reales

**BMW 320i (2019, 6 años, CO₂ 160, 18.000€)**
- Total: 4.337€

**VW Golf TDI (2015, 10 años, CO₂ 130, 12.000€)**
- Total: 2.262€

**Porsche 911 (2020, 5 años, CO₂ 240, 80.000€)**
- Total: 13.562€

---

## Plazos clave

### Plazos legales

| Evento | Plazo | Consecuencia |
|--------|-------|--------------|
| Llegada a España | Día 0 | Inicio plazo |
| ITV de importación | 30 días | Sanción + no matricular |
| IEDMT | 30 días | Recargo + intereses |
| IVA/ITP | 30 días | Recargo + intereses |
| Colocar placas | 5 días | Sanción |

### Timeline típico

```
Día 1:       Compra en Alemania
Día 1-3:     Transporte a España
Día 4-10:    ITV de importación
Día 11-17:   Impuestos
Día 18-20:   Matriculación DGT
Día 21:      Placas físicas
Día 22:      ¡LISTO!
```

---

## Documentos checklist

### Paso 1 - Compra
- [ ] Contrato compraventa (Kaufvertrag)
- [ ] Certificado de Conformidad (COC)
- [ ] Ficha técnica alemana (Fahrzeugbrief)
- [ ] Tarjeta ITV/TÜV alemana
- [ ] Factura compra (si concesionario)

### Paso 2 - Transporte
- [ ] Placas temporales (si aplica)
- [ ] Seguro transporte (si aplica)
- [ ] Factura transportista (si aplica)
- [ ] Fotos llegada

### Paso 3 - ITV
- [ ] Ficha técnica alemana (original)
- [ ] Tarjeta ITV/TÜV (original)
- [ ] COC (original)
- [ ] Contrato compraventa (original)
- [ ] DNI/NIE titular
- [ ] Ficha técnica española (resultado)

### Paso 4 - Impuestos
- [ ] Modelo 576 (IEDMT)
- [ ] Justificante pago IEDMT
- [ ] Modelo 309/300 (IVA) o liquidación ITP
- [ ] Justificante pago IVA/ITP
- [ ] Justificante pago IVTM

### Paso 5 - Matriculación DGT
- [ ] Contrato compraventa
- [ ] Ficha técnica española
- [ ] COC
- [ ] Justificante IEDMT
- [ ] Justificante IVA/ITP
- [ ] Justificante IVTM
- [ ] DNI/NIE titular
- [ ] Seguro contratado
- [ ] Permiso circulación

### Paso 6 - Placas
- [ ] Placas colocadas
- [ ] Fotos finales

---

## Casos especiales

### Vehículos eléctricos
- 0% de IEDMT
- Bonificaciones IVTM
- Verificar compatibilidad cargadores

### Vehículos híbridos
- Híbrido no enchufable: Según CO₂ (normalmente 0-4,75%)
- PHEV: Según CO₂ en modo ICE

### Vehículos históricos (> 25 años)
- Exención IEDMT
- ITP reducido
- Seguro especial más barato

### Vehículos con daños
- Presupuesto reparaciones antes de comprar
- Verificar repuestos en España
- Posibles problemas en ITV

---

## URLs oficiales completas

### Agencias españolas

**DGT:**
- Matriculación: https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/matriculacion/matriculacion-ordinaria/
- ITV: https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/inspecciones-tecnicas/itv/
- Homologación: https://sede.dgt.gob.es/es/tramites-y-multas/vehiculo/homologacion-de-vehiculos/

**Hacienda:**
- Modelo 576: https://sede.agenciatributaria.gob.es/acciona05i/Inicio.html
- Modelo 309/300: https://sede.agenciatributaria.gob.es/
- Tramos CO₂: https://www.boe.es/buscar/act.php?id=BOE-A-2023-12921

**ITV:**
- General: https://www.itv.es/
- Buscar estación: https://www.itv.es/itv-web/

### Fuentes alemanas

**ADAC:**
- Compra coche: https://www.adac.de/rund-ums-fahrzeug/autokauf/
- Placas temporales: https://www.adac.de/rund-ums-fahrzeug/zulassung/kennzeichen/ausfuerrkennzeichen/
- Contrato: https://www.adac.de/rund-ums-fahrzeug/autoverkauf-kauf/kaufvertrag/

**KBA:**
- Verificar vehículo: https://www.kba.de/DE/Service/Fahrzeug/Fahrzeug.html

**DAT:**
- Historial kilometraje: https://www.dat.de/

**Mobile.de:**
- Precios mercado: https://www.mobile.de/

---

## Implementación en GarageOS

### Estructura de módulo

El módulo `VehicleImport` debe implementar el wizard de 6 pasos:

```
app/Modules/VehicleImport/
├── Models/
│   ├── VehicleImport.php           (import_case)
│   ├── ImportDocument.php
│   └── TemporaryPlate.php
├── Actions/
│   ├── ValidateGermanPlateAction.php
│   ├── GenerateSpanishPlateAction.php
│   ├── ProcessImportAction.php
│   ├── AdvanceImportStepAction.php
│   └── UploadImportDocumentAction.php
├── Enums/
│   └── ImportStep.php              (PURCHASE, TRANSPORT, ITV_INSPECTION, TAXES, DGT_REGISTRATION, PLATES)
├── Content/                        (markdown por paso)
│   ├── purchase.md
│   ├── transport.md
│   ├── itv_inspection.md
│   ├── taxes.md
│   ├── dgt_registration.md
│   └── plates.md
├── Http/
│   ├── Controllers/
│   │   ├── VehicleImportController.php
│   │   └── ImportDocumentController.php
│   ├── Requests/
│   │   ├── StoreVehicleImportRequest.php
│   │   └── UpdateImportStepRequest.php
│   └── Resources/
│       └── VehicleImportResource.php
├── Events/
│   ├── ImportStepCompleted.php
│   └── TemporaryPlateExpiringSoon.php
├── Jobs/
│   ├── CheckTemporaryPlateExpiryJob.php
│   └── SendPlateExpiryReminderJob.php
├── Console/
│   └── CheckTemporaryPlateExpiryCommand.php
└── Providers/
    └── VehicleImportServiceProvider.php
```

### Base de datos

```sql
CREATE TABLE vehicle_imports (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             BIGINT UNSIGNED NOT NULL,
    vehicle_id          BIGINT UNSIGNED NULL,
    listing_id          BIGINT UNSIGNED NULL,
    plate_original      VARCHAR(20) NULL COMMENT 'matrícula alemana',
    plate_new           VARCHAR(10) NULL COMMENT 'matrícula española definitiva',
    brand               VARCHAR(50) NOT NULL,
    model               VARCHAR(80) NOT NULL,
    year                YEAR NULL,
    engine_cc           INT NULL,
    power_kw            INT NULL,
    co2_emissions       INT NULL COMMENT 'g/km, clave para IEDMT',
    origin_country      VARCHAR(2) DEFAULT 'DE',
    purchase_date       DATE NULL,
    arrival_date        DATE NULL COMMENT 'dispara plazo 30 días ITV',
    itv_deadline        DATE NULL COMMENT 'arrival_date + 30 días',
    current_step        ENUM('purchase','transport','itv_inspection','taxes','dgt_registration','plates','completed') DEFAULT 'purchase',
    needs_homologation  BOOLEAN DEFAULT FALSE,
    status              ENUM('pending','processing','approved','rejected') DEFAULT 'pending',
    documents           JSON NULL,
    rejection_reason    TEXT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    INDEX idx_user_step (user_id, current_step),
    INDEX idx_plate_original (plate_original)
);

CREATE TABLE import_documents (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    import_id       BIGINT UNSIGNED NOT NULL COMMENT 'FK a vehicle_imports',
    step            ENUM('purchase','transport','itv_inspection','taxes','dgt_registration','plates') NOT NULL,
    type            ENUM(
                        'compraventa','coc','ficha_tecnica_origen','tarjeta_itv_origen',
                        'seguro_transporte','ficha_itv_es','modelo_576','modelo_309_300',
                        'modelo_itp','justificante_ivtm','permiso_circulacion','otro'
                    ) NOT NULL,
    file_path       VARCHAR(255) NOT NULL,
    is_verified     BOOLEAN DEFAULT FALSE,
    uploaded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_id) REFERENCES vehicle_imports(id) ON DELETE CASCADE,
    INDEX idx_import_step (import_id, step)
);

CREATE TABLE temporary_plates (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    import_id       BIGINT UNSIGNED NOT NULL,
    plate_number    VARCHAR(20) NOT NULL,
    issued_at       DATE NOT NULL,
    expires_at      DATE NOT NULL COMMENT 'normalmente issued_at + 2 meses',
    is_extended     BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_id) REFERENCES vehicle_imports(id) ON DELETE CASCADE,
    INDEX idx_expiry (expires_at)
);
```

### Migraciones pendientes

El módulo `VehicleImport` existe pero está incompleto. Faltan:

1. **Enum ImportStep** con los 6 pasos
2. **Contenido markdown** por paso (basado en esta guía)
3. **ImportDocument model** + migración
4. **TemporaryPlate model** + migración
5. **AdvanceImportStepAction**
6. **UploadImportDocumentAction**
7. **Jobs de alertas de caducidad placas temporales**
8. **Command de check diario**
9. **Frontend: ImportStepper.vue**
10. **Frontend: DocumentChecklist.vue**
11. **Frontend: PlateExpiryBanner.vue**

---

## Notas legales

⚠️ **Disclaimer:** Esta guía es informativa y se basa en fuentes oficiales (julio 2026). La normativa puede cambiar. Consulta siempre las fuentes primarias (DGT, Hacienda, ayuntamiento) para trámites oficiales.

---

## Próximos pasos

Para usar esta guía en GarageOS:

1. ✅ **Completar esta guía** (HECHO)
2. 🔄 **Implementar módulo VehicleImport completo**
3. 📝 **Crear contenido markdown por paso**
4. ✅ **Implementar checklists interactivos**
5. ⏰ **Añadir alertas automáticas de plazos**
6. 💰 **Implementar cálculo automático impuestos**
7. 🔧 **Integrar con módulo Providers** (sugerir transportistas, gestorías)

Esta guía es la fuente de verdad del contenido del Import Wizard.

> **Objetivo:** Módulo para importar vehículos alemanes a España con validación de matriculación
> **Fecha:** 30 Junio 2026

---

## Fase 1: Modelos y migraciones

### 1.1 VehicleImport model
```php
// app/Modules/VehicleImport/Models/VehicleImport.php
- plate_original (DE)
- plate_new (ES)
- brand, model, year
- import_date
- status: pending|processing|approved|rejected
- documents: JSON (ficha técnica, permiso importación)
- user_id
```

### 1.2 Migration
```bash
php artisan make:migration create_vehicle_imports_table
```

---

## Fase 2: Actions

### 2.1 ValidateGermanPlateAction
- Validar formato matrícula alemana (ej: B-XX-1234)
- Verificar que no esté en España

### 2.2 GenerateSpanishPlateAction
- Generar matrícula española válida
- Formato: 1234-ABC

### 2.3 ProcessImportAction
- Crear Vehicle con datos importados
- Asignar matrícula española
- Generar documentos de importación

---

## Fase 3: Controllers

### 3.1 VehicleImportController
- `index()` — Listar importaciones
- `create()` — Formulario importación
- `store()` — Guardar solicitud
- `show()` — Detalle importación
- `generateDocs()` — Generar documentos

---

## Fase 4: Rutas

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/imports', [VehicleImportController::class, 'index'])->name('imports.index');
    Route::get('/imports/create', [VehicleImportController::class, 'create'])->name('imports.create');
    Route::post('/imports', [VehicleImportController::class, 'store'])->name('imports.store');
    Route::get('/imports/{import}', [VehicleImportController::class, 'show'])->name('imports.show');
    Route::post('/imports/{import}/generate', [VehicleImportController::class, 'generateDocs'])->name('imports.generate');
});
```

---

## Fase 5: Vistas Vue

### 5.1 ImportVehicle.vue
- Formulario con datos del vehículo
- Subida de documentos (ficha técnica)
- Preview matrícula española

### 5.2 ImportShow.vue
- Estado de la importación
- Documentos generados
- Botón descarga PDF

---

## Fase 6: Tests

```bash
php artisan make:test VehicleImportTest --unit
```

- Validar matrícula alemana
- Generar matrícula española
- Procesar importación completa

---

## Fase 7: Integración con Marketplace

- Usar `ScrapeMarketValueAction` para valorar vehículo importado
- `CalculateScoreAction` para puntuación
- `GenerateCertificateAction` para certificado de importación

---

## Estado actual

| Tarea | Estado |
|---|---|
| Modelo VehicleImport | ⏳ Pendiente |
| Migration | ⏳ Pendiente |
| Actions | ⏳ Pendiente |
| Controller | ⏳ Pendiente |
| Rutas | ⏳ Pendiente |
| Vistas Vue | ⏳ Pendiente |
| Tests | ⏳ Pendiente |