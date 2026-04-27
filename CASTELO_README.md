# Castelo Group - Backend Laravel

## Configuración rápida

### Base de datos
- Base de datos: `castelo_group`
- Usuario admin: `admin@castelobienes.ec`
- Contraseña admin: `Castelo2026!`

### Comandos
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

### API Base URL
`http://castelo-group-back.test/api`

## Módulos implementados
- Autenticación con Laravel Sanctum (JWT tokens)
- CRUD de propiedades con filtros y paginación
- Subida de medios (fotos/videos)
- Sistema de afiliados con flujo completo
- Comisiones configurables por admin
- Panel de administración con estadísticas
- Formularios de contacto
- Roles: admin / client / affiliate
