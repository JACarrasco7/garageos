# GarageOS — Evolución Visual · "Heritage Motorspace UI"

> Plan de rediseño visual para GarageOS
> Stack: Laravel 13 + Inertia 2 + Vue 3 + shadcn-vue + Tailwind 4
> Fecha: 2026-07-08
> Estado: **Planificación — pendiente aprobación**

---

## 1 · Resumen ejecutivo

**Objetivo.** Evolucionar el lenguaje visual de GarageOS desde el "Glassmorphism genérico" actual hacia una identidad **única y memorable**, llamada internamente **"Heritage Motorspace UI"**, que mezcle tres estéticas de forma cohesiva:

| Estética          | Aporte                                                                | Referentes mentales                              |
| ----------------- | --------------------------------------------------------------------- | ------------------------------------------------ |
| **Glassmorphism** | Profundidad, capas, jerarquía visual                                  | Sidebar flotante, KPIs translúcidos              |
| **Liquid Glass**  | Refracción sutil, highlight de borde "mojado", micro‑realismo táctil  | Apple iOS 26 Liquid Glass (sensación, no copia) |
| **Space / Clean** | Aire, silencio, tipografía precisa, ritmo de 4/8 px, paleta restringida | Linear, Vercel, dashboards SpaceX, Notion AI     |

**Tono emocional.** GarageOS debe sentirse como entrar al **paddock de Nürburgring a las 6 de la mañana**: ordenado, profesional, con historia. Nada chillón, nada decorativo "porque sí". Cada píxel justifica su existencia.

**Paleta.** Paleta fundamentada en **colores nacionales de competición FIA** (los que la gente del motor reconoce instintivamente) más un eje neutro **carbon/asfalto**. No es una paleta "bonita": es una paleta con **memoria**.

---

## 2 · Auditoría del estado actual

> Lo que **ya tenemos bien** (no romper) y lo que **falta o está disperso**.

### ✅ Lo que ya está y se mantiene

- **`resources/css/app.css`** ya define tokens semánticos OKLCH/HEX correctos (`--primary`, `--accent`, `--destructive`, `--secondary`, `--background`, `--foreground`).
- Utilidades `.glass`, `.glass-strong`, `.glass-panel`, `.glass-card`, `.glass-item`, `.glass-liquid`, `.glass-button`, `.glass-kpi` **funcionales**.
- `AppSidebarLayout.vue` ya usa el patrón de sidebar flotante con `backdrop-blur-2xl`, `rounded-2xl`, `border-white/15`.
- Componentes shadcn-vue instalados y migrados (sidebar, card, dialog, dropdown, table, form, sheet, tabs, sonner, etc.).
- Dark mode soportado con swap correcto de tokens.
- Animaciones definidas: `gradientShift`, `shimmer`, `float`, `shine`, `pulse`, `slideUp`, `fadeIn`, `scaleIn`.

### ⚠️ Lo que falta o está mal

1. **Paleta incompleta.** Solo hay 4 colores identitarios (verde, papaya, rojo, plata). Faltan Bleu de France, los grises de carrocería, el amarillo belga, el rojo japonés, y un sistema de **estado semántico** (success / warning / info) coherente con la herencia motorsport.
2. **Liquid Glass solo en nombre.** Las clases `.glass-liquid` son "glassmorphism con blur más alto". No hay **refracción SVG**, **specular highlight de borde**, ni **sensación de cristal mojado**.
3. **Space UI ausente.** No hay reglas de espaciado (escala 4/8), ni jerarquía tipográfica, ni reglas de "máximo 3 niveles de profundidad".
4. **CSS utilities infladas.** `@layer utilities` tiene ~60 clases mezcladas (glass + animación + hover + background). Difícil de mantener y aplicar con criterio.
5. **Sin sistema de tokens de spacing/motion/shadow.** Solo `--radius` está tokenizado. Sombras, espaciados y duraciones son valores sueltos.
6. **Gradientes `oklch` con hue random.** Los gradientes mesh usan `oklch(0.55 0.22 264)` (azul aleatorio) que no casa con la identidad motorspace.
7. **Sidebar con `bg-card/50 border-white/15` hardcoded.** No respeta dark mode, no usa tokens.
8. **Falta definir el "espacio negativo"** — la sensación de aire que define Space UI. Hoy muchos componentes se ven apiñados.

---

