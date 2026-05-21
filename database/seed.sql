SET NAMES utf8mb4;

INSERT INTO users (username, name, role, password_hash, status) VALUES
('ccruces', 'Administrador CCruces', 'admin', '$2y$10$bnOVcnWTaKJiDzRVVszSi.UemM1kmr93aWCc7Nxd7mTQ/j/JD3Mry', 1)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
role = VALUES(role),
password_hash = VALUES(password_hash),
status = VALUES(status);

INSERT INTO services (id, name, tagline, description, logo, demo_url, private_url, status, sort_order) VALUES
('bocado', 'Bocado', 'Control inteligente de comedor', 'Gestiona raciones, elimina fraude y mejora la operación del comedor empresarial en tiempo real.', 'img/Bocado Logo.png', 'demo.php?servicio=bocado', 'https://app.ccruces.com/bocado', 'Demostración abierta + acceso privado', 1),
('conectagh', 'Conecta GH', 'Experiencia digital de RR. HH.', 'Centraliza comunicaciones, documentos y procesos de talento humano en una sola plataforma.', 'img/Logo Gh.png', 'demo.php?servicio=conectagh', 'https://rrhh.bluebox-enterprise.com/login', 'Demostración abierta + acceso privado', 2),
('gestionocupacional', 'Gestión Ocupacional', 'SST y cumplimiento normativo', 'Monitorea riesgos laborales y automatiza acciones de seguridad y salud en el trabajo.', 'img/Logo gestion ocupacional.png', 'demo.php?servicio=gestionocupacional', 'https://topico.bluebox-enterprise.com/admin/login', 'Demostración abierta + acceso privado', 3),
('agrogestor', 'AgroGestor', 'Gestión operativa agrícola', 'Solución para registrar labores de campo, organizar producción y dar trazabilidad operativa a equipos agrícolas.', 'img/Logo AgroGestor.png', 'demo.php?servicio=agrogestor', 'https://tareos.blueboxsolutions.tech/admin/login', 'Demostración en evolución + acceso privado', 4),
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
('post-bocado-20260519', 'Bocado: de gasto invisible a control rentable en comedores empresariales', 'Cuando el consumo no se controla en tiempo real, el comedor se convierte en una fuga silenciosa. Bocado cambia esa historia con trazabilidad y datos para rentabilidad.', 'En muchas empresas, el comedor se gestiona como un costo fijo difícil de discutir. Sin embargo, detrás de esa aparente normalidad suelen existir fugas por consumos duplicados, validaciones débiles y poca claridad por centro de costo.

Bocado digitaliza el proceso con validación de identidad, registro inmediato y reportes accionables para operación y finanzas. El resultado no es solo orden operativo: es control presupuestal, capacidad de negociación con proveedores y mejor lectura del costo real por colaborador.

Puntos de debate para equipos directivos:
- ¿Tu empresa conoce con precisión cuánto gasta por turno, sede y área?
- ¿Qué porcentaje del gasto de comedor hoy no se puede auditar con evidencia?
- ¿Cómo cambiaría tu margen operativo si reduces estas fugas en el siguiente trimestre?

Bocado propone una idea simple: cada consumo debe convertirse en un dato confiable para tomar mejores decisiones empresariales.', 'Carlos Cruces', '2026-05-19'),
('post-conectagh-20260519', 'Conecta GH: productividad en RR. HH. sin sacrificar control documental', 'La gestión humana pierde valor cuando vive entre correos, hojas sueltas y tiempos de respuesta largos. Conecta GH integra procesos para ganar velocidad y control.', 'Cuando RR. HH. trabaja con procesos dispersos, el impacto económico aparece en forma de horas improductivas, errores repetidos y riesgos documentales. Conecta GH centraliza solicitudes, comunicación y archivos críticos en una experiencia única.

Su valor no está solo en la interfaz: está en la reducción de reprocesos administrativos, la trazabilidad por colaborador y la mejora del servicio interno hacia toda la organización.

Puntos de debate para líderes de talento y finanzas:
- ¿Cuánto cuesta al mes resolver tareas repetitivas que podrían automatizarse?
- ¿Qué riesgos asume la empresa por no tener documentos actualizados y accesibles?
- ¿Cuánto valor recupera una organización cuando RR. HH. pasa de reaccionar a planificar?

Conecta GH convierte la operación de personas en una plataforma de datos y decisiones, no solo en un centro de trámite.', 'Carlos Cruces', '2026-05-19'),
('post-gestionocupacional-20260519', 'Gestión Ocupacional: prevención que protege personas y resultados financieros', 'La SST no es un requisito aislado: es una palanca para continuidad operativa, reducción de contingencias y sostenibilidad del negocio.', 'Toda empresa entiende que un incidente laboral duele en lo humano, pero también golpea en lo financiero: paralizaciones, sanciones, litigios y desgaste operativo. Gestión Ocupacional permite monitorear riesgos, ejecutar planes y sostener evidencia de cumplimiento.

Esto fortalece la cultura preventiva y al mismo tiempo reduce la volatilidad de costos asociados a eventos críticos. La clave es anticipar y no solo reaccionar.

Puntos de debate para comités de gerencia:
- ¿Tu empresa mide el costo real de los incidentes evitables?
- ¿Qué tan preparada está para responder una auditoría exigente en cualquier momento?
- ¿Cuánto impacto tendría bajar el índice de eventos críticos durante un año completo?

Gestión Ocupacional transforma la prevención en una estrategia concreta de estabilidad operativa y rentabilidad.', 'Carlos Cruces', '2026-05-19'),
('post-agrogestor-20260519', 'AgroGestor: trazabilidad de campo para mejorar costo por hectárea', 'En agro, decidir tarde cuesta caro. AgroGestor lleva control diario de labores y rendimiento para administrar mejor recursos, tiempos y márgenes.', 'El reto en operaciones agrícolas no es solo producir más: es producir con control. Sin visibilidad diaria, las desviaciones de mano de obra, tiempo y recursos se detectan demasiado tarde.

AgroGestor captura actividades por lote y cuadrilla para dar trazabilidad real al avance de campaña. Esa información permite corregir sobre la marcha y cerrar con mejor precisión de costos.

Puntos de debate para operaciones agrícolas:
- ¿Tu costo por hectárea se calcula con datos del día o con cierres tardíos?
- ¿Qué decisiones podrías mejorar con visibilidad por lote en tiempo real?
- ¿Dónde se pierde margen hoy: en planificación, ejecución o control?

AgroGestor propone una ventaja competitiva clara: convertir cada jornada en una fuente de decisiones rentables.', 'Carlos Cruces', '2026-05-19'),
('post-bluesalesv2-20260519', 'BluesalesV2: vender arándano con estrategia, trazabilidad y margen', 'No basta con vender más; hay que vender mejor. BluesalesV2 conecta gestión comercial con disciplina financiera para proteger margen.', 'Las áreas comerciales suelen tener presión por cerrar, pero no siempre cuentan con una visión completa del impacto de cada decisión en margen y flujo de caja. BluesalesV2 ordena oportunidades, compromisos y pedidos con seguimiento estructurado.

Al centralizar el proceso comercial, la empresa gana previsibilidad de ingresos y mayor control de condiciones negociadas, evitando pérdidas por desorden o seguimiento débil.

Puntos de debate para dirección comercial y financiera:
- ¿Tu pipeline actual anticipa ingresos con confianza o solo acumula contactos?
- ¿Qué porcentaje de oportunidades se pierde por falta de seguimiento riguroso?
- ¿Cómo cambia la rentabilidad cuando se controla mejor descuento, volumen y cumplimiento?

BluesalesV2 impulsa una cultura comercial donde crecimiento y rentabilidad avanzan juntos.', 'Carlos Cruces', '2026-05-19')
ON DUPLICATE KEY UPDATE
title = VALUES(title),
excerpt = VALUES(excerpt),
content = VALUES(content),
author = VALUES(author),
published_at = VALUES(published_at);
