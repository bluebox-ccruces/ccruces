<?php

function service_video_embed_url(string $rawUrl): string
{
    $rawUrl = trim($rawUrl);
    if ($rawUrl === '') {
        return '';
    }

    if (preg_match('~<iframe[^>]+src=["\']([^"\']+)["\']~i', $rawUrl, $iframeMatch)) {
        $rawUrl = trim((string) ($iframeMatch[1] ?? ''));
    }

    $parts = @parse_url($rawUrl);
    if (!is_array($parts)) {
        return $rawUrl;
    }

    $host = strtolower((string) ($parts['host'] ?? ''));
    $path = trim((string) ($parts['path'] ?? ''), '/');
    $query = (string) ($parts['query'] ?? '');

    $videoId = '';
    if ($host !== '') {
        $host = preg_replace('/^(www|m|music)\./i', '', $host) ?? $host;
    }

    if ($host === 'youtu.be') {
        $videoId = explode('/', $path)[0] ?? '';
    } elseif ($host === 'youtube.com' || $host === 'youtube-nocookie.com') {
        if ($path !== '') {
            $segments = explode('/', $path);
            $first = strtolower((string) ($segments[0] ?? ''));
            $second = (string) ($segments[1] ?? '');
            if (in_array($first, ['embed', 'shorts', 'live'], true)) {
                $videoId = $second;
            }
        }

        if ($videoId === '' && $query !== '') {
            parse_str($query, $queryParams);
            $videoId = (string) ($queryParams['v'] ?? '');
        }
    }

    if (preg_match('/^[A-Za-z0-9_-]{6,}$/', $videoId) === 1) {
        return 'https://www.youtube.com/embed/' . $videoId . '?rel=0';
    }

    if ($host === 'youtu.be' || $host === 'youtube.com' || $host === 'youtube-nocookie.com') {
        return '';
    }

    return $rawUrl;
}

function service_private_entry_url(array $service): string
{
    $privateUrlRaw = trim((string) ($service['private_url'] ?? ''));
    if ($privateUrlRaw !== '') {
        return str_starts_with($privateUrlRaw, 'http') ? $privateUrlRaw : app_url($privateUrlRaw);
    }

    return app_url('acceso.php?servicio=' . urlencode((string) ($service['id'] ?? '')));
}

function service_modal_details(array $service): array
{
    $splitLines = static function (string $text): array {
        $lines = preg_split('/\r\n|\r|\n/', trim($text)) ?: [];
        return array_values(array_filter(array_map('trim', $lines), static fn(string $line): bool => $line !== ''));
    };

    $summary = trim((string) ($service['summary'] ?? ''));
    $content = trim((string) ($service['content'] ?? ''));
    $benefitsText = trim((string) ($service['benefits'] ?? ''));
    $financialBenefitsText = trim((string) ($service['financial_benefits'] ?? ''));
    $roiNote = trim((string) ($service['roi_note'] ?? ''));

    $payload = [
        'summary' => $summary !== '' ? $summary : (string) ($service['description'] ?? ''),
        'content' => $content !== '' ? $content : 'Este servicio forma parte del ecosistema CCruces Holding y está orientado a mejorar productividad, control y trazabilidad en procesos clave.',
        'video_url' => trim((string) ($service['video_url'] ?? '')),
        'benefits' => $benefitsText !== '' ? $splitLines($benefitsText) : [
            'Implementación rápida para equipos operativos.',
            'Visibilidad centralizada de indicadores clave.',
            'Escalabilidad por unidades de negocio y sedes.',
        ],
        'financial_benefits' => $financialBenefitsText !== '' ? $splitLines($financialBenefitsText) : [
            'Mejor asignación de costos por centro de responsabilidad.',
            'Menor reproceso administrativo por datos dispersos.',
            'Información más confiable para presupuestos y cierres.',
        ],
        'roi_note' => $roiNote !== '' ? $roiNote : 'Impacto esperado: control más preciso de costos, mejor disciplina operativa y decisiones financieras con menor incertidumbre.',
        'images' => [
            (string) ($service['logo'] ?? 'img/Icono BB.png'),
        ],
    ];

    $payload['benefits'] = array_values(array_filter((array) ($payload['benefits'] ?? []), static fn($item): bool => is_string($item) && trim($item) !== ''));
    $payload['financial_benefits'] = array_values(array_filter((array) ($payload['financial_benefits'] ?? []), static fn($item): bool => is_string($item) && trim($item) !== ''));
    $payload['images'] = array_values(array_filter((array) ($payload['images'] ?? []), static fn($item): bool => is_string($item) && trim($item) !== ''));
    $payload['video_url'] = service_video_embed_url((string) ($payload['video_url'] ?? ''));

    return $payload;
}
function service_modal_payload(array $service): array
{
    $details = service_modal_details($service);
    $demoUrlRaw = (string) ($service['demo_url'] ?? '');
    $demoUrl = str_starts_with($demoUrlRaw, 'http') ? $demoUrlRaw : app_url($demoUrlRaw);
    $privateUrl = service_private_entry_url($service);
    $videoUrlRaw = (string) ($details['video_url'] ?? '');
    if ($videoUrlRaw === '' && str_starts_with($demoUrlRaw, 'http')) {
        $videoUrlRaw = $demoUrlRaw;
    }
    $videoEmbedUrl = service_video_embed_url($videoUrlRaw);

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
        'video_url' => $videoEmbedUrl,
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
                        <a class="btn-mini main" href="#" data-service-private>Ingresar</a>
                    </div>
                </div>
                <div class="service-modal__media">
                    <div class="service-modal__video-wrap">
                        <iframe
                            title="Video del servicio"
                            data-service-video
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                        <p class="service-modal__video-empty" data-service-video-empty hidden>
                            Este servicio aÃºn no tiene video cargado.
                        </p>
                    </div>
                    <div data-service-images></div>
                </div>
            </div>
        </article>
    </div>
    <?php
}

