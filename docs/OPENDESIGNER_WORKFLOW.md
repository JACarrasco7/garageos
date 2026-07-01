# OpenDesigner + Copilot: Workflow para GarageOS

## Flujo de Trabajo

### 1. Iniciar OpenDesigner
```powershell
# Activar Node 24
$env:PATH = "$env:LOCALAPPDATA\fnm_node24\node-v24.11.1-win-x64;$env:PATH"

# Iniciar servidor
cd C:\laragon\www\open-design
pnpm tools-dev start
```

### 2. Generar Diseño

1. Abre [http://127.0.0.1:56517](http://127.0.0.1:56517)
2. En "Sistemas de diseño", selecciona o crea **GarageOS**
3. Escribe un prompt en el campo principal
4. Click en "Run"

### 3. Implementar con Copilot

Una vez generado el diseño:

```
👤 Tú: Implementa este diseño en GarageOS usando Vue 3 + Inertia + shadcn-vue
   Usa el design system definido en DESIGN_SYSTEM.md
   Convierte el HTML/JSX a componentes .vue con TypeScript
```

Copilot convertirá el diseño a código compatible con tu stack.

## Design System de GarageOS

El archivo [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) contiene:
- Paleta de colores (Nürburgring Green, McLaren Orange, Ferrari Red)
- Tokens de diseño (tipografía, espaciado, radios)
- Patrones de componentes (Cards, Buttons, Inputs)
- Layout patterns (Sidebar, Header, Container)
- Animaciones y microinteracciones

## Ejemplos de Prompts

```
Dashboard principal con cards de estadísticas
Lista de vehículos con cards detalladas
Formulario de importación de vehículos alemanes
Página de detalles de mantenimiento
```

## Componentes Disponibles

- **shadcn-vue**: Button, Card, Input, Badge, Table, Dialog, etc.
- **lucide-vue-next**: Iconos (Car, Wrench, Shield, Calendar, etc.)
- **Personalizados**: AppSidebar, EmptyState, NotificationBell

## Archivos de Referencia

- [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) - Tokens y patrones
- [GARAGEOS-VISUAL-IMPROVEMENT-PLAN.md](GARAGEOS-VISUAL-IMPROVEMENT-PLAN.md) - Plan de mejoras
- [resources/css/app.css](resources/css/app.css) - Variables CSS
- [resources/js/Components/ui/button/](resources/js/Components/ui/button/) - Componentes shadcn

## Tips

1. **Glassmorphism**: Usa `backdrop-blur-xl` con `bg-card/90`
2. **Gradientes**: Primary usa `from-primary to-primary/80`
3. **Hover effects**: `hover:scale-105 hover:shadow-lg`
4. **Colors semánticos**: `text-foreground`, `bg-muted`, NO `text-gray-*`

## Troubleshooting

**OpenDesigner no arranca**: Verifica Node 24 está activo
**Copilot no genera código**: Asegúrate que GitHub Copilot CLI está en PATH
**Diseño no se ve bien**: Revisa DESIGN_SYSTEM.md tokens
