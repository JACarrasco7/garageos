# Sistema de Usuarios, Roles y Suscripciones

## Arquitectura

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     Roles       │     │   Planes        │     │  Features       │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ superadmin      │     │ free (€0)       │     │ marketplace.*   │
│ admin           │     │ pro (€9.99)     │     │ imports.*       │
│ importer        │     │ importer (€19.99)│    │ vehicles.*      │
│ user            │     └─────────────────┘     └─────────────────┘
└─────────────────┘
```

## Roles Disponibles

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **superadmin** | Super administrador | Acceso total, gestión de claves Stripe, roles |
| **admin** | Administrador del sistema | Acceso completo a dashboard, gestión de usuarios, roles |
| **importer** | Importador de vehículos | Crear anuncios, gestionar importaciones, recibir ofertas |
| **user** | Usuario normal | Ver dashboard, gestionar perfil y vehículos |

## Planes de Suscripción

| Plan | Precio | Límite vehículos | Marketplace | Importaciones |
|------|--------|------------------|-------------|---------------|
| **Gratuito** | €0 | 3 | Ver | ❌ |
| **Pro** | €9.99/mes | Ilimitado | Crear/comprar | Crear |
| **Importador** | €19.99/mes | Ilimitado | Todo | Todo + recibir ofertas |

## Features Matrix

| Feature | Free | Pro | Importer |
|---------|------|-----|----------|
| `vehicles.create` | ✅ (3 max) | ✅ (∞) | ✅ (∞) |
| `marketplace.view` | ✅ | ✅ | ✅ |
| `marketplace.create` | ❌ | ✅ | ✅ |
| `marketplace.buy` | ❌ | ✅ | ✅ |
| `imports.create` | ❌ | ✅ | ✅ |
| `imports.receive` | ❌ | ❌ | ✅ |
| `documents.ocr` | ❌ | ✅ | ✅ |
| `reports.pdf` | ❌ | ✅ | ✅ |

## Modelos

### User
```php
// Verificar rol
$user->isAdmin()       // true si es admin
$user->isImporter()   // true si es importador
$user->isUser()       // true si es usuario normal

// Verificar feature
$user->hasFeature('marketplace.create')
$user->hasFeature('imports.receive')

// Límites
$user->getVehicleLimit()     // Límite de vehículos
$user->canCreateVehicle()    // Puede crear más vehículos?
```

### Contact (tabla separada)
```php
$user->contact;  // Relación HasOne
$user->contact->is_public;  // Visibilidad
```

## Middleware

### RoleMiddleware
```php
Route::middleware('role:importer')->group(function () { ... });
```

### SubscriptionMiddleware
```php
Route::middleware('subscription:marketplace.create')->group(function () { ... });
```

## Comandos Artisan

```bash
# Convertir usuario a importador
php artisan user:make-importer user@email.com
php artisan user:make-importer user@email.com --force
```

## Flujo de Registro

1. Usuario se registra → rol `user` asignado automáticamente
2. Usuario elige plan → se crea suscripción
3. Si elige `importer` → rol cambiado a `importer`
4. Al login → listener sincroniza roles según suscripción

## Archivos Clave

| Archivo | Propósito |
|---------|-----------|
| `config/subscription.php` | Matrix de features y planes |
| `app/Models/User.php` | Métodos de acceso |
| `app/Models/Contact.php` | Datos de contacto separados |
| `app/Http/Middleware/SubscriptionMiddleware.php` | Control de acceso |
| `app/Listeners/AssignRoleOnSubscription.php` | Sincronización de roles |
| `app/Console/Commands/MakeImporterCommand.php` | Conversión a importador |

## Tests

- 27 tests pasados
- 0 fallidos
- Cobertura de roles, features y límites
