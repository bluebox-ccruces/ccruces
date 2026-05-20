<?php

function service_modal_details(array $service): array
{
    $id = (string) ($service['id'] ?? '');

    $defaults = [
        'summary' => (string) ($service['description'] ?? ''),
        'content' => 'Este servicio forma parte del ecosistema CCruces Holding y está orientado a mejorar productividad, control y trazabilidad en procesos clave.',
        'benefits' => [
            'Implementación rápida para equipos operativos.',
            'Visibilidad centralizada de indicadores clave.',
            'Escalabilidad por unidades de negocio y sedes.',
        ],
        'financial_benefits' => [
            'Mejor asignación de costos por centro de responsabilidad.',
            'Menor reproceso administrativo por datos dispersos.',
            'Información más confiable para presupuestos y cierres.',
        ],
        'roi_note' => 'Impacto esperado: control más preciso de costos, mejor disciplina operativa y decisiones financieras con menor incertidumbre.',
        'images' => [
            (string) ($service['logo'] ?? 'img/Icono BB.png'),
        ],
    ];

    $catalog = [
        'bocado' => [
            'summary' => 'Bocado digitaliza el control de comedor para eliminar consumos no autorizados y convertir la operación en datos auditables.',
            'content' => 'Valida identidad del colaborador, registra consumos en tiempo real y consolida reportes por sede, turno y centro de costo para administración y finanzas.',
            'benefits' => [
                'Control diario de consumo por usuario, sede y horario.',
                'Trazabilidad completa para auditoría interna y externa.',
                'Reducción de tiempos de conciliación entre operación y administración.',
            ],
            'financial_benefits' => [
                'Disminución de pérdidas por consumo no validado y duplicidades.',
                'Distribución exacta del gasto de comedor por centro de costo.',
                'Mejor negociación con proveedores al contar con demanda histórica real.',
            ],
            'roi_note' => 'Rentabilidad: reduce fugas silenciosas del gasto alimentario y permite recuperar margen operativo en ciclos cortos.',
            'images' => [
                'img/Bocado Logo.png',
            ],
        ],
        'conectagh' => [
            'summary' => 'Conecta GH concentra procesos de talento humano en una sola experiencia digital para elevar productividad y orden documental.',
            'content' => 'Integra solicitudes, documentos, flujos internos y comunicación con colaboradores para reducir fricción operativa y acelerar tiempos de respuesta.',
            'benefits' => [
                'Procesos de RR. HH. estandarizados y medibles.',
                'Documentación centralizada con acceso por perfil.',
                'Mayor velocidad en atención de requerimientos internos.',
            ],
            'financial_benefits' => [
                'Menor costo administrativo por automatización de tareas repetitivas.',
                'Reducción de contingencias por documentos incompletos o fuera de vigencia.',
                'Mejor control del costo por colaborador en procesos de gestión humana.',
            ],
            'roi_note' => 'Rentabilidad: convierte horas administrativas en capacidad productiva y reduce costos ocultos por desorden documental.',
            'images' => [
                'img/Logo Gh.png',
                'img/Imagen app Conecta Gh.jpeg',
            ],
        ],
        'gestionocupacional' => [
            'summary' => 'Gestión Ocupacional fortalece SST con seguimiento continuo de riesgos, acciones preventivas y evidencia de cumplimiento.',
            'content' => 'Organiza evaluaciones, alertas, cronogramas y evidencia operativa en un flujo claro para supervisión, cumplimiento y mejora continua.',
            'benefits' => [
                'Seguimiento estructurado de planes de seguridad y salud.',
                'Alertas oportunas para mitigar incidentes recurrentes.',
                'Trazabilidad documental para auditorías regulatorias.',
            ],
            'financial_benefits' => [
                'Reducción de costos por incidentes, paralizaciones y sanciones evitables.',
                'Menor gasto legal y administrativo por evidencia incompleta.',
                'Mejor previsión de CAPEX y OPEX en programas de prevención.',
            ],
            'roi_note' => 'Rentabilidad: protege continuidad operativa y reduce la volatilidad financiera asociada a eventos de riesgo.',
            'images' => [
                'img/Logo gestion ocupacional.png',
                'img/app gestion ocupacional.png',
            ],
        ],
        'agrogestor' => [
            'summary' => 'AgroGestor ordena la operación agrícola en campo con trazabilidad por lote, cuadrilla y jornada.',
            'content' => 'Registra labores en tiempo real, controla avances diarios y consolida indicadores para planificar mejor recursos, rendimiento y ejecución.',
            'benefits' => [
                'Control operativo por lote, labor y responsable.',
                'Información diaria para tomar decisiones en campaña.',
                'Coordinación fluida entre campo, planificación y administración.',
            ],
            'financial_benefits' => [
                'Control más fino del costo por hectárea y por labor.',
                'Reducción de desviaciones de presupuesto durante campaña.',
                'Mejor rentabilidad por asignación eficiente de mano de obra y recursos.',
            ],
            'roi_note' => 'Rentabilidad: mejora el margen agrícola al reducir desperdicio operativo y elevar precisión de costos en campo.',
            'images' => [
                'img/Logo AgroGestor.png',
            ],
        ],
        'bluesalesv2' => [
            'summary' => 'BluesalesV2 impulsa la gestión comercial de arándano con control del pipeline, pedidos y cumplimiento de compromisos.',
            'content' => 'Centraliza oportunidades, acuerdos y pedidos para conectar la gestión comercial con la operación y sostener crecimiento con disciplina.',
            'benefits' => [
                'Seguimiento comercial en tiempo real por cliente y etapa.',
                'Priorización de oportunidades con mayor probabilidad de cierre.',
                'Visibilidad del desempeño comercial por canal y ejecutivo.',
            ],
            'financial_benefits' => [
                'Mayor previsibilidad de ingresos y flujo de caja comercial.',
                'Mejor gestión de descuentos y condiciones para proteger margen.',
                'Reducción de pérdidas por errores de seguimiento en pedidos.',
            ],
            'roi_note' => 'Rentabilidad: mejora conversión comercial y control de margen para sostener crecimiento con ventas más rentables.',
            'images' => [
                'img/Icono BB.png',
            ],
        ],
    ];

    $selected = $catalog[$id] ?? [];
    $payload = array_merge($defaults, $selected);
    $payload['benefits'] = array_values(array_filter((array) ($payload['benefits'] ?? []), static fn($item): bool => is_string($item) && trim($item) !== ''));
    $payload['financial_benefits'] = array_values(array_filter((array) ($payload['financial_benefits'] ?? []), static fn($item): bool => is_string($item) && trim($item) !== ''));
    $payload['images'] = array_values(array_filter((array) ($payload['images'] ?? []), static fn($item): bool => is_string($item) && trim($item) !== ''));

    return $payload;
}

