# GarageOS — Migración Frontend: Tailwind 4 + shadcn-vue

> **Objetivo:** Migrar de Tailwind 3 + Headless UI → Tailwind 4 + shadcn-vue
> **Fecha:** 2026-06-29
> **Stack:** Laravel 13 + Inertia 2 + Vue 3 + TypeScript + Pinia
> **Estado:** ✅ **MIGRACIÓN COMPLETADA**

---

## Estado final ✅

- ✅ **Tailwind 4** con `@tailwindcss/vite` plugin (sin PostCSS config)
- ✅ **16 componentes shadcn-vue instalados** en `Components/ui/`
- ✅ **32+ vistas migradas** a la nueva estructura Pages/
- ✅ **Componentes legacy eliminados** (PrimaryButton, DangerButton, Modal, etc.)
- ✅ **Dependencias legacy eliminadas** (`@headlessui/vue`, `@heroicons/vue`)
- ✅ **Build verificado funcionando** — `npm run build` exitoso
- ✅ **Skill creada** en `~/.copilot/skills/garageos-frontend/`
- ✅ **copilot-instructions actualizado** en `.github/copilot-instructions.md`

---

## Estado actual ✅ COMPLETADO

| Elemento | Versión actual | Destino | Estado |
|---|---|---|---|
| Tailwind CSS | 4.x (`@tailwindcss/vite`) | 4.x | ✅ Completado |
| @tailwindcss/vite | 4.3.2 | 4.x | ✅ Completado |
| @tailwindcss/forms | Eliminado | — | ✅ Eliminado |
| @headlessui/vue | Eliminado | — | ✅ Eliminado |
| @heroicons/vue | Eliminado | — | ✅ Eliminado |
| PostCSS | Eliminado (Vite plugin) | — | ✅ Eliminado |
| autoprefixer | Eliminado (Vite plugin) | — | ✅ Eliminado |

## Componentes shadcn-vue instalados (16)

| Componente | Ruta | Uso principal |
|---|---|---|
| `Button` | `Components/ui/button/` | Acciones, formularios |
| `Badge` | `Components/ui/badge/` | Estados, etiquetas |
| `Input` | `Components/ui/input/` | Campos de formulario |
| `Label` | `Components/ui/label/` | Etiquetas de campos |
| `Checkbox` | `Components/ui/checkbox/` | Selección booleana |
| `Card` | `Components/ui/card/` | Contenedores de contenido |
| `Table` | `Components/ui/table/` | Datos tabulares |
| `Select` | `Components/ui/select/` | Dropdown de selección |
| `Dialog` | `Components/ui/dialog/` | Modales, confirmaciones |
| `DropdownMenu` | `Components/ui/dropdown-menu/` | Menús contextuales |
| `Avatar` | `Components/ui/avatar/` | Imágenes de usuario |
| `Form` | `Components/ui/form/` | Validación integrada |
| `Separator` | `Components/ui/separator/` | Divisores visuales |
| `Sheet` | `Components/ui/sheet/` | Paneles laterales |
| `Sonner` | `Components/ui/sonner/` | Notificaciones toast |
| `Tabs` | `Components/ui/tabs/` | Navegación por pestañas |

## Vistas migradas (32+ archivos)

### Layouts
- ✅ `Layouts/AuthenticatedLayout.vue`
- ✅ `Layouts/GuestLayout.vue`

### Auth
- ✅ `Pages/Auth/Login.vue`
- ✅ `Pages/Auth/Register.vue`
- ✅ `Pages/Auth/ForgotPassword.vue`
- ✅ `Pages/Auth/ResetPassword.vue`
- ✅ `Pages/Auth/ConfirmPassword.vue`
- ✅ `Pages/Auth/VerifyEmail.vue`

### Dashboard
- ✅ `Pages/Dashboard.vue`
- ✅ `Pages/Dashboard/Stats.vue`

### Vehicle
- ✅ `Pages/Vehicle/Index.vue`
- ✅ `Pages/Vehicle/Show.vue`
- ✅ `Pages/Vehicle/Create.vue`
- ✅ `Pages/Vehicle/Edit.vue`
- ✅ `Pages/Vehicle/Import.vue`
- ✅ `Pages/Vehicle/Public.vue`

### Workshop
- ✅ `Pages/Workshop/Dashboard.vue`

### Maintenance
- ✅ `Pages/Maintenance/Index.vue`
- ✅ `Pages/Maintenance/Show.vue`
- ✅ `Pages/Maintenance/Create.vue`
- ✅ `Pages/Maintenance/Workshops.vue`

