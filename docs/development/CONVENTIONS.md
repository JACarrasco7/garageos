# Convenciones de Código

> Estilo y convenciones del proyecto GarageOS.

---

## 🎯 Principios

1. **KISS** - Keep It Simple, Stupid
2. **DRY** - Don't Repeat Yourself
3. **YAGNI** - You Aren't Gonna Need It
4. **Readability counts** - El código se lee más de lo que se escribe

---

## 🐘 PHP / Laravel

### Estilo de código
- PSR-12 estricto
- Laravel Pint para formateo automático
- `vendor/bin/pint` antes de commit

### Naming
- **Models**: singular, PascalCase (`Vehicle`, `VehicleImport`)
- **Controllers**: PascalCase + suffix (`VehicleController`)
- **Actions**: verbo + sustantivo + `Action` (`CreateListingAction`)
- **Services**: sustantivo + `Service` (`IedmtCalculatorService`)
- **Jobs**: verbo + sustantivo + `Job` (`ParseDocumentJob`)
- **Events**: pasado (`VehicleRegistered`, `OfferReceived`)
- **Listeners**: presente (`SendEmailNotification`)
- **Requests**: acción + `Request` (`StoreVehicleRequest`)
- **Resources**: modelo + `Resource` (`VehicleResource`)

### Validación
- **Siempre** con FormRequest, nunca inline
- Reglas en método `rules()` del Request
- Mensajes custom en `messages()`

```php
// ✅ Correcto
class StoreVehicleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'plate' => 'required|string|max:10|unique:vehicles,plate',
            'brand' => 'required|string|max:50',
        ];
    }
}

// ❌ Incorrecto
public function store(Request $request)
{
    $data = $request->validate([
        'plate' => 'required|string',
    ]);
}
```

### Eager Loading
- **Obligatorio** para evitar N+1
- Usar `with()` en relaciones que se muestran

```php
// ✅ Correcto
$vehicles = Vehicle::with(['specs', 'documents'])->get();

// ❌ Incorrecto (N+1)
$vehicles = Vehicle::all();
foreach ($vehicles as $v) {
    echo $v->specs->engine_cc;
}
```

### Tests
- Pest PHP, sintaxis moderna
- Happy path + validación + edge cases
- Cobertura: nuevas features = 100% test coverage

```php
test('vehicle can be created with valid data', function () {
    $data = Vehicle::factory()->make()->toArray();
    $response = $this->post('/api/vehicles', $data);
    $response->assertCreated();
});
```

---

## 🔐 Roles y Suscripciones

### Verificar acceso
```php
// Roles
$user->isAdmin();
$user->isImporter();
$user->hasRole('admin');

// Features
$user->hasFeature('marketplace.create');
$user->hasFeature('imports.receive');

// Límites
$user->canCreateVehicle();
$user->getVehicleLimit();
```

### Middleware
```php
// Por rol
Route::middleware('role:importer')->group(fn() => [...]);

// Por feature
Route::middleware('subscription:marketplace.create')->group(fn() => [...]);
```

---

## 🎨 Vue 3 / TypeScript

### Estilo
- Composition API + `<script setup lang="ts">`
- TypeScript estricto
- ESLint + Prettier (configurados)

### Naming
- **Components**: PascalCase (`AppSidebar.vue`)
- **Props/Emits**: camelCase
- **Composables**: `use` prefix (`useAuth`)

### Componentes
- **shadcn-vue** en `Components/ui/` (no editar)
- **Reutilizables** en `Components/`
- **Páginas** en `Pages/` con Inertia

### TypeScript
- Interfaces en `resources/js/types/`
- Tipos explícitos en props

```vue
<script setup lang="ts">
import type { Vehicle } from '@/types'

interface Props {
    vehicle: Vehicle
}
defineProps<Props>()
</script>
```

### Tailwind 4
- Usar **colores semánticos**: `bg-card`, `text-foreground`, `text-muted-foreground`
- **NUNCA** `text-gray-800`, `bg-blue-500`
- `cn()` de `@/lib/utils` para merge de clases

```vue
<!-- ✅ Correcto -->
<Button class="bg-primary text-primary-foreground">

<!-- ❌ Incorrecto -->
<Button class="bg-blue-500 text-white">
```

### Iconos
- **lucide-vue-next** exclusivamente
- Nombres descriptivos

```vue
<script setup>
import { Car, Wrench, FileText } from 'lucide-vue-next'
</script>
```

---

## 🗄️ Base de datos

### Migrations
- Una migración por tabla/cambio
- Nombres descriptivos: `create_vehicles_table`, `add_qr_token_to_vehicles`
- Usar tipos nativos PostgreSQL cuando sea posible (jsonb)

### Models
- `$fillable` explícito
- `$casts` para tipos complejos
- Relaciones con tipos return explícitos

```php
class Vehicle extends Model
{
    protected $fillable = ['plate', 'brand', 'model', 'year', 'current_km'];

    protected $casts = [
        'purchase_date' => 'date',
        'images' => 'array',
    ];

    public function specs(): HasOne
    {
        return $this->hasOne(VehicleSpec::class);
    }
}
```

---

## 📁 Estructura de archivos

```
app/Modules/{ModuleName}/
├── Actions/              # Lógica de negocio
├── Content/              # Markdown/enums
├── Console/              # Comandos Artisan
├── Events/               # Eventos
├── Http/
│   ├── Controllers/      # Controllers
│   ├── Requests/         # FormRequests
│   └── Resources/        # API Resources
├── Jobs/                 # Queue jobs
├── Listeners/            # Event listeners
├── Models/               # Eloquent models
├── Policies/             # Authorization policies
├── Providers/            # Service providers
├── Services/             # Servicios
└── Tests/                # Pest tests
```

---

## 🔀 Git Workflow

### Branches
- `main` - Producción
- `develop` - Desarrollo
- `feature/*` - Features nuevas
- `fix/*` - Bugfixes
- `hotfix/*` - Fixes urgentes

### Commits
Mensajes en inglés con prefijo conventional:

```
feat: add VIN decoder service
fix: correct IEDMT calculation for hybrid vehicles
docs: update README with Docker setup
refactor: extract valuation logic to service
test: add coverage for ImportValuationAction
chore: update dependencies
```

### Pull Requests
- 1 feature = 1 PR
- Pasar todos los tests
- Code review obligatorio
- Squash merge a develop

---

## 🔒 Seguridad

- **NUNCA** commitear `.env`
- **NUNCA** hardcodear credenciales
- Usar variables de entorno
- Validar TODAS las entradas
- Sanitizar output HTML

---

## 📊 Performance

- Eager loading siempre
- Cachear queries pesadas
- Queue jobs para tareas largas
- Índices en columnas frecuentemente consultadas
- Paginación en listados

---

## 🧪 Testing

```bash
# Todos los tests
php artisan test

# Solo unit
php artisan test --testsuite=Unit

# Solo feature
php artisan test --testsuite=Feature

# Un test específico
php artisan test --filter=VehicleImportRequestTest

# Con coverage
php artisan test --coverage
```

---

## 📚 Referencias

- [Laravel Best Practices](https://laravel.com/docs)
- [Vue 3 Style Guide](https://vuejs.org/style-guide/)
- [shadcn-vue](https://www.shadcn-vue.com/)
- [Tailwind CSS](https://tailwindcss.com/docs)