## 3 · Sistema de color · "Motorsport Heritage Palette"

### 3.1 Filosofía

Tres ejes cromáticos:

1. **Identidad** (Nürburgring Green + Papaya Orange) → ya definidos, mantener.
2. **Herencia** (colores nacionales FIA) → ampliar el catálogo, usarlos como **acentos contextuales** (badges de estado, charts, iconografía deportiva).
3. **Asfalto** (neutros carbon/slate) → base estructural.

### 3.2 Tokens semánticos nuevos (extender `app.css`)

```css
:root {
  /* === Eje Identidad (mantener) === */
  --primary:           #0A3A2F; /* Nürburgring Green · heritage */
  --primary-foreground:#F8FAFC;
  --accent:            #FF8000; /* McLaren Papaya Spark */
  --accent-foreground: #FFFFFF;
  --destructive:       #CC0000; /* Rosso Corsa */

  /* === Eje Herencia FIA (nuevo) === */
  --heritage-blue:     #318CE7; /* Bleu de France · Alpine F1 */
  --heritage-silver:   #B8B9BC; /* German Silver · Mercedes */
  --heritage-yellow:   #FAE042; /* Belgium Yellow · Spa */
  --heritage-red:      #DC1F26; /* Japan Hi‑Vis · Rising Sun */
  --heritage-navy:     #002654; /* Le Mans Blue · Ford GT40 */
  --heritage-bronze:   #B08D57; /* Vintage Bronze · dashboard patina */

  /* === Estados semánticos (nuevo) === */
  --success:           #16A34A; /* semáforo verde · ITV OK */
  --warning:           #F59E0B; /* ámbar · próximo service */
  --info:              #318CE7; /* azul · info general */
  --danger:            #CC0000; /* rojo · error crítico */

  /* === Eje Asfalto / neutros (mantener y refinar) === */
  --background:        #F8FAFC; /* Slate 50 */
  --foreground:        #0F172A; /* Carbon black */
  --muted:             #F1F5F9; /* Slate 100 */
  --muted-foreground:  #64748B; /* Slate 500 */
  --border:            #E2E8F0; /* Slate 200 · más suave que #CBD5E1 */
  --ring:              #0A3A2F;

  /* === Spacing tokens (nuevo) === */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 24px;
  --space-6: 32px;
  --space-8: 48px;
  --space-10:64px;

  /* === Radius tokens (nuevo, complementar --radius) === */
  --radius-xs: 6px;
  --radius-sm: 10px;
  --radius-md: 14px;
  --radius-lg: 20px;
  --radius-xl: 28px;
  --radius-2xl: 36px;

  /* === Motion tokens (nuevo) === */
  --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
  --ease-in-out-quart: cubic-bezier(0.76, 0, 0.24, 1);
  --duration-fast: 150ms;
  --duration-normal: 280ms;
  --duration-slow: 480ms;
}

.dark {
  --background:        #020617;
  --foreground:        #F8FAFC;
  --muted:             #0F172A;
  --muted-foreground:  #94A3B8;
  --border:            #1E293B;
  --heritage-bronze:   #C9A876; /* un punto más cálido para fondo oscuro */
}
```

### 3.3 Uso previsto por contexto