### Otras páginas
- ✅ `Pages/Welcome.vue`
- ocuments/` (vistas)
- ✅ `Pages/Marketplace/` (vistas)
- ✅ `Pages/Profile/` (vistas)
- ✅ `Pages/Subscription/` (vistas)

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
npm install -D tailwindcss@latest @tailwindcss/vite@latest
```

### 1.3 Configuración ✅
- **Eliminado** `tailwind.config.js` (TW4 usa CSS-first config)
- **Eliminado** `postcss.config.js` (Vite plugin lo reemplaza)
- **Actualizado** `vite.config.js`: añadido `@tailwindcss/vite` plugin
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

## Fase 2: shadcn-vue setup ✅ COMPLETADA

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

### 2.2 Componentes instalados (16) ✅
```bash
npx shadcn-vue@latest add button badge input label checkbox card table select dialog dropdown-menu avatar form separator sheet sonner tabs
```

### 2.3 Dependencias adicionales ✅
```bash
npm install class-variance-authority clsx tailwind-merge lucide-vue-next reka-ui
```

### 2.4 Utilidad cn() ✅
Creado `resources/js/lib/utils.ts` automáticamente

---

## Fase 3: Migración de componentes ✅ COMPLETADA

### Estrategia ejecutada
1. shadcn-vue instaló en `Components/ui/` — NO sobreescribió los existentes
2. Vistas migradas una a una, cambiando imports
3. Componentes legacy eliminados cuando todas las vistas migraron
4. `@headlessui/vue` y `@heroicons/vue` eliminados del package.json

### 3.1 Utilidades y base ✅
- [x] Crear `lib/utils.ts`
- [x] Actualizar `app.css` con `@theme`
- [x] Instalar `@tailwindcss/vite`

### 3.2 Componentes shadcn instalados ✅
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
- [x] `Avatar` → `Components/ui/avatar/`
- [x] `Form` → `Components/ui/form/`
- [x] `Separator` → `Components/ui/separator/`
- [x] `Sheet` → `Components/ui/sheet/`
- [x] `Sonner` → `Components/ui/sonner/`
- [x] `Tabs` → `Components/ui/tabs/`

### 3.3 Migración de vistas ✅
- [x] `AuthenticatedLayout.vue` — usar DropdownMenu, Button
- [x] `GuestLayout.vue` — usar Button, Card
- [x] `Dashboard.vue` — usar Card, Badge, Table
- [x] `Dashboard/Stats.vue` — usar Card
- [x] `Vehicle/Index.vue` — usar Table, Badge
- [x] `Vehicle/Show.vue` — usar Card, Dialog
- [x] `Vehicle/Create.vue` — usar Form, Input, Label
- [x] `Vehicle/Edit.vue` — usar Form, Input, Label
- [x] `Vehicle/Import.vue` — usar Button, Input
- [x] `Vehicle/Public.vue` — usar Card, Badge
- [x] `Workshop/Dashboard.vue` — usar Card, Table
- [x] `Maintenance/Index.vue` — usar Table, Badge
- [x] `Maintenance/Show.vue` — usar Card
- [x] `Maintenance/Create.vue` — usar Form, Input
- [x] `Maintenance/Workshops.vue` — usar Card, Table
- [x] `Auth/Login.vue` — usar Form, Input, Button
- [x] `Auth/Register.vue` — usar Form, Input, Button
- [x] `Auth/ForgotPassword.vue` — usar Form, Input, Button
- [x] `Auth/ResetPassword.vue` — usar Form, Input, Button
- [x] `Auth/ConfirmPassword.vue` — usar Form, Input, Button
- [x] `Auth/VerifyEmail.vue` — usar Button
- [x] `Welcome.vue` — usar Button, Card
- [x] Resto de vistas (Documents, Marketplace, Profile, Subscription)

### 3.4 Limpieza ✅
- [x] Eliminar componentes legacy cuando migración completó
- [x] Eliminar `@headlessui/vue` de package.json
- [x] Eliminar `@heroicons/vue` de package.json
- [x] Eliminar `tailwind.config.js`
- [x] Eliminar `postcss.config.js`

---

## Fase 4: Validación ✅ COMPLETADA

- [x] `npm run dev` — sin errores
- [x] `npm run build` — build exitoso
- [x] Responsive en mobile/tablet/desktop
- [x] Dark mode funcional
- [x] Accesibilidad (ARIA labels, focus trap en modals)
- [x] Navegación Inertia funciona
- [x] Notificaciones push (FCM) funcionan
- [x] Build sin errores

---

## Limpieza final

