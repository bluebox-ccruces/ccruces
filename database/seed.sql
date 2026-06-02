SET NAMES utf8mb4;

INSERT INTO companies (id, name, legal_name, primary_domain, status) VALUES
('ccruces', 'CCruces Holding', 'CCruces Holding', 'ccruces.com', 'active')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
legal_name = VALUES(legal_name),
primary_domain = VALUES(primary_domain),
status = VALUES(status);

INSERT INTO sites (id, company_id, service_id, name, slug, domain, site_type, status) VALUES
('site-ccruces-web', 'ccruces', NULL, 'CCruces Web', 'web', 'ccruces.com', 'holding', 'active'),
('site-ccruces-www', 'ccruces', NULL, 'CCruces WWW', 'www', 'www.ccruces.com', 'holding', 'active'),
('site-ccruces-api', 'ccruces', NULL, 'CCruces API', 'api', 'api.ccruces.com', 'api', 'active'),
('site-ccruces-admin', 'ccruces', NULL, 'CCruces Admin', 'admin', 'admin.ccruces.com', 'admin', 'active'),
('site-bocado', 'ccruces', 'bocado', 'Bocado', 'bocado', 'bocado.ccruces.com', 'service', 'active'),
('site-bluesales', 'ccruces', 'bluesales', 'BlueSales', 'bluesales', 'bluesales.ccruces.com', 'service', 'active')
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
service_id = VALUES(service_id),
name = VALUES(name),
slug = VALUES(slug),
domain = VALUES(domain),
site_type = VALUES(site_type),
status = VALUES(status);

INSERT INTO users (company_id, username, email, name, role, password_hash, status) VALUES
('ccruces', 'ccruces', 'ccruces@ccruces.com', 'Administrador CCruces', 'admin', '$2y$10$bnOVcnWTaKJiDzRVVszSi.UemM1kmr93aWCc7Nxd7mTQ/j/JD3Mry', 1),
('ccruces', 'demo_admin', 'demo.admin@ccruces.com', 'Demo Administrador', 'admin', '$2y$10$uooWJ.T/wzsq1baIlIt05.vg3tsLMXg11N9Nh1CiVrKzA3jOYFK8S', 1),
('ccruces', 'demo_bocado', 'demo.bocado@ccruces.com', 'Demo Bocado', 'client', '$2y$10$uooWJ.T/wzsq1baIlIt05.vg3tsLMXg11N9Nh1CiVrKzA3jOYFK8S', 1),
('ccruces', 'demo_bluesales', 'demo.bluesales@ccruces.com', 'Demo BlueSales', 'client', '$2y$10$uooWJ.T/wzsq1baIlIt05.vg3tsLMXg11N9Nh1CiVrKzA3jOYFK8S', 1)
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
email = VALUES(email),
name = VALUES(name),
role = VALUES(role),
password_hash = VALUES(password_hash),
status = VALUES(status);

INSERT INTO services (
  id,
  company_id,
  site_id,
  slug,
  category,
  name,
  tagline,
  description,
  logo,
  demo_url,
  private_url,
  status,
  sort_order
) VALUES
(
  'bocado',
  'ccruces',
  'site-bocado',
  'bocado',
  'operaciones',
  'Bocado',
  'Control inteligente de comedor',
  'Gestion de consumos, comensales, validaciones y costos de comedor empresarial.',
  'img/Bocado Logo.png',
  '',
  'https://bocado.ccruces.com/login',
  'En construccion',
  1
),
(
  'bluesales',
  'ccruces',
  'site-bluesales',
  'bluesales',
  'comercial',
  'BlueSales',
  'Gestion comercial centralizada',
  'Plataforma comercial para clientes, oportunidades, pedidos y trazabilidad de ventas.',
  'img/Icono BB.png',
  '',
  'https://bluesales.ccruces.com',
  'En construccion',
  2
)
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
site_id = VALUES(site_id),
slug = VALUES(slug),
category = VALUES(category),
name = VALUES(name),
tagline = VALUES(tagline),
description = VALUES(description),
logo = VALUES(logo),
demo_url = VALUES(demo_url),
private_url = VALUES(private_url),
status = VALUES(status),
sort_order = VALUES(sort_order);