| Contexto                         | Color               | Justificación                              |
| -------------------------------- | ------------------- | ------------------------------------------ |
| Botón primario / CTA / sidebar   | `--primary`         | Nürburgring Green = identidad GarageOS     |
| Acción positiva / "Save" / "OK"  | `--accent` (Papaya) | El naranja papaya es energía, **acción**   |
| Error / eliminar                | `--destructive`     | Rosso Corsa = atención máxima              |
| Estado "ITV pasada" / "Activo"   | `--success` (#16A34A) | Semáforo verde, lectura inmediata        |
| Estado "Próximo service"         | `--warning` (#F59E0B) | Ámbar, no rojo (no es error)            |
| Info / ayuda / "Cómo funciona"   | `--info` (Bleu de France) | Azul Francia = información              |
| Badge "Premium" / "Pro"          | `--heritage-bronze` | Toque vintage, premium                     |
| Badges de marca / charts varios  | paleta `heritage-*` | Contexto motorsport (origen país)          |

---

## 4 · Tipografía · "Space Mono / Inter / Geist"

### 4.1 Stack propuesto

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

@theme {
  --font-sans: 'Inter', ui-sans, system-ui, sans-serif;
  --font-mono: 'JetBrains Mono', ui-monospace, monospace;
  --font-display: 'Inter', ui-sans, system-ui, sans-serif; /* weight 700/800 */
}
```

### 4.2 Escala tipográfica (Space / Clean)

| Token            | Familia | Weight | Tamaño | Line‑height | Tracking | Uso                         |
| ---------------- | ------- | ------ | ------ | ----------- | -------- | --------------------------- |
| `text-display`   | Inter   | 800    | 36px   | 1.1         | -0.03em  | Hero Dashboard              |
| `text-h1`        | Inter   | 700    | 28px   | 1.2         | -0.02em  | Título página               |
| `text-h2`        | Inter   | 700    | 22px   | 1.25        | -0.015em | Sección                     |
| `text-h3`        | Inter   | 600    | 17px   | 1.3         | -0.01em  | Subsección / card title     |
| `text-body`      | Inter   | 400    | 14px   | 1.55        | normal   | Texto general               |
| `text-small`     | Inter   | 500    | 12px   | 1.4         | normal   | Labels, ayuda               |
| `text-micro`     | Inter   | 600    | 10px   | 1.2         | 0.08em   | Caps, eyebrows (UPPERCASE)  |
| `text-mono-data` | JetBrains Mono | 500 | 13px | 1.4     | -0.01em  | VIN, KM, matrícula, precio  |

### 4.3 Reglas de uso

- **Eyebrows** (etiqueta sobre título) en `text-micro` uppercase con `text-muted-foreground`.
- **Datos numéricos** (km, VIN, precio, fecha) **siempre** en `font-mono` → legibilidad técnica, sensación "dashboard de carrera".
- **Títulos en negativo** (color blanco) → usar weight 700, nunca weight 800 sobre fondos oscuros (vibra mal con Liquid Glass).
- **Letter‑spacing negativo** en h1/h2 → sensación más "premium", menos "marketing".

---

## 5 · Sistema de espaciado · "Escala 4 px"

Space UI se sostiene sobre una **escala de espaciado coherente**. Prohibido usar valores arbitrarios (`p-[13px]`). Solo múltiplos de 4.

```
4 · 8 · 12 · 16 · 24 · 32 · 48 · 64 · 96
```

Esto se aplica a:
- `padding` / `margin` interno de cards
- `gap` entre elementos de grid/flex
- `space-y-*` en stacks verticales
- Distancia entre título y contenido

**Regla de oro del "aire":** una card de Dashboard debe tener **mínimo 24 px** de padding interno (`p-6`). Una página, mínimo `p-8`. Esto es lo que da el aspecto "Clean UI".

---

## 6 · Profundidad y sombras · "Tres niveles"

Space UI define **3 niveles de profundidad máximo** sobre la superficie base. Más de 3 = aspecto barroco.

| Nivel | Uso                              | Sombra                                                  | Borde              |
| ----- | -------------------------------- | ------------------------------------------------------- | ------------------ |
| **0** | Fondo base de página             | `none`                                                  | `none`             |
| **1** | Card / panel flotante            | `shadow-float-light` (definida)                          | `border-white/10` light, `border-white/[0.06]` dark |
| **2** | Modal / sheet / dropdown / popover | `shadow-float-light-lg`                                | `border-white/15`  |
| **3** | Hero / KPI destacado (raro)      | glow coloreado puntual `shadow-glow-primary`            | `border-primary/30` |

**Refinar sombras existentes** (`shadow-float-light`, `shadow-float-dark`): mantener pero añadir **inset highlight de 1 px blanco en el top edge** → simula el reflejo de cristal que define Liquid Glass.

```css
.shadow-crystal {
  box-shadow:
    inset 0 1px 0 0 rgba(255, 255, 255, 0.4),  /* top highlight */
    inset 0 -1px 0 0 rgba(0, 0, 0, 0.05),       /* bottom edge */
    0 4px 12px rgba(0, 0, 0, 0.06),
    0 12px 32px rgba(0, 0, 0, 0.10);
}
.dark .shadow-crystal {
  box-shadow:
    inset 0 1px 0 0 rgba(255, 255, 255, 0.08),
    inset 0 -1px 0 0 rgba(0, 0, 0, 0.3),
    0 4px 12px rgba(0, 0, 0, 0.4),
    0 12px 32px rgba(0, 0, 0, 0.5);
}
```

---

## 7 · Liquid Glass · Implementación real

Glassmorphism "clásico" = blur + transparencia. **Liquid Glass** añade **refracción**, **specular highlight** y **sensación de volumen**.

### 7.1 Refracción con SVG `feDisplacementMap`

Añadir a `app.css` un filtro SVG reutilizable. Aplicarlo solo a **elementos nivel 2** (modales, sheets, popovers) por coste de render.

```html
<!-- en resources/views/app.blade.php o layout principal -->
<svg style="position:absolute;width:0;height:0" aria-hidden="true">
  <defs>
    <filter id="liquid-glass">
      <feTurbulence type="fractalNoise" baseFrequency="0.012 0.018" numOctaves="2" />
      <feDisplacementMap in="SourceGraphic" scale="6" />
      <feGaussianBlur stdDeviation="0.5" />
    </filter>
  </defs>
</svg>
```

```css
.liquid-glass {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(28px) saturate(140%);
  -webkit-backdrop-filter: blur(28px) saturate(140%);
  border: 1px solid rgba(255, 255, 255, 0.18);
  filter: url(#liquid-glass);
  box-shadow:
    inset 0 1px 0 0 rgba(255, 255, 255, 0.5),
    0 8px 32px rgba(0, 0, 0, 0.2);
  border-radius: 20px;
}
```

**Fallback accesible** (si el navegador no soporta filter SVG, sigue viéndose bien):
```css
@supports not (filter: url(#liquid-glass)) {
  .liquid-glass { filter: none; backdrop-filter: blur(28px); }
}
```

### 7.2 `prefers-reduced-transparency` y `prefers-reduced-motion`

Liquid Glass + animación puede ser pesado y molesto. Respetar siempre:

```css
@media (prefers-reduced-transparency: reduce) {
  .glass-liquid, .liquid-glass, .glass-panel {
    background: var(--card) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
  }
}
@media (prefers-reduced-motion: reduce) {
  .glass-button, .card-hover, .card-hover-glow { transition: none !important; }
  .badge-pulse, .badge-shine { animation: none !important; }
}
```

### 7.3 Directrices de cuándo usar cada nivel

| Elemento UI                | Clase             | Por qué                                |
| -------------------------- | ----------------- | -------------------------------------- |
| Sidebar flotante           | `.glass-panel`    | Soporta muchos items, no necesita refracción |
| Card de vehículo/KPI       | `.glass-card`     | Nivel 1, sin refracción                |
| Modal / Dialog / Sheet     | `.liquid-glass`   | Elemento **flotante** = refracción     |
| Dropdown / Popover         | `.liquid-glass-sm`| Versión pequeña del filtro             |
| Botón primario             | `.glass-button`   | Nivel 2 funcional, blur + hover        |
| Tablas / listas            | `.glass-item`     | Mínimo ruido visual, máxima legibilidad |
| Toasts (Sonner)            | native (ya glass) | No tocar                               |

---

## 8 · Backgrounds · "Motorspace Gradient Mesh"

El fondo base de GarageOS debe sugerir **espacio / pista / profundidad** sin distraer. La idea es un **gradient mesh sutil** en colores de la paleta heritage + asfalto, animado MUY lentamente (15‑20 s loop).

### 8.1 Definición de capas

```
Capa 0 · Fondo base sólido      → var(--background)
Capa 1 · Gradient mesh radial    → oklch(Nürburgring Green 5%) + oklch(Papaya 3%) + oklch(Asfalto 4%)
Capa 2 · Noise grain SVG         → overlay de ruido 2% opacidad (efecto "papel de gráfica")
Capa 3 · Grid sutil opcional     → líneas cada 64px, 1px, 3% opacidad (efecto "blueprint")
```

### 8.2 Implementación en `app.css`

```css
.bg-motorspace {
  background:
    radial-gradient(at 15% 15%, color-mix(in oklch, var(--primary) 6%, transparent) 0px, transparent 45%),
    radial-gradient(at 85% 85%, color-mix(in oklch, var(--accent) 4%, transparent) 0px, transparent 45%),
    radial-gradient(at 50% 50%, color-mix(in oklch, var(--heritage-blue) 3%, transparent) 0px, transparent 60%),
    var(--background);
  background-attachment: fixed;
}

.bg-motorspace::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.4'/%3E%3C/svg%3E");
  opacity: 0.025;
  pointer-events: none;
  z-index: 0;
}
```

Aplicar `bg-motorspace` en `<body>` (vía `@layer base` en `app.css`) o en el contenedor raíz de cada layout.

### 8.3 Variantes

- `bg-motorspace-hero` → más saturado, para landing.
- `bg-motorspace-calm` → solo radial del primary, para pantallas de error / empty states.

---

## 9 · Tipografía de componentes · reglas concretas

### 9.1 Botones

- **Primario**: `bg-primary text-primary-foreground` (Nürburgring Green sólido). Sin glass. Hover → ligero brighten + `shadow-glow-primary`.
- **Secundario**: `bg-glass-button` (translúcido). Ideal para acciones menos importantes.
- **Destructive**: `bg-destructive text-white`. Siempre sólido, sin glass.
- **Ghost**: `hover:bg-muted`. Sin borde, sin glass.
- **CTA destacado** (e.g. "Suscríbete a Pro"): `bg-accent text-accent-foreground` con glow Papaya.

### 9.2 Cards

- Padding interno: `p-6` (24 px) por defecto, `p-5` (20 px) en listas densas.
- Header separado del body con `space-y-2` (8 px), nunca con divider salvo casos especiales.
- `rounded-lg` (20 px) por defecto, `rounded-xl` (28 px) para hero cards.
- Hover en cards interactivas: lift de -2 px + `shadow-crystal` ligeramente intensificado.

### 9.3 Badges

- Usar **solo los tokens semánticos** (`bg-success/10 text-success`, `bg-warning/10 text-warning`, etc.).
- Para badges "premium" / "Pro": `bg-heritage-bronze/15 text-heritage-bronze`.
- Tamaño: `h-5 px-2 text-micro` (10 px tracking 0.08em) → sensación "F1 timing tower".

### 9.4 Iconos

- `lucide-vue-next` ya cumple: stroke 2, tamaño 16/20/24.
- **Nueva regla**: para iconos en KPI/Stats, envolver en `glass-icon` (cuadrado 40×40 con `bg-primary/10` + `rounded-lg`) → "icon chip".

### 9.5 Tablas (Vehicle Index, Marketplace, etc.)

- Header: `text-micro uppercase text-muted-foreground font-semibold`.
- Filas: `h-14` mínimo, hover `bg-muted/30` con transición 200 ms.
- Cero bordes verticales. Solo `border-b border-border/40` en filas.
- Datos numéricos en `font-mono`.

---

## 10 · Microinteracciones · "Sentido del movimiento"

Space UI exige que **cada acción tenga respuesta visual**. Pero respuestas **breves y elegantes**, no rebotes exagerados.

| Acción                   | Duración | Easing              | Transformación                                |
| ------------------------ | -------- | ------------------- | --------------------------------------------- |
| Hover en card            | 280 ms   | `--ease-out-expo`   | `translate-y(-2px)` + shadow                  |
| Click en botón           | 100 ms   | `ease-out`          | `scale(0.97)`                                 |
| Apertura modal/sheet     | 320 ms   | `--ease-out-expo`   | `opacity 0→1` + `scale(0.96→1)` + `translate-y(8px→0)` |
| Toast entrada            | 260 ms   | `--ease-out-expo`   | `translate-x(100%) → 0` + opacity             |
| Page transition (futuro) | 240 ms   | `--ease-in-out-quart` | fade + 4 px translate-y                     |

**Prohibido**: bounce exagerado, giro 360° al click, parallax pesado, animaciones > 600 ms en interacciones básicas.

---

## 11 · Iconografía de marca · propuesta

Aprovechar la herencia motorspace para añadir **iconografía funcional nueva** que refuerce identidad:

- **Logo GarageOS** → evolucionar del wordmark actual a un emblema minimalista. Propuesta: tipografía Inter Black 800 + un glifo "G" con un biselado tipo tacómetro (sugerir velocidad sin ser literal).
- **Empty states** → ilustración SVG simple (línea 1.5 px) en lugar de emoji o fotos. Mantener el estilo "blueprint" (líneas blancas sobre fondo dark, primary en detalle).

---

## 12 · Estructura de carpetas `docs/design/`

Para que el sistema sea mantenible, crear:

```
docs/design/
├── garageos-visual-evolution.md    ← este documento
├── tokens.md                       ← tabla maestra de tokens (color/spacing/type/motion)
├── components/
│   ├── card.md                     ← anatomía de una card
│   ├── button.md                   ← variantes y reglas
│   ├── dialog.md                   ← Liquid Glass real
│   ├── badge.md
│   ├── sidebar.md
│   └── table.md
├── patterns/
│   ├── glass-mesh.md               ← cómo combinar glass + mesh
│   ├── spacing-rhythm.md           ← escala 4px + ejemplos
│   ├── motion.md                   ← tabla de duraciones/easings
│   └── dark-mode.md                ← reglas de inversión
└── inspiration/
    ├── references.md               ← Dribbble/Sitios que inspiran
    └── forbiddens.md               ← "nunca hacer X" (neumorfismo, glow rosa, etc.)
```

---

## 13 · Plan de implementación por fases

> **No se ejecuta `npm run build` automáticamente.** El usuario lo lanza cuando quiera.

### Fase 0 · Aprobación & exploración visual (1 sesión)
- [ ] Revisar este documento.
- [ ] Generar 1 mock en OpenDesigner de "Dashboard v2" para validar dirección.
- [ ] Confirmar paleta (¿añadimos heritage colors o nos quedamos en green+papaya+rojo+silver?).
- [ ] Confirmar tipografía (¿Inter+JetBrains o solo Inter?).

### Fase 1 · Token foundation · `app.css` ✅ COMPLETADA (2026-07-08)
- [x] Añadir tokens nuevos en `:root` y `.dark`: heritage-*, success/warning/info, spacing, motion, radius.
- [x] Definir `.bg-motorspace`, `.bg-motorspace-hero`, `.bg-motorspace-calm`.
- [x] Añadir `.shadow-crystal`, `.shadow-crystal-lg`, `.depth-1/2/3`.
- [x] Añadir SVG filter `liquid-glass` en `resources/views/app.blade.php`.
- [x] Añadir `.liquid-glass` y `.liquid-glass-sm` con refracción SVG real.
- [x] Añadir `@media` prefers-reduced-transparency / reduced-motion.
- [x] Tipografía Inter + JetBrains Mono vía Google Fonts (con fallback Figtree eliminado).
- [x] Utilidades semánticas (`text-success`, `bg-warning/10`, etc.) y heritage (`text-heritage-bronze`, `border-heritage-blue`, etc.).
- [x] Body ahora aplica `bg-motorspace` por defecto.
- [x] **No romper** las clases `.glass-*` existentes → coexisten, las nuevas son complementarias.

### Fase 2 · Componentes shadcn-vue custom (1 día)
- [ ] Crear wrapper `<NCard variant="default|hero|minimal">` en `Components/garageos/` → encapsula el uso correcto de tokens glass + spacing + radius.
- [ ] Crear `<NButton variant="primary|secondary|ghost|destructive|cta">` con tokens correctos.
- [ ] Crear `<NBadge variant="success|warning|info|danger|premium|neutral">`.
- [ ] Crear `<NStatTile>` para KPIs con mono font + glass-icon.
- [ ] Estos wrappers **no sustituyen** shadcn-vue; lo envuelven para garantizar consistencia.

### Fase 3 · Aplicación en vistas existentes (2–3 días, en paralelo)
- [ ] **Dashboard.vue** → aplicar `<NStatTile>`, `<NCard hero>` para "próximos servicios", bg-motorspace.
- [ ] **Vehicle/Index.vue** → tabla con tokens nuevos, header sticky, KPI header en glass.
- [ ] **Vehicle/Show.vue** → re‑estructurar en grid 12 col con `<NCard>` (specs, mantenimiento, documentos, alertas).
- [ ] **Maintenance/Show.vue** → timeline con tokens motion.
- [ ] **Marketplace/Generate.vue** → hero con `<NCard hero>` + CTA accent.
- [ ] **Sidebar / WebHeader** → refinar `bg-card/50 border-white/15` → usar tokens semánticos.

### Fase 4 · Páginas públicas (1 día)
- [ ] **Vehicle/Public.vue** → más "hero", aplicar `bg-motorspace-hero`.
- [ ] **Marketplace/PublicReport.vue** → versión shareable con tipografía display.
- [ ] **Auth (Login/Register)** → fondo motorspace-hero, card central con liquid-glass.

### Fase 5 · Documentación (½ día)
- [ ] Crear `docs/design/tokens.md`, `docs/design/components/*.md`, `docs/design/patterns/*.md`.
- [ ] Añadir changelog visual a `docs/audit_report.md`.

### Fase 6 · QA & auditoría (½ día)
- [ ] Lighthouse → comprobar que `backdrop-filter` no mata el rendimiento (mobile).
- [ ] WCAG AA en contrastes (papaya sobre verde oscuro, blanco sobre heritage blue…).
- [ ] Dark mode: revisar inversión de todos los tokens nuevos.
- [ ] `php artisan test --parallel` → asegurar 0 regresiones.

---

## 14 · Métricas de éxito

| KPI                                       | Objetivo                           |
| ----------------------------------------- | ---------------------------------- |
| Lighthouse Performance (mobile)           | ≥ 85                               |
| Lighthouse Accessibility                 | ≥ 95                               |
| First Contentful Paint                    | ≤ 1.4 s                            |
| Tests PHPUnit                             | 0 regresiones, mismo nº o +        |
| Adopción de `<NCard>` en vistas migradas  | ≥ 80 % en Fase 3                   |
| Tiempo de onboarding visual (mock review) | "Wow" en < 5 s                     |

---

## 15 · Riesgos & mitigaciones

| Riesgo                                                       | Mitigación                                                              |
| ------------------------------------------------------------ | ----------------------------------------------------------------------- |
| `backdrop-filter` agresivo degrada FPS en móviles low‑end     | Usar `.glass-card` (blur 16) por defecto, `.liquid-glass` solo en modales |
| Refracción SVG no soportada en algunos navegadores            | Fallback `@supports not (filter: url(#liquid-glass))` → glass clásico  |
| Demasiados colores heritage saturan la UI                    | Regla: máximo **2 colores heritage** visibles por pantalla              |
| Cambios rompen contraste WCAG en dark mode                   | Tabla de contraste en `docs/design/patterns/dark-mode.md` + auditoría   |
| Equipos consumen "mucho glass" → ilegibilidad                | Regla: tablas y listas densas usan `.glass-item` (mínimo blur)          |
| Resistencia al cambio en componentes ya migrados              | Wrappers (`<NCard>`) en lugar de tocar shadcn-vue directo              |

---

## 16 · Decisiones pendientes (necesitan respuesta del usuario)

Antes de Fase 1, confirmar:

1. **¿Paleta heritage completa o subset?**
   - (a) **Completa** (blue + silver + yellow + red + navy + bronze) → más personalidad, más riesgo de saturar.
   - (b) **Subset motoresport clásicos**: bleu + silver + bronze solo.
   - (c) **Solo identidad**: green + papaya + rojo + silver (lo actual).

2. **¿Tipografía?**
   - (a) Inter + JetBrains Mono (recomendado, todo Google Fonts).
   - (b) Geist + Geist Mono (Vercel, más "Space UI" puro, requiere self‑host).
   - (c) Mantener Figtree (actual).

3. **¿Aplicar Liquid Glass real con SVG filter?**
   - (a) **Sí** (más impacto visual, coste de render).
   - (b) **No**, seguir con backdrop‑filter blur alto (más rendimiento).

4. **¿Empezar por Dashboard (Fase 3) o por `app.css` tokens (Fase 1)?**
   - Recomendado: Fase 1 primero → todo lo demás se apoya en tokens.

5. **¿Generamos mock visual con OpenDesigner antes de tocar código?**
   - (a) **Sí** (1 mock Dashboard + 1 mock Vehicle/Show).
   - (b) **No**, vamos directo a código con este plan como spec.

---

## 17 · Notas finales

- **No ejecutar `npm run build`** en ningún paso. El usuario lo lanza cuando lo considere.
- **No tocar componentes shadcn-vue directamente** en `Components/ui/`. Usar CLI si hay que actualizar o añadir nuevos.
- **No introducir dependencias nuevas** sin justificación clara. Para Liquid Glass nos basta con CSS + SVG filter (cero JS).
- Este plan **convive** con la Fase 4 de suscripciones y los módulos actuales (Vehicle, Maintenance, Alerts, Documents, Identity, Marketplace). No es un "rediseño desde cero", es una **evolución coherente** del lenguaje visual existente.