### Archivos eliminados
- `tailwind.config.js` — reemplazado por CSS-first config en `app.css`
- `postcss.config.js` — reemplazado por `@tailwindcss/vite` plugin
- `resources/js/Components/PrimaryButton.vue` — reemplazado por `Button`
- `resources/js/Components/DangerButton.vue` — reemplazado por `Button` (destructive)
- `resources/js/Components/SecondaryButton.vue` — reemplazado por `Button` (secondary/outline)
- `resources/js/Components/Card.vue` — reemplazado por `Card` shadcn
- `resources/js/Components/Modal.vue` — reemplazado por `Dialog` shadcn
- `resources/js/Components/TextInput.vue` — reemplazado por `Input` shadcn
- `resources/js/Components/Badge.vue` — reemplazado por `Badge` shadcn
- `resources/js/Components/Dropdown.vue` — reemplazado por `DropdownMenu` shadcn
- `resources/js/Components/Table.vue` — reemplazado por `Table` shadcn
- `resources/js/Components/Select.vue` — reemplazado por `Select` shadcn
- `resources/js/Components/Checkbox.vue` — reemplazado por `Checkbox` shadcn
- `resources/js/Components/InputLabel.vue` — reemplazado por `Label` shadcn
- `resources/js/Components/InputError.vue` — integrado en `Form` shadcn

### Dependencias eliminadas
- `@headlessui/vue` — reemplazado por Radix Vue (via shadcn-vue)
- `@heroicons/vue` — reemplazado por `lucide-vue-next`
- `@tailwindcss/forms` — integrado en TW4
- `autoprefixer` — integrado en Vite
- `postcss` — integrado en `@tailwindcss/vite`

### Dependencias añadidas
- `tailwindcss@4.3.2`
- `@tailwindcss/vite@4.3.2`
- `class-variance-authority@0.7.1`
- `clsx@2.1.1`
- `tailwind-merge@3.6.0`
- `lucide-vue-next@1.0.0`
- `reka-ui@2.10.1` (primitivas Radix Vue)
- `@lucide/vue@1.22.0`
- `tw-animate-css@1.4.0`

---

## Cómo continuar (nuevas features)

### Añadir un nuevo componente shadcn-vue
```bash
cd garageos
npx shadcn-vue@latest add <component-name>
```
Ejemplo: `npx shadcn-vue@latest add calendar toast command`

### Crear una nueva vista
1. Crear archivo en `resources/js/Pages/<Module>/`
2. Usar componentes de `Components/ui/` (import via `@/Components/ui/...`)
3. Usar `cn()` desde `@/lib/utils` para clases condicionales
4. Layout automático via `AuthenticatedLayout` o `GuestLayout`

### Patrón de importación
```vue
<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { cn } from '@/lib/utils'
</script>
```

### Dark mode toggle
- Añadir clase `dark` en `<html>` via JS
- Usar `@custom-variant dark (&:is(.dark *))` en CSS
- Variables `@theme` se adaptan automáticamente

### Referencias
- **Skill:** `~/.copilot/skills/garageos-frontend/SKILL.md`
- **Copilot instructions:** `.github/copilot-instructions.md`
- **shadcn-vue docs:** https://www.shadcn-vue.com/
- **Tailwind 4 docs:** https://tailwindcss.com/docs

---

## Riesgos y mitigaciones (histórico)

| Riesgo | Mitigación | Estado |
|---|---|---|
| Clases TW3 no compatibles con TW4 | Buscar con grep clases deprecadas | ✅ Resuelto |
| Dark mode diferente en TW4 | Usar `@custom-variant dark` | ✅ Resuelto |
| shadcn-vue incompatibilidad con Inertia | Probar Dialog dentro de Inertia page | ✅ Resuelto |
| Estilos custom se rompen | Migrar CSS custom a `@theme` variables | ✅ Resuelto |
| Radix vs Headless UI conflictos | Migrar gradualmente, mantener ambos temporalmente | ✅ Resuelto |

---

## Notas técnicas

- shadcn-vue usa **Radix Vue** (reka-ui) para primitivas accesibles
- Componentes son **copy/paste** en `Components/ui/` — no dependencia runtime
- `class-variance-authority` (CVA) gestiona variantes de componentes
- `cn()` mergea clases Tailwind sin conflictos
- `lucide-vue-next` reemplaza `@heroicons/vue` como iconos
- `vue-sonner` se usa para toast notifications (más ligero que shadcn Toast)
- `recharts` se mantiene para charts complejos
- `@tanstack/vue-table` para tablas avanzadas con server-side
- `vee-validate` + `zod` para validación de formularios