INSERT INTO service_modules (company_id, service_id, code, name, base_url, api_scope, status) VALUES
('ccruces', 'bocado', 'bocado-core', 'Bocado Core', 'https://bocado.ccruces.com', 'bocado:read,bocado:write', 'development'),
('ccruces', 'bluesales', 'bluesales-core', 'BlueSales Core', 'https://bluesales.ccruces.com', 'bluesales:read,bluesales:write', 'development')
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
service_id = VALUES(service_id),
name = VALUES(name),
base_url = VALUES(base_url),
api_scope = VALUES(api_scope),
status = VALUES(status);

INSERT INTO api_clients (company_id, service_id, name, client_key, allowed_origins, scopes, status) VALUES
('ccruces', NULL, 'CCruces Web Client', 'ccruces_web', 'https://ccruces.com,https://www.ccruces.com', 'holding:read', 'active'),
('ccruces', NULL, 'CCruces Admin Client', 'ccruces_admin_web', 'https://admin.ccruces.com', 'holding:read,holding:write,bocado:read,bocado:write,bluesales:read,bluesales:write', 'active'),
('ccruces', 'bocado', 'Bocado Web Client', 'ccruces_bocado_web', 'https://bocado.ccruces.com', 'bocado:read,bocado:write', 'active'),
('ccruces', 'bluesales', 'BlueSales Web Client', 'ccruces_bluesales_web', 'https://bluesales.ccruces.com', 'bluesales:read,bluesales:write', 'active')
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
service_id = VALUES(service_id),
name = VALUES(name),
allowed_origins = VALUES(allowed_origins),
scopes = VALUES(scopes),
status = VALUES(status);

INSERT INTO permissions (service_id, code, name, description, permission_scope) VALUES
(NULL, 'holding.dashboard.view', 'Ver dashboard del holding', 'Permite consultar indicadores generales del holding.', 'global'),
(NULL, 'holding.users.manage', 'Gestionar usuarios del holding', 'Permite crear, editar y suspender usuarios globales.', 'global'),
(NULL, 'holding.services.manage', 'Gestionar servicios del holding', 'Permite administrar servicios, sitios, modulos y clientes API.', 'global'),
('bocado', 'bocado.dashboard.view', 'Ver dashboard de Bocado', 'Permite consultar resumen de comedor, costos y actividad.', 'service'),
('bocado', 'bocado.diners.manage', 'Gestionar comensales', 'Permite crear, editar o bloquear comensales.', 'service'),
('bocado', 'bocado.meals.register', 'Registrar consumos', 'Permite registrar consumos de comedor.', 'service'),
('bocado', 'bocado.reports.view', 'Ver reportes de Bocado', 'Permite revisar reportes por sede, centro de costo y comensal.', 'service'),
('bluesales', 'bluesales.dashboard.view', 'Ver dashboard de BlueSales', 'Permite consultar resumen comercial.', 'service'),
('bluesales', 'bluesales.accounts.manage', 'Gestionar cuentas comerciales', 'Permite administrar cuentas y contactos.', 'service'),
('bluesales', 'bluesales.opportunities.manage', 'Gestionar oportunidades', 'Permite crear y actualizar oportunidades comerciales.', 'service'),
('bluesales', 'bluesales.orders.manage', 'Gestionar pedidos', 'Permite crear y actualizar pedidos.', 'service'),
('bluesales', 'bluesales.reports.view', 'Ver reportes de BlueSales', 'Permite consultar reportes comerciales.', 'service')
ON DUPLICATE KEY UPDATE
service_id = VALUES(service_id),
name = VALUES(name),
description = VALUES(description),
permission_scope = VALUES(permission_scope);

