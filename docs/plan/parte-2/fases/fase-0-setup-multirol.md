# Fase 0 — Setup Multi-Rol

**Duración estimada:** 1 semana
**Habilita:** base multi-rol para buyer/seller/provider simultáneos.

## Checklist

- [ ] Migración `user_roles` + modelo `UserRole`
- [ ] Trait `HasRoles` en `User`: `hasRole('buyer')`, `assignRole('provider')`, `roles()`
- [ ] Middleware `EnsureUserHasRole` para proteger rutas de proveedor/admin
- [ ] Crear estructura de carpetas vacía de los 6 módulos nuevos (`Listings`, `Valuations`, `Providers`, `Transactions`, `Messaging`, `Import`)
- [ ] `BuyerLayout.vue` y `ProviderLayout.vue` — copiar patrón de `AppLayout.vue` existente
- [ ] Sidebar adaptativo con switcher de rol (dropdown simple: "Comprador" / "Soy profesional")
- [ ] Seeders de datos oficiales: `iedmt_brackets` y `depreciation_table` (Anexo IV Hacienda + tramos CO₂)
- [ ] Commit: `git add -A && git commit -m "feat: setup multi-rol + estructura v2"`

## Tabla nueva

```sql
CREATE TABLE user_roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    role        ENUM('buyer', 'seller', 'provider', 'admin') NOT NULL,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_user_role (user_id, role)
);
```

> `users.role` original se deja intacto. `user_roles` es la fuente de verdad.

## Verificación

- [ ] Un usuario nuevo puede registrarse como comprador y luego activar el rol de proveedor sin crear cuenta nueva.
- [ ] El sidebar cambia de opciones según el rol activo.
- [ ] `php artisan tinker` → `IedmtBracket::count()` devuelve 4.
- [ ] `DepreciationTable::count()` devuelve las filas del Anexo IV.

## Próxima fase

→ [fase-1-listings-mvp.md](./fase-1-listings-mvp.md)
