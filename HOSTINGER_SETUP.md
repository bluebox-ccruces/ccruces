# Publicacion desde cero en Hostinger (ccruces.com)

## 1) Subida a `public_html`

- Sube todo el contenido del proyecto al directorio `public_html`.
- Manten la estructura `includes/`, `database/`, `scripts/`, `data/`, `assets/`, `img/` y los archivos `.php` en raiz.

## 2) PHP

- Esta version requiere PHP 8.0+.
- Recomendado: PHP 8.1 o superior en `hPanel > Advanced > PHP Configuration`.

## 3) Base de datos MySQL central

1. Crea una base de datos MySQL en Hostinger para el holding.
   - Nombre sugerido: `ccruces_holding`
   - Hostinger normalmente agregara un prefijo, por ejemplo `uXXXX_ccruces_holding`.
2. Crea usuario y contrasena para la base.
3. En Hostinger, crea `includes/config.local.php` copiando la plantilla `includes/config.local.example.php` y completa las credenciales reales:
   - `host`
   - `port`
   - `name`
   - `user`
   - `pass`
4. Para una base nueva, importa `database/schema.sql` desde phpMyAdmin.
5. Importa `database/seed.sql` para cargar datos iniciales y contenido demo del holding, Bocado y BlueSales.
6. Importa `database/ensure-utf8mb4.sql` para asegurar soporte de caracteres especiales.

No importes `database/add-post-images.sql`, `database/add-service-modal-fields.sql` ni `database/add-users-email.sql` en una base nueva. Esas columnas ya vienen en `schema.sql`. Usa esos archivos `add-*` solo si actualizas una base antigua.

No subas `includes/config.local.php` a GitHub. Ese archivo queda solo en Hostinger y contiene la clave real de la base.

## 4) Estructura central

La base nueva incluye:

- Nucleo del holding: `companies`, `sites`, `users`, `services`, `service_modules`, `api_clients`, `customers`, `leads`, `audit_logs`.
- Web y blog: `posts`, `post_images`, `post_likes`, `post_comments`.
- Bocado: `bocado_locations`, `bocado_cost_centers`, `bocado_diners`, `bocado_meal_types`, `bocado_meal_events`.
- BlueSales: `bluesales_accounts`, `bluesales_contacts`, `bluesales_products`, `bluesales_opportunities`, `bluesales_opportunity_items`, `bluesales_orders`, `bluesales_order_items`.

Mas detalle en `database/HOLDING_DATABASE.md`.

## 5) Permisos

- Si mantienes fallback JSON, conserva permisos de lectura/escritura para `data/`.
- Recomendacion estandar: `644` para archivos y `755` para carpetas.

## 6) Accesos iniciales

- Crea usuarios manualmente desde un entorno controlado o desde SQL.
- Usa contrasenas unicas y robustas.
- No publiques ni compartas credenciales en documentacion ni en GitHub.

Usuarios demo incluidos solo para pruebas:

```txt
demo_admin / Demo1234!      -> holding.admin, bocado.supervisor, bluesales.manager
demo_bocado / Demo1234!     -> bocado.operator
demo_bluesales / Demo1234!  -> bluesales.sales_rep
```

Cambia o elimina estas cuentas antes de produccion real.

## 7) Seguridad inicial

- Usa credenciales distintas por entorno: local, staging y produccion.
- Manten `data/.htaccess` para bloquear acceso directo a JSON.
- Usa HTTPS activo con SSL de Hostinger.

## 8) URLs clave

- Inicio: `/index.php`
- Servicios: `/servicios.php`
- Blog publico: `/blog.php`
- Login: `/login.php`
- Panel cliente: `/panel.php`
- Admin: `/admin.php`

## 9) Configurar accesos reales a servicios

Actualiza `private_url` en la tabla `services`:

- Bocado: `https://bocado.ccruces.com/login`
- BlueSales: `https://bluesales.ccruces.com`

## 10) Verificacion rapida post-lanzamiento

- Login funcional con cuentas creadas de forma segura.
- Servicios iniciales visibles: Bocado y BlueSales.
- Blog con publicaciones demo para Bocado y BlueSales.
- Bocado con sedes, centros de costo, comensales y consumos demo.
- BlueSales con clientes, productos, oportunidades y pedidos demo.
- Apertura de Bocado y Bluesales desde sus enlaces privados.
- Navegacion completa sin errores PHP.