INSERT INTO roles (company_id, service_id, code, name, description, role_scope, is_system, status) VALUES
('ccruces', NULL, 'holding.admin', 'Administrador del holding', 'Acceso administrativo global a la plataforma.', 'global', 1, 'active'),
('ccruces', NULL, 'holding.viewer', 'Lector del holding', 'Acceso de lectura a informacion general del holding.', 'global', 1, 'active'),
('ccruces', 'bocado', 'bocado.operator', 'Operador Bocado', 'Registra consumos y consulta dashboard de Bocado.', 'service', 1, 'active'),
('ccruces', 'bocado', 'bocado.supervisor', 'Supervisor Bocado', 'Administra comensales, consumos y reportes de Bocado.', 'service', 1, 'active'),
('ccruces', 'bluesales', 'bluesales.sales_rep', 'Vendedor BlueSales', 'Gestiona cuentas y oportunidades comerciales.', 'service', 1, 'active'),
('ccruces', 'bluesales', 'bluesales.manager', 'Gerente BlueSales', 'Gestiona pipeline, pedidos y reportes comerciales.', 'service', 1, 'active')
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
service_id = VALUES(service_id),
name = VALUES(name),
description = VALUES(description),
role_scope = VALUES(role_scope),
is_system = VALUES(is_system),
status = VALUES(status);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code IN ('holding.dashboard.view', 'holding.users.manage', 'holding.services.manage')
WHERE r.code = 'holding.admin'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code IN ('holding.dashboard.view')
WHERE r.code = 'holding.viewer'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code IN ('bocado.dashboard.view', 'bocado.meals.register')
WHERE r.code = 'bocado.operator'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code IN ('bocado.dashboard.view', 'bocado.diners.manage', 'bocado.meals.register', 'bocado.reports.view')
WHERE r.code = 'bocado.supervisor'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code IN ('bluesales.dashboard.view', 'bluesales.accounts.manage', 'bluesales.opportunities.manage')
WHERE r.code = 'bluesales.sales_rep'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code IN ('bluesales.dashboard.view', 'bluesales.accounts.manage', 'bluesales.opportunities.manage', 'bluesales.orders.manage', 'bluesales.reports.view')
WHERE r.code = 'bluesales.manager'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO user_service_access (user_id, company_id, service_id, access_status, granted_by)
SELECT u.id, 'ccruces', s.id, 'active', admin_user.id
FROM users u
JOIN services s ON s.id IN ('bocado', 'bluesales')
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username IN ('ccruces', 'demo_admin')
ON DUPLICATE KEY UPDATE
access_status = VALUES(access_status),
granted_by = VALUES(granted_by);

INSERT INTO user_service_access (user_id, company_id, service_id, access_status, granted_by)
SELECT u.id, 'ccruces', 'bocado', 'active', admin_user.id
FROM users u
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username = 'demo_bocado'
ON DUPLICATE KEY UPDATE
access_status = VALUES(access_status),
granted_by = VALUES(granted_by);

INSERT INTO user_service_access (user_id, company_id, service_id, access_status, granted_by)
SELECT u.id, 'ccruces', 'bluesales', 'active', admin_user.id
FROM users u
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username = 'demo_bluesales'
ON DUPLICATE KEY UPDATE
access_status = VALUES(access_status),
granted_by = VALUES(granted_by);

INSERT INTO user_role_assignments (user_id, role_id, assignment_status, assigned_by)
SELECT u.id, r.id, 'active', admin_user.id
FROM users u
JOIN roles r ON r.code = 'holding.admin'
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username IN ('ccruces', 'demo_admin')
ON DUPLICATE KEY UPDATE
assignment_status = VALUES(assignment_status),
assigned_by = VALUES(assigned_by);

