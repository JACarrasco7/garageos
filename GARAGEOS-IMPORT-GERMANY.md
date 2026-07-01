# GarageOS — Importación de Vehículos (Alemania → España)

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