# GarageOS — Instrucciones para Copilot

## Proyecto
- Laravel 11 + Inertia.js + Vue 3 + PrimeVue + Tailwind
- Módulos en `app/Modules/`: Vehicle, Maintenance, Alerts, Documents, Identity, Marketplace

## Convenciones
- FormRequests para TODA validación (no inline)
- Eager loading obligatorio en relaciones
- Tests: happy path + validación + edge cases
- Idioma: español para comentarios y mensajes

## Ahorro de Tokens
- Usa `#file:` en vez de pegar contenido
- Usa `/compact` tras 30 mensajes
- Prefiere `@Explore` para investigación read-only
- Usa `codebase-memory-mcp` tools para exploración estructural

## Estructura de Módulos
```
app/Modules/{Module}/
    Controllers/  Models/  Requests/  Services/  Routes/
resources/js/Pages/{Module}/
tests/Feature/{Module}/
```