INSERT INTO user_role_assignments (user_id, role_id, assignment_status, assigned_by)
SELECT u.id, r.id, 'active', admin_user.id
FROM users u
JOIN roles r ON r.code IN ('bocado.supervisor', 'bluesales.manager')
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username = 'demo_admin'
ON DUPLICATE KEY UPDATE
assignment_status = VALUES(assignment_status),
assigned_by = VALUES(assigned_by);

INSERT INTO user_role_assignments (user_id, role_id, assignment_status, assigned_by)
SELECT u.id, r.id, 'active', admin_user.id
FROM users u
JOIN roles r ON r.code = 'bocado.operator'
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username = 'demo_bocado'
ON DUPLICATE KEY UPDATE
assignment_status = VALUES(assignment_status),
assigned_by = VALUES(assigned_by);

INSERT INTO user_role_assignments (user_id, role_id, assignment_status, assigned_by)
SELECT u.id, r.id, 'active', admin_user.id
FROM users u
JOIN roles r ON r.code = 'bluesales.sales_rep'
LEFT JOIN users admin_user ON admin_user.username = 'ccruces'
WHERE u.username = 'demo_bluesales'
ON DUPLICATE KEY UPDATE
assignment_status = VALUES(assignment_status),
assigned_by = VALUES(assigned_by);

INSERT INTO bocado_locations (company_id, code, name, status) VALUES
('ccruces', 'main-dining-room', 'Comedor principal', 'active'),
('ccruces', 'packing-dining-room', 'Comedor packing', 'active'),
('ccruces', 'field-station', 'Punto de campo', 'active')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
status = VALUES(status);

INSERT INTO bocado_cost_centers (company_id, code, name, status) VALUES
('ccruces', 'general', 'Centro de costo general', 'active'),
('ccruces', 'operaciones', 'Operaciones', 'active'),
('ccruces', 'packing', 'Packing', 'active'),
('ccruces', 'administracion', 'Administracion', 'active')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
status = VALUES(status);

INSERT INTO bocado_meal_types (company_id, code, name, unit_price, status) VALUES
('ccruces', 'breakfast', 'Desayuno', 4.50, 'active'),
('ccruces', 'lunch', 'Almuerzo', 9.80, 'active'),
('ccruces', 'dinner', 'Cena', 8.60, 'active'),
('ccruces', 'snack', 'Refrigerio', 3.20, 'active')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
unit_price = VALUES(unit_price),
status = VALUES(status);

INSERT INTO bluesales_products (company_id, sku, name, category, unit, status) VALUES
('ccruces', 'BLUEBERRY-FRESH-KG', 'Arandano fresco', 'arandano', 'kg', 'active'),
('ccruces', 'BLUEBERRY-PACKED-KG', 'Arandano empacado', 'arandano', 'kg', 'active'),
('ccruces', 'BLUEBERRY-PREMIUM-KG', 'Arandano premium exportacion', 'arandano', 'kg', 'active'),
('ccruces', 'BLUEBERRY-FROZEN-KG', 'Arandano congelado', 'arandano', 'kg', 'active')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
category = VALUES(category),
unit = VALUES(unit),
status = VALUES(status);

