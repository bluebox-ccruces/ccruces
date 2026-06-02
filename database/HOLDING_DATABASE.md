# CCruces Holding Database

Esta base esta pensada como una sola base central desde cero para `ccruces.com`
y los servicios del holding. La web principal, Bocado y BlueSales deben
consumirla por medio de una API central cuando avancemos a `api.ccruces.com`.

## Importacion en Hostinger

Para una base nueva, importa en este orden desde phpMyAdmin:

1. `database/schema.sql`
2. `database/seed.sql`
3. `database/add-service-modal-fields.sql` si vienes de una version antigua
4. `database/add-post-images.sql` si vienes de una version antigua
5. `database/add-users-email.sql` si vienes de una version antigua
6. `database/ensure-utf8mb4.sql`

Si la base es completamente nueva, los pasos principales son `schema.sql`,
`seed.sql` y `ensure-utf8mb4.sql`. El `seed.sql` actual no carga servicios
heredados; crea la estructura inicial del holding con Bocado y BlueSales, mas
datos de demostracion para que los usuarios puedan probar los flujos.

## Usuarios demo

Estos usuarios son solo para ambiente de prueba:

```txt
demo_admin / Demo1234!
demo_bocado / Demo1234!
demo_bluesales / Demo1234!
```

Cambia o elimina estas cuentas antes de pasar a produccion.

## Nucleo del holding

- `companies`: empresa o holding propietario de los datos.
- `sites`: dominios/subdominios como `ccruces.com`, `api.ccruces.com`,
  `bocado.ccruces.com` y `bluesales.ccruces.com`.
- `users`: usuarios globales del holding.
- `services`: catalogo de servicios visibles en la web.
- `service_modules`: modulos tecnicos por servicio.
- `api_clients`: clientes autorizados para consumir la API.
- `permissions`: permisos granulares globales o por servicio.
- `roles`: roles globales o roles especificos de cada servicio.
- `role_permissions`: permisos incluidos en cada rol.
- `user_service_access`: servicios a los que puede entrar cada usuario.
- `user_role_assignments`: roles asignados a cada usuario.
- `customers`: clientes compartidos por servicios.
- `leads`: oportunidades/contactos capturados desde las webs.
- `posts`, `post_images`, `post_likes`, `post_comments`: contenido y blog.
- `audit_logs`: registro central de acciones.

## Accesos, roles y permisos

Un usuario se crea una sola vez en `users`. Despues se le concede acceso a uno
o mas servicios en `user_service_access`.

Los roles viven en `roles`:

- Roles globales: `role_scope = 'global'` y `service_id = NULL`.
- Roles por servicio: `role_scope = 'service'` y `service_id` apunta al
  servicio correspondiente.

Los permisos viven en `permissions` y se conectan a los roles por medio de
`role_permissions`. La asignacion final del usuario queda en
`user_role_assignments`.

Ejemplo:

```txt
demo_admin
  - acceso: bocado, bluesales
  - roles: holding.admin, bocado.supervisor, bluesales.manager

demo_bocado
  - acceso: bocado
  - roles: bocado.operator

demo_bluesales
  - acceso: bluesales
  - roles: bluesales.sales_rep
```

## Servicios iniciales

- `bocado`: control inteligente de comedor.
- `bluesales`: gestion comercial centralizada.

Los demas servicios se agregaran despues en `services`, `sites`,
`service_modules` y `api_clients`.

## Bocado

Tablas especificas para control de comedor:

- `bocado_locations`: comedores/sedes.
- `bocado_cost_centers`: centros de costo.
- `bocado_diners`: comensales, empleados, contratistas o visitantes.
- `bocado_meal_types`: desayuno, almuerzo, cena u otros tipos.
- `bocado_meal_events`: consumos registrados con costo, subsidio y validacion.

El seed carga sedes, centros de costo, tipos de comida, comensales y consumos
de muestra para probar reportes y validaciones.

## BlueSales

Tablas especificas para gestion comercial:

- `bluesales_accounts`: cuentas/clientes comerciales.
- `bluesales_contacts`: contactos por cuenta.
- `bluesales_products`: productos comercializables.
- `bluesales_opportunities`: oportunidades del pipeline.
- `bluesales_opportunity_items`: detalle de oportunidad.
- `bluesales_orders`: pedidos confirmados.
- `bluesales_order_items`: detalle del pedido.

El seed carga clientes, contactos, productos, oportunidades, items de
oportunidad, pedidos e items de pedido para probar el pipeline comercial.

## Regla de arquitectura

Las paginas no deberian conectarse directo a la base en el largo plazo.

Flujo recomendado:

```txt
ccruces.com
bocado.ccruces.com
bluesales.ccruces.com
admin.ccruces.com
        |
        v
api.ccruces.com
        |
        v
Base central del holding
```

## Variables esperadas

Cuando configures `includes/config.php` o variables de entorno en Hostinger,
usa los datos reales de la base creada en hPanel:

```txt
DB_HOST=localhost
DB_PORT=3306
DB_NAME=uXXXX_ccruces_holding
DB_USER=uXXXX_ccruces_user
DB_PASS=...
DB_CHARSET=utf8mb4
```
