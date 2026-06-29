# GarageOS — Migración Frontend: Tailwind 4 + shadcn-vue

> **Objetivo:** Migrar de Tailwind 3 + Headless UI → Tailwind 4 + shadcn-vue
> **Fecha:** 2026-06-29
> **Stack:** Laravel 12 + Inertia 2 + Vue 3 + TypeScript + Pinia

---

## Estado actual ✅ ACTUALIZADO

| Elemento | Versión actual | Destino | Estado |
|---|---|---|---|
| Tailwind CSS | 4.x | 4.x | ✅ Instalado |
| @tailwindcss/postcss | 4.x | 4.x | ✅ Instalado |
| @tailwindcss/forms | 0.5.3 | Eliminar | ⏳ Pendiente |
| @headlessui/vue | 1.7.23 | Eliminar | ⏳ Pendiente |
| PostCSS | 8.x | Mantener | ✅ Config actualizado |
| autoprefixer | 10.x | Mantener | ✅ Config actualizado |

## Componentes actuales → shadcn-vue

| Componente actual | Archivo | Reemplazo shadcn-vue | Notas |
|---|---|---|---|
| PrimaryButton | `Components/PrimaryButton.vue` | `Button` (default) | Variantes via props |
| DangerButton | `Components/DangerButton.vue` | `Button` (destructive) | Unificar en Button |
| SecondaryButton | `Components/SecondaryButton.vue` | `Button` (secondary/outline) | Unificar en Button |
| Card | `Components/Card.vue` | `Card` | CardHeader, CardContent, CardFooter |
| Modal | `Components/Modal.vue` | `Dialog` | DialogContent, DialogHeader, etc. |
| TextInput | `Components/TextInput.vue` | `Input` | v-model compatible |
| Badge | `Components/Badge.vue` | `Badge` | Variantes nativas |
| Dropdown | `Components/Dropdown.vue` | `DropdownMenu` | DropdownMenuTrigger, etc. |
| Table | `Components/Table.vue` | `Table` | TableHeader, TableBody, etc. |
| Select | `Components/Select.vue` | `Select` | SelectTrigger, SelectContent, etc. |
| Checkbox | `Components/Checkbox.vue` | `Checkbox` | Directo |
| InputLabel | `Components/InputLabel.vue` | `Label` | Directo |
| InputError | `Components/InputError.vue` | `FormMessage` / custom | Integrado en Form |

## Vistas a migrar (orden de prioridad)

1. `Layouts/AuthenticatedLayout.vue` — Layout principal
2. `Pages/Dashboard.vue` — Dashboard general
3. `Pages/Vehicle/Index.vue` — Listado vehículos
4. `Pages/Vehicle/Show.vue` — Detalle vehículo
5. `Pages/Workshop/Dashboard.vue` — Dashboard taller
6. Resto de vistas

---

## Fase 1: Preparación e infraestructura ✅ COMPLETADA

### 1.1 Git
```bash
git checkout -b feature/frontend-update
git add -A && git commit -m "chore: checkpoint before frontend migration"
```

### 1.2 Actualizar Tailwind 4 ✅
```bash
cd garageos
npm uninstall tailwindcss @tailwindcss/forms autoprefixer postcss
npm install tailwindcss@latest @tailwindcss/postcss@latest
```

### 1.3 Configuración ✅
- **Eliminado** `tailwind.config.js` (TW4 usa CSS-first config)
- **Actualizado** `postcss.config.js`: usa `@tailwindcss/postcss`
- **Actualizado** `resources/css/app.css`: usa `@import "tailwindcss"` + `@theme`
- **Build exitoso** con Tailwind 4

### 1.4 CSS base (app.css)
```css
@import "tailwindcss";

@theme {
  --font-sans: 'Figtree', ui-sans-serif, system-ui, sans-serif;
  --color-background: oklch(1 0 0);
  --color-foreground: oklch(0.145 0 0);
  --color-card: oklch(1 0 0);
  --color-card-foreground: oklch(0.145 0 0);
  --color-primary: oklch(0.205 0 0);
  --color-primary-foreground: oklch(0.985 0 0);
  --color-secondary: oklch(0.97 0 0);
  --color-secondary-foreground: oklch(0.205 0 0);
  --color-muted: oklch(0.97 0 0);
  --color-muted-foreground: oklch(0.556 0 0);
  --color-accent: oklch(0.97 0 0);
  --color-accent-foreground: oklch(0.205 0 0);
  --color-destructive: oklch(0.577 0.245 27.325);
  --color-destructive-foreground: oklch(0.985 0 0);
  --color-border: oklch(0.922 0 0);
  --color-input: oklch(0.922 0 0);
  --color-ring: oklch(0.708 0 0);
  --color-chart-1: oklch(0.646 0.222 41.116);
  --color-chart-2: oklch(0.6 0.118 184.704);
  --color-chart-3: oklch(0.398 0.07 227.392);
  --color-chart-4: oklch(0.828 0.189 84.429);
  --color-chart-5: oklch(0.769 0.188 70.08);
  --color-sidebar-background: oklch(0.985 0 0);
  --color-sidebar-foreground: oklch(0.145 0 0);
  --color-sidebar-primary: oklch(0.205 0 0);
  --color-sidebar-primary-foreground: oklch(0.985 0 0);
  --color-sidebar-accent: oklch(0.97 0 0);
  --color-sidebar-accent-foreground: oklch(0.205 0 0);
  --color-sidebar-border: oklch(0.922 0 0);
  --color-sidebar-ring: oklch(0.708 0 0);
  --radius: 0.625rem;
}

@layer base {
  * {
    @apply border-border;
  }
  body {
    @apply bg-background text-foreground;
  }
}
```