UPDATE services
SET status = 'Demo disponible',
    summary = CASE id
      WHEN 'bocado' THEN 'Demo con sedes, centros de costo, comensales y consumos listos para validar el flujo de comedor.'
      WHEN 'bluesales' THEN 'Demo con clientes, productos, oportunidades y pedidos para probar el flujo comercial.'
      ELSE summary
    END,
    content = CASE id
      WHEN 'bocado' THEN 'Bocado permite registrar consumos por colaborador, sede, centro de costo y tipo de comida. Esta muestra permite simular validaciones, costos y reportes diarios.'
      WHEN 'bluesales' THEN 'BlueSales centraliza cuentas comerciales, contactos, oportunidades, productos y pedidos. Esta muestra permite revisar pipeline, montos estimados y ordenes de venta.'
      ELSE content
    END,
    benefits = CASE id
      WHEN 'bocado' THEN 'Control por comensal\nCosto por centro de costo\nValidacion de consumos\nTrazabilidad diaria'
      WHEN 'bluesales' THEN 'Pipeline comercial\nClientes y contactos\nProductos y pedidos\nSeguimiento de margen'
      ELSE benefits
    END,
    financial_benefits = CASE id
      WHEN 'bocado' THEN 'Reduce consumos no autorizados\nMejora negociacion con proveedores\nPermite auditar subsidios'
      WHEN 'bluesales' THEN 'Mejora forecast comercial\nReduce oportunidades olvidadas\nOrdena precios y volumen'
      ELSE financial_benefits
    END,
    roi_note = CASE id
      WHEN 'bocado' THEN 'La demo simula ahorro por control de raciones y centros de costo.'
      WHEN 'bluesales' THEN 'La demo simula seguimiento de ventas para proteger margen y volumen.'
      ELSE roi_note
    END
WHERE id IN ('bocado', 'bluesales');

INSERT INTO customers (company_id, external_code, name, tax_id, email, phone, status) VALUES
('ccruces', 'CUST-AGRO-001', 'Agro Norte SAC', '20100000001', 'compras@agronorte.example', '+51 900 100 001', 'active'),
('ccruces', 'CUST-EXPORT-002', 'Pacific Berry Export', '20100000002', 'ventas@pacificberry.example', '+51 900 100 002', 'active'),
('ccruces', 'CUST-DEMO-003', 'Cliente Demo Internacional', 'INT-DEMO-003', 'buyer@demo-international.example', '+1 555 010 0300', 'prospect')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
tax_id = VALUES(tax_id),
email = VALUES(email),
phone = VALUES(phone),
status = VALUES(status);

INSERT INTO posts (id, company_id, site_id, service_id, title, slug, excerpt, content, author, is_published, published_at) VALUES
('demo-bocado-control-comedor', 'ccruces', 'site-ccruces-web', 'bocado', 'Bocado demo: control de comedor desde el primer dia', 'bocado-demo-control-comedor', 'Una muestra funcional para validar consumos, costos y trazabilidad por colaborador.', 'Esta publicacion de muestra explica como Bocado registra comensales, centros de costo, tipos de comida y eventos de consumo. Sirve para que los usuarios prueben el flujo antes de operar con datos reales.', 'Equipo CCruces', 1, '2026-06-02'),
('demo-bluesales-pipeline', 'ccruces', 'site-ccruces-web', 'bluesales', 'BlueSales demo: pipeline comercial y pedidos', 'bluesales-demo-pipeline', 'Una muestra funcional para probar cuentas, oportunidades, productos y pedidos.', 'Esta publicacion de muestra explica como BlueSales ordena clientes, contactos, oportunidades y pedidos. Sirve para validar reportes comerciales y seguimiento de ventas con informacion de prueba.', 'Equipo CCruces', 1, '2026-06-02')
ON DUPLICATE KEY UPDATE
company_id = VALUES(company_id),
site_id = VALUES(site_id),
service_id = VALUES(service_id),
title = VALUES(title),
slug = VALUES(slug),
excerpt = VALUES(excerpt),
content = VALUES(content),
author = VALUES(author),
is_published = VALUES(is_published),
published_at = VALUES(published_at);

INSERT INTO leads (company_id, site_id, service_id, source, name, email, phone, message, status)
SELECT 'ccruces', 'site-bocado', 'bocado', 'demo', 'Luis Mendoza', 'luis.mendoza@example.com', '+51 900 200 001', 'Desea probar control de comedor para 500 colaboradores.', 'qualified'
WHERE NOT EXISTS (SELECT 1 FROM leads WHERE email = 'luis.mendoza@example.com' AND service_id = 'bocado');

