SET NAMES utf8mb4;

INSERT INTO users (username, name, role, password_hash, status) VALUES
('admin', 'Administrador CCruces', 'admin', '$2y$10$PXXNkx483mzaSL7pkNWmv.fHN8STUwDIEJ0dXdx92BECSPLMLkK4.', 1),
('demo', 'Usuario Demo', 'client', '$2y$10$wvwL/NSkoEw.1bLpSAJ8nOtbF9s75iM1lGZQIvJNVFSSYMvP8lcne', 1)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
role = VALUES(role),
password_hash = VALUES(password_hash),
status = VALUES(status);

INSERT INTO services (id, name, tagline, description, logo, demo_url, private_url, status, sort_order) VALUES
('bocado', 'Bocado', 'Control inteligente de comedor', 'Gestiona raciones, elimina fraude y mejora la operación del comedor empresarial en tiempo real.', 'img/Bocado Logo.png', 'demo.php?servicio=bocado', 'https://app.ccruces.com/bocado', 'Demostración abierta + acceso privado', 1),
('conectagh', 'Conecta GH', 'Experiencia digital de RR. HH.', 'Centraliza comunicaciones, documentos y procesos de talento humano en una sola plataforma.', 'img/Logo Gh.png', 'demo.php?servicio=conectagh', 'https://app.ccruces.com/conectagh', 'Demostración abierta + acceso privado', 2),
('gestionocupacional', 'Gestión Ocupacional', 'SST y cumplimiento normativo', 'Monitorea riesgos laborales y automatiza acciones de seguridad y salud en el trabajo.', 'img/Logo gestion ocupacional.png', 'demo.php?servicio=gestionocupacional', 'https://app.ccruces.com/gestion-ocupacional', 'Demostración abierta + acceso privado', 3),
('agrogestor', 'AgroGestor', 'Gestión operativa agrícola', 'Solución para registrar labores de campo, organizar producción y dar trazabilidad operativa a equipos agrícolas.', 'img/Icono BB.png', 'demo.php?servicio=agrogestor', 'https://app.ccruces.com/agrogestor', 'Demostración en evolución + acceso privado', 4),
('bluesalesv2', 'BluesalesV2', 'Venta de arándano', 'Plataforma comercial para gestionar ventas de arándano, clientes, pedidos y trazabilidad del proceso de comercialización.', 'img/Icono BB.png', 'demo.php?servicio=bluesalesv2', 'https://app.ccruces.com/bluesalesv2', 'Demostración abierta + acceso privado', 5)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
tagline = VALUES(tagline),
description = VALUES(description),
logo = VALUES(logo),
demo_url = VALUES(demo_url),
private_url = VALUES(private_url),
status = VALUES(status),
sort_order = VALUES(sort_order);

INSERT INTO posts (id, title, excerpt, content, author, published_at) VALUES
('post-5', 'Servicios activos del holding: Bocado, Conecta GH, Gestión Ocupacional, AgroGestor y BluesalesV2', 'Consolidamos nuestros productos estratégicos en una sola plataforma para facilitar la evaluación, el acceso y la operación de cada servicio.', 'En CCruces Holding centralizamos cinco soluciones clave para resolver procesos críticos en operación, talento humano, seguridad ocupacional, gestión agrícola y comercialización.

Cada servicio cuenta con un enfoque práctico: modo demostración para evaluación funcional y acceso privado para uso real con usuarios autorizados. Esto permite reducir tiempos de implementación y mejorar la adopción por parte de los equipos.

Nuestros servicios activos son:
- Bocado: control inteligente de comedor y consumo.
- Conecta GH: experiencia digital para gestión de talento humano.
- Gestión Ocupacional: soporte integral para SST y cumplimiento normativo.
- AgroGestor: operación agrícola con trazabilidad de campo.
- BluesalesV2: gestión comercial para venta de arándano.

Esta arquitectura unificada fortalece la trazabilidad, la calidad de la información y la velocidad de toma de decisiones para cada unidad de negocio.', 'Cristhian Cruces', '2026-05-19')
ON DUPLICATE KEY UPDATE
title = VALUES(title),
excerpt = VALUES(excerpt),
content = VALUES(content),
author = VALUES(author),
published_at = VALUES(published_at);
