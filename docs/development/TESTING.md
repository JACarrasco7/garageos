# Testing

> Estrategia y herramientas de testing para GarageOS.

---

## 🧪 Stack de Testing

- **Pest PHP 3** - Framework principal
- **PHPUnit** - Bajo el capó de Pest
- **Mockery** - Mocking de dependencias
- **Faker** - Datos aleatorios realistas
- **Factories** - Generadores de modelos

---

## 📁 Estructura

```
tests/
├── Pest.php                       # Configuración global
├── TestCase.php                   # TestCase base
├── Feature/                       # Tests de integración
│   ├── Vehicle/
│   │   ├── VehicleRegistrationTest.php
│   │   ├── VinDecodeTest.php
│   │   └── PublicQrAccessTest.php
│   ├── VehicleImport/
│   ├── Maintenance/
│   ├── Marketplace/
│   └── ...
└── Unit/                          # Tests unitarios
    ├── Vehicle/
    ├── VehicleImport/
    │   ├── ImportValuationActionTest.php
    │   └── IedmtCalculatorServiceTest.php
    └── ...
```

---

## 🚀 Comandos

```bash
# Todos los tests
php artisan test

# Con paralelización
php artisan test --parallel

# Solo Unit
php artisan test --testsuite=Unit

# Solo Feature
php artisan test --testsuite=Feature

# Test específico
php artisan test --filter=VehicleImportRequestTest

# Con coverage
php artisan test --coverage

# Solo tests modificados
php artisan test --changed-since=main
```

---

## 📝 Convenciones

### Naming
- Tests descriptivos que explican el comportamiento
- `test('user can register a vehicle')` o `it('redirects unauthenticated users')`

### Estructura AAA
```php
test('vehicle registration creates qr token', function () {
    // Arrange
    $user = User::factory()->create();
    $data = ['plate' => '1234ABC', 'brand' => 'Toyota'];

    // Act
    $response = $this->actingAs($user)->post('/vehicles', $data);

    // Assert
    $response->assertCreated();
    expect(Vehicle::where('plate', '1234ABC')->first())
        ->not->toBeNull()
        ->and(Vehicle::where('plate', '1234ABC')->first()->qr_token)
        ->not->toBeNull();
});
```

### Cobertura obligatoria
- Happy path
- Validación (datos inválidos)
- Edge cases (límites, valores extremos)
- Errores esperados

---

## 🏭 Factories

### Crear factory
```bash
php artisan make:factory VehicleFactory
```

### Definir factory
```php
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plate' => $this->faker->unique()->regexify('[0-9]{4}[A-Z]{3}'),
            'brand' => $this->faker->randomElement(['Toyota', 'BMW', 'Seat']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2010, 2025),
            'current_km' => $this->faker->numberBetween(1000, 200000),
            'qr_token' => Str::uuid(),
        ];
    }

    public function electric(): self
    {
        return $this->state(['fuel_type' => 'electric']);
    }
}
```

### Usar factory
```php
// Crear un modelo
$vehicle = Vehicle::factory()->create();

// Crear varios
$vehicles = Vehicle::factory()->count(5)->create();

// Con estado custom
$electric = Vehicle::factory()->electric()->create();

// Sin persistir
$draft = Vehicle::factory()->make();
```

---

## 🔄 Mocking

### Mockear dependencias externas
```php
use App\Services\CarDataService;

test('vin decoder returns vehicle specs', function () {
    $mock = Mockery::mock(CarDataService::class);
    $mock->shouldReceive('decodeVin')
        ->with('WBA3A5C50CF256123')
        ->andReturn(['make' => 'BMW', 'model' => '320i']);

    $this->app->instance(CarDataService::class, $mock);

    // ...
});
```

### Fake HTTP calls
```php
use Illuminate\Support\Facades\Http;

test('vin decoder handles nhtsa api', function () {
    Http::fake([
        'vpic.nhtsa.dot.gov/*' => Http::response([
            'Results' => [
                ['Make' => 'BMW', 'Model' => '320i']
            ]
        ], 200)
    ]);

    $service = app(CarDataService::class);
    $result = $service->decodeVin('WBA3A5C50CF256123');

    expect($result['make'])->toBe('BMW');
});
```

---

## 🗄️ Refresh Database

Pest usa `RefreshDatabase` automáticamente. Para cada test:

```php
uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup común a todos los tests
    $this->user = User::factory()->create();
});
```

---

## 🎯 Tests por tipo

### Feature (integración)
```php
test('user can register a vehicle', function () {
    $response = $this->actingAs($this->user)
        ->post('/vehicles', [
            'plate' => '1234ABC',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
        ]);

    $response->assertCreated();
    $this->assertDatabaseHas('vehicles', ['plate' => '1234ABC']);
});
```

### Unit (lógica aislada)
```php
test('iedmt calculator returns correct rate for co2', function () {
    $calc = app(IedmtCalculatorService::class);

    $result = $calc->calculate([
        'purchase_price' => 18000,
        'year' => 2019,
        'co2_emissions' => 160,
    ]);

    expect($result['iedmt'])->toBeGreaterThan(0);
});
```

---

## 📊 Coverage

```bash
# Generar reporte HTML
php artisan test --coverage --min=80

# Cobertura por archivo
php artisan test --coverage --coverage-html=storage/coverage
```

Apertura `storage/coverage/index.html` en navegador.

---

## 🚦 CI/CD

### GitHub Actions (ejemplo)
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with:
          node-version: '20'
      - run: composer install
      - run: npm ci
      - run: php artisan test --parallel
```

---

## 📚 Recursos

- [Pest PHP Docs](https://pestphp.com/)
- [Laravel Testing](https://laravel.com/docs/testing)
- [PHPUnit](https://phpunit.readthedocs.io/)