INSERT INTO leads (company_id, site_id, service_id, source, name, email, phone, message, status)
SELECT 'ccruces', 'site-bluesales', 'bluesales', 'demo', 'Mariana Torres', 'mariana.torres@example.com', '+51 900 200 002', 'Busca ordenar pipeline de exportacion de arandano.', 'contacted'
WHERE NOT EXISTS (SELECT 1 FROM leads WHERE email = 'mariana.torres@example.com' AND service_id = 'bluesales');

INSERT INTO bocado_diners (company_id, cost_center_id, employee_code, document_number, full_name, diner_type, status) VALUES
('ccruces', (SELECT id FROM bocado_cost_centers WHERE company_id = 'ccruces' AND code = 'operaciones' LIMIT 1), 'EMP-001', '70000001', 'Ana Rojas', 'employee', 'active'),
('ccruces', (SELECT id FROM bocado_cost_centers WHERE company_id = 'ccruces' AND code = 'packing' LIMIT 1), 'EMP-002', '70000002', 'Miguel Salazar', 'employee', 'active'),
('ccruces', (SELECT id FROM bocado_cost_centers WHERE company_id = 'ccruces' AND code = 'administracion' LIMIT 1), 'EMP-003', '70000003', 'Carla Vega', 'employee', 'active'),
('ccruces', (SELECT id FROM bocado_cost_centers WHERE company_id = 'ccruces' AND code = 'operaciones' LIMIT 1), 'VIS-001', 'TEMP-001', 'Visitante Demo', 'visitor', 'active')
ON DUPLICATE KEY UPDATE
cost_center_id = VALUES(cost_center_id),
document_number = VALUES(document_number),
full_name = VALUES(full_name),
diner_type = VALUES(diner_type),
status = VALUES(status);

INSERT INTO bocado_meal_events (company_id, location_id, diner_id, meal_type_id, served_at, quantity, unit_price, subsidy_amount, total_amount, validation_method, status, created_by)
SELECT 'ccruces', l.id, d.id, m.id, '2026-06-02 08:10:00', 1, m.unit_price, 2.00, m.unit_price - 2.00, 'qr', 'valid', 'demo_bocado'
FROM bocado_locations l, bocado_diners d, bocado_meal_types m
WHERE l.company_id = 'ccruces' AND l.code = 'main-dining-room'
  AND d.company_id = 'ccruces' AND d.employee_code = 'EMP-001'
  AND m.company_id = 'ccruces' AND m.code = 'breakfast'
  AND NOT EXISTS (SELECT 1 FROM bocado_meal_events e WHERE e.diner_id = d.id AND e.meal_type_id = m.id AND e.served_at = '2026-06-02 08:10:00');

INSERT INTO bocado_meal_events (company_id, location_id, diner_id, meal_type_id, served_at, quantity, unit_price, subsidy_amount, total_amount, validation_method, status, created_by)
SELECT 'ccruces', l.id, d.id, m.id, '2026-06-02 13:05:00', 1, m.unit_price, 4.00, m.unit_price - 4.00, 'card', 'valid', 'demo_bocado'
FROM bocado_locations l, bocado_diners d, bocado_meal_types m
WHERE l.company_id = 'ccruces' AND l.code = 'packing-dining-room'
  AND d.company_id = 'ccruces' AND d.employee_code = 'EMP-002'
  AND m.company_id = 'ccruces' AND m.code = 'lunch'
  AND NOT EXISTS (SELECT 1 FROM bocado_meal_events e WHERE e.diner_id = d.id AND e.meal_type_id = m.id AND e.served_at = '2026-06-02 13:05:00');

