# Git Workflow

> Flujo de trabajo con Git para GarageOS.

---

## 🌿 Branching Strategy

Usamos **Git Flow simplificado**:

```
main            ← Producción
└── develop     ← Integración
    ├── feature/vehicle-import-wizard
    ├── feature/marketplace-b2b
    ├── fix/iedmt-calculation-co2
    └── refactor/extract-valuation-service
```

### Tipos de branch

| Tipo | Naming | Origen | Merge a |
|------|--------|--------|---------|
| Feature | `feature/*` | `develop` | `develop` |
| Fix | `fix/*` | `develop` | `develop` |
| Hotfix | `hotfix/*` | `main` | `main` + `develop` |
| Release | `release/*` | `develop` | `main` + `develop` |

---

## 📝 Convención de Commits

### Conventional Commits
```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Tipos
- `feat` - Nueva funcionalidad
- `fix` - Bug fix
- `docs` - Solo documentación
- `style` - Formato (sin cambio de lógica)
- `refactor` - Refactor sin cambio de comportamiento
- `test` - Añadir/modificar tests
- `chore` - Mantenimiento (deps, build, etc)
- `perf` - Mejora de performance

### Ejemplos
```bash
git commit -m "feat(vehicle-import): add IEDMT calculator service"
git commit -m "fix(marketplace): correct platform fee calculation"
git commit -m "docs(readme): update Docker setup instructions"
git commit -m "refactor(vehicle): extract specs validation to request"
git commit -m "test(import): add coverage for valuation edge cases"
```

---

## 🔄 Flujo de trabajo diario

### 1. Empezar nueva feature
```bash
# Actualizar develop
git checkout develop
git pull origin develop

# Crear branch
git checkout -b feature/marketplace-b2b

# Trabajar...
```

### 2. Hacer commits
```bash
git add .
git commit -m "feat(marketplace): add B2B transaction model"
```

### 3. Push y PR
```bash
git push origin feature/marketplace-b2b
# Crear PR en GitHub/GitLab
```

### 4. Merge
- Review de al menos 1 persona
- Tests pasando
- CI verde
- Squash merge a develop

---

## 🛡️ Reglas de protección

### En `main`
- ❌ Push directo prohibido
- ✅ PR + review obligatorio
- ✅ Tests pasando
- ✅ Coverage mínimo 80%

### En `develop`
- ✅ Push directo permitido para el equipo core
- ✅ PR recomendado para colaboradores externos

---

## 🏷️ Versionado y Tags

### Semantic Versioning
```
v MAJOR . MINOR . PATCH
```

- `MAJOR` - Breaking changes
- `MINOR` - Nuevas features (backwards compatible)
- `PATCH` - Bug fixes

### Crear release
```bash
git checkout main
git pull
git merge --no-ff release/1.2.0 -m "Release 1.2.0"
git tag -a v1.2.0 -m "Release 1.2.0"
git push origin main --tags
```

---

## 📦 Comandos útiles

### Limpiar branches locales eliminadas
```bash
git fetch -p
git branch -vv | grep 'gone' | awk '{print $1}' | xargs git branch -D
```

### Stash temporal
```bash
git stash
# hacer otra cosa
git stash pop
```

### Reescribir último commit
```bash
git commit --amend
```

### Ver cambios sin commit
```bash
git diff
git status
```

---

## 🚫 Lo que NUNCA debes hacer

- ❌ Commitear `.env` o credenciales
- ❌ Force push a `main` o `develop`
- ❌ Commitear `vendor/`, `node_modules/`
- ❌ Commits genéricos ("update", "fix", "wip")
- ❌ Merge de branches sin review

---

## 📚 Recursos

- [Conventional Commits](https://www.conventionalcommits.org/)
- [Git Flow](https://nvie.com/posts/a-successful-git-branching-model/)
- [Semantic Versioning](https://semver.org/)
