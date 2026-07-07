# Guía Técnica: Sistema de Roles y Suscripciones

## Instalación

El paquete `spatie/laravel-permission` ya está instalado en `composer.json`.

## Migraciones

### 1. Tablas de Spatie Permission
```bash
php artisan migrate
```
Crea: `roles`, `permissions`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`

### 2. Tabla de Contactos
```bash
php artisan migrate
```
Crea: `contacts` (tabla separada para datos de contacto)

## Seeders

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SuperAdminSeeder
```

Crea los roles:
- `superadmin` - Super administrador (acceso total)
- `admin` - Administrador
- `importer` - Importador
- `user` - Usuario normal

### SuperAdmin
Usuario por defecto:
- Email: `jacararsco@garageos.com`
- Password: `admin`
- Rol: `superadmin`

## Configuración

### config/permission.php
```php
return [
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        // ...
    ],
    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],
];
```

### config/subscription.php
```php
return [
    'features' => [
        'vehicles.create' => ['plans' => ['free', 'pro', 'importer'], 'limits' => ['free' => 3]],
        'marketplace.create' => ['plans' => ['pro', 'importer']],
        // ...
    ],
    'plans' => [
        'free' => ['name' => 'Gratuito', 'price' => 0, ...],
        'pro' => ['name' => 'Pro', 'price' => 999, ...],
        'importer' => ['name' => 'Importador', 'price' => 1999, ...],
    ],
];
```

## Uso en Código

### Asignar rol al usuario
```php
$user->assignRole('user');
```

### Verificar rol
```php
$user->hasRole('importer');
$user->isAdmin();
$user->isImporter();
```

### Verificar feature
```php
$user->hasFeature('marketplace.create');
```

### Verificar límite
```php
$user->canCreateVehicle();
$user->getVehicleLimit();
```

## Middleware

### RoleMiddleware
```php
Route::middleware('role:importer')->group(function () {
    // Solo importadores
});
```

### SubscriptionMiddleware
```php
Route::middleware('subscription:marketplace.create')->group(function () {
    // Solo usuarios con suscripción que tengan el feature
});
```

## Comandos Artisan

```bash
# Convertir usuario a importador
php artisan user:make-importer email@dominio.com
php artisan user:make-importer email@dominio.com --force
```

## Listener: AssignRoleOnSubscription

Escucha el evento `Login` y sincroniza los roles según la suscripción activa.

## Testing

```php
// Crear usuario con rol
$user = $this->createUserWithRole('importer');

// Crear usuario con suscripción
$user = $this->createUserWithSubscription('pro');
```
