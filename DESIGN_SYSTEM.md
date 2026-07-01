# GarageOS Design System

## Brand
GarageOS es una plataforma SaaS para gestión de vehículos, talleres y mantenimientos. Audiencia: propietarios de coches, talleristas, importadores de vehículos alemanes.

## Stack Técnico
- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: Vue 3.5, Inertia.js 2, TypeScript
- **Estilos**: Tailwind CSS 4
- **Componentes**: shadcn-vue
- **Iconos**: lucide-vue-next

## Paleta de Colores (Motor Heritage)

### Neutrals (Foundation - 60-70% del diseño)
- Background: `#F8FAFC` (Slate 50)
- Foreground: `#0F172A` (Slate 900)
- Card: `oklch(1 0 0)` (Blanco)
- Border: `#CBD5E1` (Slate 300 - McLaren Silver)
- Muted: `#CBD5E1`
- Muted Foreground: `#64748B` (Slate 500)

### Identity Colors (Essence)
- Primary: `#0A3A2F` (Nürburgring Green)
  - Hover: `#14532D`
  - Ultra-dark: `#041F1A`
- Secondary: `#FF8000` (McLaren Papaya Orange)
  - Para badges, tags, estados activos

### Action Colors (Impact)
- Destructive: `#CC0000` (Ferrari Rosso Corsa)
  - Hover: `#990000`
- Accent: `#0053A0` (Alpine Blue - información, links)

## Tipografía
- Font family: Figtree, Inter, system-ui, sans-serif
- Headings: `font-bold tracking-tight`
- Body: `text-sm md:text-base`
- Labels: `text-sm font-medium`

## Componentes Clave

### Botones
- **Primary**: Gradiente `from-primary to-primary/80`, hover `scale-105`, shadow `shadow-primary/25`
- **Secondary**: Border `border-border`, hover `hover:bg-accent`
- **Destructive**: Gradiente `from-destructive to-destructive/90`

### Cards
- Base: `rounded-xl border border-border/50 bg-card`
- Hover: `hover:shadow-lg hover:shadow-primary/5 hover:-translate-y-1 transition-all duration-300`
- Header con icono en gradiente

### Inputs
- Base: `rounded-lg border border-input bg-background px-3 py-2`
- Focus: `focus:ring-2 focus:ring-ring focus:ring-offset-2`

### Iconos
- Contenedores: `h-10 w-10 rounded-lg bg-gradient-to-br from-primary/10 to-accent/10`
- Text: `h-4 w-4 text-primary`

## Layout Patterns

### Container
- `mx-auto w-full max-w-7xl px-6 py-6`

### Sidebar
- Ancho: `w-64` (expandido), `w-16` (colapsado)
- Glassmorphism: `backdrop-blur-xl`
- Links con hover: `hover:translate-x-1 hover:bg-muted`

### Header
- Sticky: `sticky top-0 z-30`
- Glassmorphism: `bg-card/90 backdrop-blur-xl`
- Altura: `h-16`

## Dark Mode
- Background: `#0F172A` (Slate 900)
- Card: `#1E293B` (Slate 800)
- Foreground: `oklch(0.95 0.01 264)`
- Border: `#334155`

## Animaciones
- Botón hover: `scale-105` (200ms)
- Card hover: `translate-y-[-4px]` (300ms)
- Sidebar link: `translate-x-1` (200ms)
- Page transition: `fade-in` (400ms)

## Espaciado
- Section: `p-6 md:p-8 lg:p-10`
- Card: `p-5 md:p-6`
- Stack: `gap-4` (sm), `gap-6` (md), `gap-8` (lg)

## Patrones de Negocio
- Vehículos: cards con matrícula, marca, modelo, km
- Talleres: listado con badges de estado
- Importación: workflow con pasos visuales
- Marketplace: grid de productos con imágenes

## Anti-Patterns
- NO usar `text-gray-*`, usar colores semánticos
- NO valores hardcoded, usar variables CSS
- NO inline styles, usar clases Tailwind
- NO componentes sin estados vacíos (EmptyState)

## Design References
- Vercel: sistema de componentes, paleta neutra
- Stripe: animaciones sutiles, glassmorphism
- Linear: dark mode, microinteracciones