### 1.5 Dark mode
TW4 usa `@media (prefers-color-scheme: dark)` por defecto. Para toggle manual:
```css
@custom-variant dark (&:is(.dark *));
```
Añadir clase `dark` en `<html>` via JS.

---

## Fase 2: shadcn-vue setup

### 2.1 Inicialización
```bash
npx shadcn-vue@latest init
```
Config:
- Style: new-york
- Icon library: Lucide
- Font: Inter
- Base color: Neutral
- Components path: `@/Components/ui`

### 2.2 Instalar componentes base
```bash
npx shadcn-vue@latest add button card dialog input table badge dropdown-menu select checkbox label
```

### 2.3 Dependencias adicionales ✅
```bash
npm install class-variance-authority clsx tailwind-merge lucide-vue-next radix-vue
```

### 2.4 Utilidad cn() ✅
Creado `resources/js/lib/utils.ts` automáticamente

---

## Fase 3: Migración de componentes

### Estrategia
1. shadcn-vue instala en `Components/ui/` — NO sobreescribe los existentes
2. Migrar vistas una a una, cambiando imports
3. Eliminar componentes legacy cuando todas las vistas migren
4. Mantener `@headlessui/vue` hasta que Dropdown y Modal migren

### 3.1 Orden de migración

**Paso 1 — Utilidades y base** ✅
- [x] Crear `lib/utils.ts`
- [x] Actualizar `app.css` con `@theme`
- [x] Instalar `@tailwindcss/postcss`

**Paso 2 — Componentes shadcn instalados** ✅
- [x] `Button` → `Components/ui/button/`
- [x] `Badge` → `Components/ui/badge/`
- [x] `Input` → `Components/ui/input/`
- [x] `Label` → `Components/ui/label/`
- [x] `Checkbox` → `Components/ui/checkbox/`
- [x] `Card` → `Components/ui/card/`
- [x] `Table` → `Components/ui/table/`
- [x] `Select` → `Components/ui/select/`
- [x] `Dialog` → `Components/ui/dialog/`
- [x] `DropdownMenu` → `Components/ui/dropdown-menu/`

**Paso 3 — Migración de vistas (pendiente)**
- [ ] `AuthenticatedLayout.vue` — usar DropdownMenu, Button
- [ ] `Dashboard.vue` — usar Card, Badge, Table
- [ ] `Vehicle/Index.vue` — usar Table, Badge
- [ ] `Vehicle/Show.vue` — usar Card, Dialog
- [ ] `Workshop/Dashboard.vue` — usar Card, Table

**Paso 4 — Limpieza (pendiente)**
- [ ] Eliminar componentes legacy cuando migración complete
- [ ] Mantener `@headlessui/vue` temporalmente

---

## Fase 4: Validación

- [ ] `npm run dev` — sin errores
- [ ] Responsive en mobile/tablet/desktop
- [ ] Dark mode funcional
- [ ] Accesibilidad (ARIA labels, focus trap en modals)
- [ ] Navegación Inertia funciona
- [ ] Notificaciones push (FCM) funcionan
- [ ] Build sin errores

---

## Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Clases TW3 no compatibles con TW4 | Buscar con grep clases deprecadas |
| Dark mode diferente en TW4 | Usar `@custom-variant dark` |
| shadcn-vue incompatibilidad con Inertia | Probar Dialog dentro de Inertia page |
| Estilos custom se rompen | Migrar CSS custom a `@theme` variables |
| Radix vs Headless UI conflictos | Migrar gradualmente, mantener ambos temporalmente |

---

## Notas técnicas

- shadcn-vue usa **Radix Vue** (no Headless UI) para primitivas accesibles
- Componentes son **copy/paste** en `Components/ui/` — no dependencia runtime
- `class-variance-authority` (CVA) gestiona variantes de componentes
- `cn()` mergea clases Tailwind sin conflictos
- `lucide-vue-next` reemplaza `@heroicons/vue` como iconos
- Mantener `vue-sonner` si shadcn Toast no cubre necesidades
- `recharts` se mantiene para charts complejos