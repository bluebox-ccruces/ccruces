# Reestructuracion base para integrar mas servicios

## Estado actual
Se separo la app en capas para crecer sin romper:

- `includes/core/app.php`
  - Configuracion
  - Conexion DB (PDO)
  - Helpers base
  - Lectura/escritura JSON fallback

- `includes/core/session.php`
  - Flash messages
  - Login helpers
  - Roles y permisos
  - CSRF

- `includes/repositories/users_repository.php`
  - Lectura de usuarios (DB o JSON fallback)

- `includes/repositories/services_repository.php`
  - Catalogo de servicios

- `includes/repositories/posts_repository.php`
  - Blog (listar/buscar/crear/eliminar)

- `includes/bootstrap.php`
  - Punto unico de carga
  - Compatibilidad con funciones legacy (`load_json`, `save_json`)

## Ventaja para integrar nuevos proyectos
Para integrar un nuevo servicio solo necesitas:
1. Alta en tabla `services`.
2. Definir `demo_url` y `private_url`.
3. (Opcional) Crear pagina demo dedicada.

## Siguiente paso recomendado
Crear modulo por servicio con esta estructura:
- `modules/<servicio>/demo.php`
- `modules/<servicio>/assets/*`
- `modules/<servicio>/README.md`

Luego mapear `demo_url` a `modules/<servicio>/demo.php`.
