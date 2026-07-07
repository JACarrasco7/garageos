# Arquitectura Modular

> Estructura modular de GarageOS para mantener código organizado y desacoplado.

---

## 🎯 Filosofía

Cada **módulo** agrupa toda la lógica de un dominio específico:

- Models
- Controllers
- Actions (lógica de negocio)
- Services (servicios compartidos)
- Events / Listeners / Jobs
- Policies
- Routes (si tiene API dedicada)
- Content (markdown, enums)
- Tests

---

## 📁 Estructura

```
app/Modules/
├── Identity/              # Usuarios, Garajes, Auth
│   ├── Models/
│   ├── Providers/
│   └── Policies/
├── Vehicle/               # Vehículos y specs
│   ├── Models/
│   ├── Http/Controllers/
│   ├── Events/
│   ├── Actions/
│   └── Services/
├── VehicleImport/         # Importación DE→ES
│   ├── Models/
│   ├── Actions/
│   ├── Services/
│   ├── Enums/
│   ├── Content/           # Markdown por paso
│   ├── Events/
│   ├── Jobs/
│   ├── Console/
│   └── Http/
├── Maintenance/           # Service Packs, intervalos
│   ├── Models/
│   ├── Actions/
│   └── Content/
├── Marketplace/           # Compraventa
│   ├── Models/
│   ├── Actions/
│   ├── Services/
│   ├── Events/
│   └── Policies/
├── Documents/             # Documentos + OCR
│   ├── Models/
│   ├── Actions/
│   ├── Jobs/
│   └── Listeners/
├── Alerts/                # Reglas de alertas
│   ├── Models/
│   ├── Events/
│   ├── Listeners/
│   └── Jobs/
└── Providers/             # Service Providers compartidos
```

---

## 🔧 Service Provider por módulo

Cada módulo se registra mediante un ServiceProvider:

```php
// app/Modules/Vehicle/Providers/VehicleServiceProvider.php
class VehicleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Cargar rutas del módulo
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        // Registrar policies
        Gate::policy(Vehicle::class, VehiclePolicy::class);

        // Registrar observers
        Vehicle::observe(VehicleObserver::class);
    }
}
```

Registro en `bootstrap/providers.php`:

```php
return [
    App\Modules\Identity\Providers\IdentityServiceProvider::class,
    App\Modules\Vehicle\Providers\VehicleServiceProvider::class,
    App\Modules\VehicleImport\Providers\VehicleImportServiceProvider::class,
    App\Modules\Maintenance\Providers\MaintenanceServiceProvider::class,
    App\Modules\Marketplace\Providers\MarketplaceServiceProvider::class,
    App\Modules\Documents\Providers\DocumentServiceProvider::class,
    App\Modules\Alerts\Providers\AlertsServiceProvider::class,
];
```

---

## 📐 Convenciones

### Models
- Nombre en singular: `Vehicle`, `VehicleImport`
- Primary key `id`
- Usa `casts()` para tipos complejos (json, dates)
- Usa `Factory` para tests

### Actions
- Una acción = una operación de negocio
- Sufijo `Action`: `CreateListingAction`
- Recibe DTOs primitivos, devuelve modelos
- Inyectables vía constructor

```php
class CreateListingAction
{
    public function execute(Vehicle $vehicle, array $data): MarketplaceListing
    {
        return DB::transaction(function () use ($vehicle, $data) {
            $listing = MarketplaceListing::create([...]);
            event(new ListingCreated($listing));
            return $listing;
        });
    }
}
```

### Services
- Lógica compartida por varias Actions
- Stateless
- Inyectables

### Events
- Nombre en pasado: `VehicleRegistered`, `OfferReceived`
- Payload ligero (solo IDs)
- Listeners en `app/Listeners/`

### Content
- Markdown por paso del wizard
- Accedido desde Controllers para renderizar UI

---

## 🚦 Comunicación entre módulos

### ✅ Permitido
- Vía **Events** (desacoplado)
- Vía **Services públicos** (inyección)
- Vía **relaciones Eloquent**

### ❌ Evitar
- Importar Models de otro módulo directamente en Actions
- Acoplar Controllers de un módulo con lógica de otro
- Usar `app(Model::class)` desde fuera del módulo

---

## 🧪 Tests por módulo

```
tests/
├── Feature/
│   ├── Vehicle/
│   │   ├── VehicleRegistrationTest.php
│   │   ├── VinDecodeTest.php
│   │   └── QrPublicAccessTest.php
│   ├── VehicleImport/
│   └── ...
└── Unit/
    ├── Vehicle/
    ├── VehicleImport/
    └── ...
```

---

## 📚 Recursos

- Ver [STACK.md](STACK.md) para versiones
- Ver [DATABASE.md](DATABASE.md) para esquema
- Ver [docs/modules/](../modules/) para detalle de cada módulo