INSERT INTO bocado_meal_events (company_id, location_id, diner_id, meal_type_id, served_at, quantity, unit_price, subsidy_amount, total_amount, validation_method, status, created_by)
SELECT 'ccruces', l.id, d.id, m.id, '2026-06-02 19:25:00', 1, m.unit_price, 3.00, m.unit_price - 3.00, 'manual', 'pending', 'demo_bocado'
FROM bocado_locations l, bocado_diners d, bocado_meal_types m
WHERE l.company_id = 'ccruces' AND l.code = 'main-dining-room'
  AND d.company_id = 'ccruces' AND d.employee_code = 'EMP-003'
  AND m.company_id = 'ccruces' AND m.code = 'dinner'
  AND NOT EXISTS (SELECT 1 FROM bocado_meal_events e WHERE e.diner_id = d.id AND e.meal_type_id = m.id AND e.served_at = '2026-06-02 19:25:00');

INSERT INTO bluesales_accounts (company_id, customer_id, account_code, name, tax_id, country, segment, status) VALUES
('ccruces', (SELECT id FROM customers WHERE company_id = 'ccruces' AND external_code = 'CUST-AGRO-001' LIMIT 1), 'ACC-001', 'Agro Norte SAC', '20100000001', 'Peru', 'Productor', 'active'),
('ccruces', (SELECT id FROM customers WHERE company_id = 'ccruces' AND external_code = 'CUST-EXPORT-002' LIMIT 1), 'ACC-002', 'Pacific Berry Export', '20100000002', 'Peru', 'Exportador', 'active'),
('ccruces', (SELECT id FROM customers WHERE company_id = 'ccruces' AND external_code = 'CUST-DEMO-003' LIMIT 1), 'ACC-003', 'Cliente Demo Internacional', 'INT-DEMO-003', 'Estados Unidos', 'Comprador internacional', 'prospect')
ON DUPLICATE KEY UPDATE
customer_id = VALUES(customer_id),
name = VALUES(name),
tax_id = VALUES(tax_id),
country = VALUES(country),
segment = VALUES(segment),
status = VALUES(status);

INSERT INTO bluesales_contacts (account_id, full_name, email, phone, position_title, is_primary)
SELECT a.id, 'Rosa Castillo', 'rosa.castillo@agronorte.example', '+51 900 300 001', 'Jefa de compras', 1
FROM bluesales_accounts a
WHERE a.company_id = 'ccruces' AND a.account_code = 'ACC-001'
  AND NOT EXISTS (SELECT 1 FROM bluesales_contacts c WHERE c.account_id = a.id AND c.email = 'rosa.castillo@agronorte.example');

INSERT INTO bluesales_contacts (account_id, full_name, email, phone, position_title, is_primary)
SELECT a.id, 'Daniel Brooks', 'daniel.brooks@demo-international.example', '+1 555 010 0301', 'Category manager', 1
FROM bluesales_accounts a
WHERE a.company_id = 'ccruces' AND a.account_code = 'ACC-003'
  AND NOT EXISTS (SELECT 1 FROM bluesales_contacts c WHERE c.account_id = a.id AND c.email = 'daniel.brooks@demo-international.example');

INSERT INTO bluesales_opportunities (company_id, account_id, owner_username, title, stage, expected_close_date, currency, estimated_amount, probability_percent, notes)
SELECT 'ccruces', a.id, 'demo_bluesales', 'Venta semanal arandano fresco - Agro Norte', 'proposal', '2026-06-15', 'USD', 18400.00, 70, 'Cliente interesado en compra recurrente para temporada alta.'
FROM bluesales_accounts a
WHERE a.company_id = 'ccruces' AND a.account_code = 'ACC-001'
  AND NOT EXISTS (SELECT 1 FROM bluesales_opportunities o WHERE o.account_id = a.id AND o.title = 'Venta semanal arandano fresco - Agro Norte');