function service_modal_payload(array $service): array
{
    $details = service_modal_details($service);
    $demoUrlRaw = (string) ($service['demo_url'] ?? '');
    $privateUrlRaw = (string) ($service['private_url'] ?? '');
    $demoUrl = str_starts_with($demoUrlRaw, 'http') ? $demoUrlRaw : app_url($demoUrlRaw);
    $privateUrl = str_starts_with($privateUrlRaw, 'http') ? $privateUrlRaw : app_url($privateUrlRaw);

    $images = [];
    foreach ((array) ($details['images'] ?? []) as $img) {
        $path = (string) $img;
        $images[] = [
            'src' => app_url($path),
            'alt' => (string) ($service['name'] ?? 'Servicio'),
        ];
    }

    return [
        'id' => (string) ($service['id'] ?? ''),
        'name' => (string) ($service['name'] ?? 'Servicio'),
        'tagline' => (string) ($service['tagline'] ?? ''),
        'description' => (string) ($service['description'] ?? ''),
        'summary' => (string) ($details['summary'] ?? ''),
        'content' => (string) ($details['content'] ?? ''),
        'benefits' => (array) ($details['benefits'] ?? []),
        'financial_benefits' => (array) ($details['financial_benefits'] ?? []),
        'roi_note' => (string) ($details['roi_note'] ?? ''),
        'images' => $images,
        'demo_url' => $demoUrl,
        'private_url' => $privateUrl,
    ];
}

function render_service_modal_shell(): void
{
    ?>
    <div class="service-modal" data-service-modal hidden>
        <div class="service-modal__backdrop" data-service-close></div>
        <article class="service-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="service-modal-title">
            <button class="service-modal__close" type="button" aria-label="Cerrar" data-service-close>&times;</button>
            <header class="service-modal__header">
                <p class="service-modal__kicker" data-service-tagline></p>
                <h3 id="service-modal-title" data-service-name></h3>
                <p class="service-modal__summary" data-service-summary></p>
            </header>
            <div class="service-modal__content">
                <div class="service-modal__text">
                    <h4>Resumen del proyecto</h4>
                    <p data-service-content></p>
                    <h4>Beneficios operativos</h4>
                    <ul data-service-benefits></ul>
                    <h4>Beneficios contables y de rentabilidad</h4>
                    <ul data-service-financial-benefits></ul>
                    <p class="service-modal__roi" data-service-roi-note></p>
                    <div class="service-modal__actions">
                        <a class="btn-mini" href="#" data-service-demo>Ver demo</a>
                        <a class="btn-mini main" href="#" data-service-private>Acceso privado</a>
                    </div>
                </div>
                <div class="service-modal__media" data-service-images></div>
            </div>
        </article>
    </div>
    <?php
}
