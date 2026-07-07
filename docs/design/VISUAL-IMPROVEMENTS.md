# Plan de Mejora Visual — GarageOS

## 🎨 Paleta de Colores

### Colores principales
- **Primary**: `oklch(0.55 0.22 264)` — Azul GarageOS (confianza, tecnología)
- **Primary Foreground**: `oklch(0.98 0 0)` — Blanco sobre primary
- **Secondary**: `oklch(0.96 0.02 264)` — Gris azulado claro
- **Accent**: `oklch(0.65 0.20 145)` — Verde esmeralda (éxito, acción)
- **Destructive**: `oklch(0.55 0.25 25)` — Rojo coral (alertas)

### Colores semánticos
- **Background**: `oklch(0.99 0 0)` — Casi blanco
- **Foreground**: `oklch(0.20 0.01 264)` — Casi negro
- **Muted**: `oklch(0.96 0.01 264)` — Gris muy claro
- **Muted Foreground**: `oklch(0.50 0.02 264)` — Gris medio
- **Border**: `oklch(0.90 0.01 264)` — Gris claro
- **Card**: `oklch(1 0 0)` — Blanco puro
- **Card Foreground**: `oklch(0.20 0.01 264)` — Casi negro

### Dark mode
- **Background**: `oklch(0.12 0.01 264)` — Negro azulado
- **Card**: `oklch(0.16 0.01 264)` — Gris muy oscuro
- **Foreground**: `oklch(0.95 0.01 264)` — Casi blanco
- **Border**: `oklch(0.25 0.01 264)` — Gris oscuro

---

## 🧩 Mejoras por Componente

### 1. Sidebar (AppSidebar.vue)
**Actual**: Plano, sin profundidad
**Mejora**:
- Gradiente sutil en el header (logo)
- Hover con transición suave + indicador lateral
- Iconos con micro-animación al hover (scale 1.1)
- Avatar del usuario con badge de estado online (verde)
- Separadores con mejor contraste

### 2. Header (AppSidebarLayout)
**Actual**: Bordes básicos
**Mejora**:
- Glassmorphism: `backdrop-blur-xl` con `bg-card/80`
- Breadcrumb con iconos entre items
- Botón de toggle con animación de rotación
- Indicador de página activa más prominente

### 3. Cards (Welcome, Dashboard)
**Actual**: Bordes simples
**Mejora**:
- Shadow suave: `shadow-sm hover:shadow-md transition-shadow`
- Hover: `hover:-translate-y-1` + `hover:border-primary/50`
- Iconos en contenedores con gradiente y blur
- Tipografía jerárquica más clara

### 4. Botones
**Actual**: Planos
**Mejora**:
- Primary con gradiente: `bg-gradient-to-r from-primary to-primary/80`
- Hover: `hover:scale-105 active:scale-95`
- Shadow en hover: `hover:shadow-lg hover:shadow-primary/25`
- Transición: `transition-all duration-200`

### 5. Welcome Page
**Actual**: Grid básico
**Mejora**:
- Hero con fondo animado (gradient mesh)
- Badge "Nuevo" con animación pulse
- Features cards con iconos en gradient containers
- CTA con efecto glow

---

## ✨ Microinteracciones

| Elemento | Animación | Duración |
|----------|-----------|----------|
| Botón hover | `scale-105` + shadow | 200ms |
| Card hover | `translate-y-[-4px]` + shadow | 300ms |
| Sidebar link | `translate-x-1` + bg | 200ms |
| Page transition | `fade-in` + `slide-up` | 400ms |
| Toast | `slide-in-from-top` | 300ms |
| Modal | `scale-95 → 100` + fade | 200ms |

---

## 📐 Espaciado y Tipografía

### Espaciado
- **Section padding**: `p-6 md:p-8 lg:p-10`
- **Card padding**: `p-5 md:p-6`
- **Stack gap**: `gap-4` (sm), `gap-6` (md), `gap-8` (lg)
- **Container max**: `max-w-7xl mx-auto`

### Tipografía
- **Headings**: `font-bold tracking-tight`
- **Body**: `text-sm md:text-base`
- **Muted**: `text-muted-foreground text-sm`
- **Gradient text**: `bg-clip-text text-transparent bg-gradient-to-r`

---

## 🎯 Implementación por Fases

### Fase 1: Tokens y base (✅ 1h)
- [x] Actualizar `resources/css/app.css` con nueva paleta
- [x] Configurar variables CSS en `:root` y `.dark`
- [x] Añadir animaciones personalizadas en `tailwind.config.js`

### Fase 2: Componentes shadcn (✅ 2h)
- [x] Personalizar `Button` con gradientes
- [x] Mejorar `Card` con hover effects
- [x] Añadir variantes de `Badge` (success, warning, info)

### Fase 3: Layout y navegación (✅ 2h)
- [x] Rediseñar `AppSidebar` con glassmorphism
- [x] Mejorar header con backdrop-blur
- [x] Animaciones en links de navegación

### Fase 4: Páginas principales (✅ 3h)
- [x] Welcome: hero animado, features cards
- [ ] Dashboard: stats cards con sparklines
- [ ] Vehicles: cards de vehículo mejoradas
- [ ] Forms: inputs con focus rings animados

