# GarageOS

Laravel 12 + Inertia 2 + Vue 3 + shadcn-vue + Tailwind 4. Módulos: Vehicle, Maintenance, Alerts, Documents, Identity, Marketplace.

## Convenciones
- FormRequests para validación (no inline)
- Eager loading obligatorio
- Tests: happy path + validación + edge cases
- Código sin comentarios obvios

## Frontend
- **shadcn-vue** componentes en `@/Components/ui/` — NO editar directamente, usar CLI
- **Tailwind 4** CSS-first config — NO tailwind.config.js
- Colores semánticos: `text-foreground`, `bg-card`, `text-muted-foreground`, etc. — NUNCA `text-gray-800`
- Iconos: `lucide-vue-next`
- `cn()` de `@/lib/utils` para merge de clases
- Botones como Link: `<Button as-child><Link ...>...</Link></Button>`
- Añadir componentes: `npx shadcn-vue@latest add [name] --yes`

## Mapeo legacy → shadcn-vue
| Legacy | shadcn-vue |
|---|---|
| PrimaryButton | Button (default) |
| DangerButton | Button (destructive) |
| SecondaryButton | Button (secondary) |
| Card (custom) | Card + CardHeader + CardContent |
| Modal | Dialog + DialogContent |
| TextInput | Input |
| Badge (custom) | Badge |
| Dropdown | DropdownMenu |
| Table (custom) | Table + subcomponentes |

## Tokens
- `#file:` en vez de pegar contenido
- `/compact` tras 30 mensajes
- `codebase-memory-mcp` para exploración estructural
