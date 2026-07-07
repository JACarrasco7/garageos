# Módulo Documents

> Gestión de documentos de vehículos con OCR automático (Tesseract).

---

## 📋 Responsabilidades

- Subida de documentos (PDF, imágenes)
- OCR con Tesseract (español + inglés)
- Extracción automática de datos (fecha, importe, tipo)
- Vinculación con vehículos
- Detección de tipo de mantenimiento desde factura

---

## 📁 Estructura

```
app/Modules/Documents/
├── Models/
│   └── Document.php
├── Actions/
│   └── UploadDocumentAction.php
├── Jobs/
│   └── ParseDocumentJob.php
├── Listeners/
│   └── CreateMaintenanceFromDocument.php
├── Http/Controllers/
│   └── DocumentController.php
└── Providers/
```

---

## 🗄️ Modelo Document

```php
- id
- vehicle_id (FK, nullable)
- user_id (FK)
- type (enum: itv, insurance, factura, permiso, ficha_tecnica, otro)
- title (string)
- file_path (string)
- mime_type (string)
- size_bytes (int)
- document_date (date, nullable)
- expiry_date (date, nullable)
- ocr_text (text, nullable)
- ocr_extracted_data (json, nullable)  # {date, amount, vendor, etc}
- created_at, updated_at
```

---

## 🤖 Flujo OCR

1. Usuario sube documento
2. `UploadDocumentAction` guarda el archivo
3. Se dispara `ParseDocumentJob` (queue)
4. Tesseract extrae texto (esp+eng)
5. Regex/heurísticas extraen datos estructurados
6. `CreateMaintenanceFromDocument` listener crea mantenimiento si es factura

---

## 🔍 Extracción de Datos

Patrones regex aplicados al texto OCR:

| Campo | Patrón |
|-------|--------|
| Fecha | `\d{1,2}[/-]\d{1,2}[/-]\d{2,4}` |
| Importe | `\d+[,\.]\d{2}\s*€` |
| Matrícula | `\d{4}\s?[A-Z]{3}` |
| VIN | `[A-HJ-NPR-Z0-9]{17}` |
| Tipo aceite | `5W-?30\|10W-?40` |

---

## 🌐 API Endpoints

```http
GET    /vehicles/{id}/documents
POST   /vehicles/{id}/documents        # multipart/form-data
GET    /documents/{id}
DELETE /documents/{id}
```

---

## 📂 Almacenamiento

- **Local:** `storage/app/documents/{vehicle_id}/`
- **Media Library:** `Spatie\MediaLibrary` para variantes (thumbnails)
- **En producción:** S3 / DigitalOcean Spaces

---

## 🧪 Tests

- OCR con imagen de prueba
- Extracción de fecha e importe
- Creación automática de mantenimiento desde factura
