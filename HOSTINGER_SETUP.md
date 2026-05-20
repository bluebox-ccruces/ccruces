# Publicación en Hostinger (ccruces.com)

## 1) Subida a `public_html`
- Sube todo el contenido del proyecto al directorio `public_html`.
- Mantén la estructura `includes/`, `database/`, `scripts/`, `data/`, `assets/`, `img/` y los archivos `.php` en raíz.

## 2) PHP
- Esta versión requiere PHP 8.0+.
- Recomendado: PHP 8.1 o superior en `hPanel > Advanced > PHP Configuration`.

## 3) Base de datos MySQL (recomendado para productivo)
1. Crea una base de datos MySQL en Hostinger.
2. Crea usuario y contraseña para la base.
3. Edita `includes/config.php` con credenciales reales:
   - `host`
   - `port`
   - `name`
   - `user`
   - `pass`
4. Importa `database/schema.sql` desde phpMyAdmin.
5. Importa `database/seed.sql` desde phpMyAdmin para cargar datos iniciales.
6. Importa `database/ensure-utf8mb4.sql` para asegurar soporte de caracteres especiales (á, é, ñ, emojis).
7. Opcional por consola SSH: ejecuta `php scripts/sync-json-to-db.php` para sincronizar desde JSON.
8. Si detectas textos dañados (`Ã`, `Â`), ejecuta `database/fix-mojibake-prod.sql` y luego reimporta `database/seed.sql`.

## 4) Permisos
- Si mantienes fallback JSON, conserva permisos de lectura/escritura para `data/`.
- Recomendación estándar: `644` para archivos y `755` para carpetas.

## 5) Accesos iniciales
- Admin: `admin` / `Admin@2026!`
- Demo cliente: `demo` / `Demo@2026!`

## 6) Seguridad inicial
- Cambia contraseñas iniciales inmediatamente.
- Mantén `data/.htaccess` para bloquear acceso directo a JSON.
- Usa HTTPS activo con SSL de Hostinger.

## 7) URLs clave
- Inicio: `/index.php`
- Servicios: `/servicios.php`
- Blog público: `/blog.php`
- Login: `/login.php`
- Panel cliente: `/panel.php`
- Admin blog: `/admin.php`

## 8) Configurar accesos reales a servicios
- Actualiza `private_url` en `services` (DB) o en `data/services.json` antes de sincronizar.

## 9) Verificación rápida post-lanzamiento
- Login admin y demo funcional.
- Crear/eliminar post desde `admin.php`.
- Apertura de demos y rutas privadas por servicio.
- Navegación completa sin errores PHP.