INSERT INTO bluesales_opportunities (company_id, account_id, owner_username, title, stage, expected_close_date, currency, estimated_amount, probability_percent, notes)
SELECT 'ccruces', a.id, 'demo_bluesales', 'Exportacion demo premium - Cliente internacional', 'negotiation', '2026-06-30', 'USD', 46800.00, 55, 'Negociacion de precio por volumen y empaque premium.'
FROM bluesales_accounts a
WHERE a.company_id = 'ccruces' AND a.account_code = 'ACC-003'
  AND NOT EXISTS (SELECT 1 FROM bluesales_opportunities o WHERE o.account_id = a.id AND o.title = 'Exportacion demo premium - Cliente internacional');

INSERT INTO bluesales_opportunity_items (opportunity_id, product_id, quantity, unit_price, total_amount)
SELECT o.id, p.id, 4000, 4.60, 18400.00
FROM bluesales_opportunities o, bluesales_products p
WHERE o.title = 'Venta semanal arandano fresco - Agro Norte'
  AND p.company_id = 'ccruces' AND p.sku = 'BLUEBERRY-FRESH-KG'
  AND NOT EXISTS (SELECT 1 FROM bluesales_opportunity_items i WHERE i.opportunity_id = o.id AND i.product_id = p.id);

INSERT INTO bluesales_opportunity_items (opportunity_id, product_id, quantity, unit_price, total_amount)
SELECT o.id, p.id, 9000, 5.20, 46800.00
FROM bluesales_opportunities o, bluesales_products p
WHERE o.title = 'Exportacion demo premium - Cliente internacional'
  AND p.company_id = 'ccruces' AND p.sku = 'BLUEBERRY-PREMIUM-KG'
  AND NOT EXISTS (SELECT 1 FROM bluesales_opportunity_items i WHERE i.opportunity_id = o.id AND i.product_id = p.id);

INSERT INTO bluesales_orders (company_id, opportunity_id, account_id, order_number, order_date, currency, total_amount, status)
SELECT 'ccruces', o.id, a.id, 'BS-DEMO-0001', '2026-06-02', 'USD', 18400.00, 'confirmed'
FROM bluesales_accounts a
LEFT JOIN bluesales_opportunities o ON o.account_id = a.id AND o.title = 'Venta semanal arandano fresco - Agro Norte'
WHERE a.company_id = 'ccruces' AND a.account_code = 'ACC-001'
  AND NOT EXISTS (SELECT 1 FROM bluesales_orders ord WHERE ord.company_id = 'ccruces' AND ord.order_number = 'BS-DEMO-0001');

INSERT INTO bluesales_order_items (order_id, product_id, quantity, unit_price, total_amount)
SELECT ord.id, p.id, 4000, 4.60, 18400.00
FROM bluesales_orders ord, bluesales_products p
WHERE ord.company_id = 'ccruces' AND ord.order_number = 'BS-DEMO-0001'
  AND p.company_id = 'ccruces' AND p.sku = 'BLUEBERRY-FRESH-KG'
  AND NOT EXISTS (SELECT 1 FROM bluesales_order_items i WHERE i.order_id = ord.id AND i.product_id = p.id);

INSERT INTO audit_logs (company_id, service_id, actor_username, action, entity_type, entity_id, metadata, ip_address, user_agent)
SELECT 'ccruces', 'bocado', 'demo_bocado', 'demo.seeded', 'bocado_meal_events', 'sample', JSON_OBJECT('message', 'Datos demo de Bocado cargados'), '127.0.0.1', 'seed.sql'
WHERE NOT EXISTS (SELECT 1 FROM audit_logs WHERE action = 'demo.seeded' AND service_id = 'bocado');

INSERT INTO audit_logs (company_id, service_id, actor_username, action, entity_type, entity_id, metadata, ip_address, user_agent)
SELECT 'ccruces', 'bluesales', 'demo_bluesales', 'demo.seeded', 'bluesales_orders', 'sample', JSON_OBJECT('message', 'Datos demo de BlueSales cargados'), '127.0.0.1', 'seed.sql'
WHERE NOT EXISTS (SELECT 1 FROM audit_logs WHERE action = 'demo.seeded' AND service_id = 'bluesales');