### Fase 5: Detalles finales (✅ 1h)
- [x] Loading states con skeleton
- [x] Empty states ilustrados (EmptyState.vue creado)
- [x] Toasts personalizados (vue-sonner ya integrado)
- [x] Dark mode polish (variables actualizadas)

---

## 📦 Componentes shadcn-vue a instalar

```bash
npx shadcn-vue@latest add skeleton
npx shadcn-vue@latest add alert
npx shadcn-vue@latest add progress
npx shadcn-vue@latest add avatar
npx shadcn-vue@latest add switch
npx shadcn-vue@latest add tabs
```

---

## 🎬 Ejemplos de uso

### Card mejorado
```vue
<Card class="group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 border-border/50">
  <CardHeader>
    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center">
      <Icon class="h-5 w-5 text-primary-foreground" />
    </div>
    <CardTitle>Título</CardTitle>
  </CardHeader>
  <CardContent>
    <p class="text-muted-foreground text-sm">Descripción</p>
  </CardContent>
</Card>
```

### Botón con gradiente
```vue
<Button class="bg-gradient-to-r from-primary to-primary/80 hover:shadow-lg hover:shadow-primary/25 transition-all">
  Acción
</Button>
```

### Sidebar link con animación
```vue
<Link class="group flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-muted hover:translate-x-1 transition-all">
  <Icon class="h-4 w-4 group-hover:scale-110 transition-transform" />
  <span>Item</span>
</Link>
```

---

## ✅ Verificación

- [ ] Build sin errores (`npm run build`)
- [ ] Responsive en móvil/tablet/desktop
- [ ] Dark mode funcional y consistente
- [ ] Animaciones suaves (60fps)
- [ ] Accesibilidad: focus rings, aria-labels
- [ ] Contraste WCAG AA

## Visual Design System

### Color Palette

#### 1. The Neutrals (The Foundation: Asphalt, McLaren Silver, and Carbon Fiber)

These colors make up 60-70% of the web and provide the visual support, backgrounds, and typography that allow the historical colors to stand out without overwhelming the user.

- **Light / Clean Background:** `#F8FAFC` (Slate 50) — For the general web background.
- **McLaren Silver (Intermediate Gray):** `#CBD5E1` (Slate 300) and `#64748B` (Slate 500) — Ideal for card borders, separators, secondary text, or icons that simulate metallic finishes.
- **Carbon Fiber / Asphalt (Darks):** `#0F172A` (Slate 900) and `#020617` (Slate 950) — For primary typography, footers, or if you decide to make a dark mode interface.

#### 2. The Identity Colors (The Essence: Nürburgring and McLaren)

These colors provide personality, elegance, and dynamism. They are used in structural elements such as navigation bars, headers, titles, or highlighted sections.

- **Original Nürburgring Green:** `#0A3A2F` (The deep, imposing green).

- *Light Variant (Hover/Highlights):* `#14532D` — A slightly more open green for visual effects or success tags.
- *Ultra-Dark Variant:* `#041F1A` — Perfect for premium menu backgrounds.
- **McLaren Heritage Orange:** `#FF8000` (Papaya Orange). Adds the iconic color from the origins of Bruce McLaren. It perfectly breaks with the green and serves for intermediate design elements, active menus, or performance subcategories.

#### 3. The Action and Interaction Colors (The Impact: Ferrari and Success/Warning)

High-contrast colors reserved strictly for user interaction (buttons, calls to action, prices, or system states).

- **Ferrari Red (Primary CTA):** `#CC0000` (Rosso Corsa).

- *Hover Variant (Button Pressed):* `#990000` — A darker version when the user hovers over the button.
- **Alpine Blue (Information / Links):** `#0053A0` — Adds the classic French competition blue. It's perfect for secondary links, clickable text, or elements that need to be seen but don't compete with Ferrari Red.

#### Summary of the Extended Palette (Component Structure)

To see the coherence of the complete visual ecosystem, here's how the components interact on the screen:

```
┌────────────────────────────────────────────────────────┐
│  [Navbar] Background: Nürburgring Green (#0A3A2F)            │ -> Text: Silver (#CBD5E1)
├────────────────────────────────────────────────────────┤
│                                                        │
│  [Hero Section] Background: Carbon (#0F172A)               │
│  Title: "Pure Performance" (White)                  │
│  Primary Button: FERRARI RED (#CC0000)               │ -> Hover: (#990000)
│                                                        │
├────────────────────────────────────────────────────────┤
│  [Card Section] Light Background (#F8FAFC)              │
│  ┌───────────────────────┐   ┌───────────────────────┐ │
│  │ Car A Card        │   │ Car B Card       │ │
│  │ Border: Silver (#CBD5E1) │   │ Border: Silver (#CBD5E1) │ │
│  │ Tag: New (Papaya)    │   │ Tag: Reserved (Blue) │ │ -> Orange (#FF8000) / Blue (#0053A0)
│  └───────────────────────┘   └───────────────────────┘ │
└────────────────────────────────────────────────────────┘
```

#### Advantage of this Structure
By expanding it in this way, the web doesn't look overloaded with "vibrant colors". The **McLaren Silver** and **Asphalt Grays** act as peacekeepers, making the **Nürburgring Green** feel elegant, the **Papaya Orange** modern, and the **Ferrari Red** guide the user's eye exactly where you want them to click.
