# Módulo VehicleImport

> Wizard completo para importar vehículos Alemania → España con cálculo automático de impuestos.

---

## 📋 Responsabilidades

- Wizard de 6 pasos para importación DE→ES
- Cálculo automático IEDMT, ITP, IVA según CO₂
- Gestión de placas temporales alemanas
- Alertas de caducidad de placas (30 días)
- Documentos de importación (COC, ficha técnica, etc.)
- Integración con gestoría y proveedores

---

## 📁 Estructura

```
app/Modules/VehicleImport/
├── Models/
│   ├── VehicleImport.php
│   ├── VehicleImportRequest.php
│   ├── VehicleImportOffer.php
│   ├── ImportDocument.php
│   └── TemporaryPlate.php
├── Actions/
│   ├── ImportValuationAction.php
│   ├── AdvanceImportStepAction.php
│   ├── UploadImportDocumentAction.php
│   ├── CreateVehicleImportRequestAction.php
│   ├── AddImportRequestCommentAction.php
│   ├── CreateImportOfferAction.php
│   ├── AcceptImportOfferAction.php
│   ├── RejectImportOfferAction.php
│   ├── ListPublicVehiclesAction.php
│   └── ...
├── Services/
│   └── IedmtCalculatorService.php
├── Enums/
│   └── ImportStep.php            # PURCHASE, TRANSPORT, ITV, TAXES, DGT, PLATES
├── Content/                       # Markdown por paso
│   ├── purchase.md
│   ├── transport.md
│   ├── itv_inspection.md
│   ├── taxes.md
│   ├── dgt_registration.md
│   └── plates.md
├── Events/
│   ├── ImportStepCompleted.php
│   └── TemporaryPlateExpiringSoon.php
├── Jobs/
│   ├── CheckTemporaryPlateExpiryJob.php
│   └── SendPlateExpiryReminderJob.php
├── Http/
│   ├── Controllers/
│   │   ├── VehicleImportController.php
│   │   ├── VehicleImportRequestController.php
│   │   ├── VehicleImportOfferController.php
│   │   └── PublicVehiclePortfolioController.php
│   ├── Requests/
│   └── Resources/
└── Console/
    └── CheckTemporaryPlateExpiryCommand.php
```

---

## 🔄 Flujo del Wizard

```
PURCHASE → TRANSPORT → ITV_INSPECTION → TAXES → DGT_REGISTRATION → PLATES → COMPLETED
   ↓           ↓              ↓            ↓          ↓               ↓
 Compra    Placas temp.    ITV España   IEDMT+ITP   Matriculación   Placas ES
```

### Pasos:

1. **PURCHASE** - Compra en Alemania
   - Datos del vendedor
   - Contrato compraventa
   - COC si está disponible

2. **TRANSPORT** - Transporte a España
   - Placas temporales (Ausfuhrkennzeichen)
   - Fecha de llegada

3. **ITV_INSPECTION** - ITV de importación
   - Cita ITV
   - Ficha técnica española
   - Homologación si no hay COC

4. **TAXES** - Liquidación de impuestos
   - IEDMT (según CO₂)
   - IVA o ITP
   - IVTM

5. **DGT_REGISTRATION** - Matriculación DGT
   - Documentación completa
   - Tasa DGT (~52€)

6. **PLATES** - Placas físicas definitivas
   - Matrícula española generada
   - Fotos finales

---

## 💰 Cálculo de Impuestos (IedmtCalculatorService)

```php
use App\Modules\VehicleImport\Services\IedmtCalculatorService;

$service = app(IedmtCalculatorService::class);
$result = $service->calculate([
    'purchase_price' => 18000,
    'year' => 2019,
    'co2_emissions' => 160,
    'origin_country' => 'DE',
    'is_new' => false,
]);

// Result:
// [
//   'iedmt' => 955.50,
//   'itp' => 1800.00,
//   'total' => 2755.50,
//   'breakdown' => [...]
// ]
```

---

## ⏰ Alertas de Caducidad

- **Job diario:** `CheckTemporaryPlateExpiryJob`
- **Recordatorios:** 7, 3, 1 días antes de expirar
- **Email + notificación in-app**

---

## 🌐 API Endpoints

```http
# Público (portfolio)
GET  /api/v1/public/imports/vehicles

# Autenticado
GET    /imports
POST   /imports
GET    /imports/{id}
PUT    /imports/{id}/step
POST   /imports/{id}/documents
POST   /imports/{id}/plates

# Requests (solicitudes a gestorías)
GET    /imports/requests
POST   /imports/requests
POST   /imports/requests/{id}/offers
POST   /imports/requests/{id}/accept
```

---

## 🔗 Documentos relacionados

- [Guía completa DE→ES](../modules/VEHICLE-IMPORT-GUIDE.md)
- [Plan de sourcing](../modules/VEHICLE-IMPORT-PLAN.md)
- [URLs oficiales DGT, Hacienda](https://sede.dgt.gob.es)
