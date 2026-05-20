<?php

require_once __DIR__ . '/includes/layout.php';

$services = services_all();
render_header('Servicios', 'services');
?>
<main class="wrap section">
    <h2>Catálogo de servicios</h2>
    <p class="lead">Cada solución tiene demo abierta y ruta de acceso privado con autenticación.</p>

    <div class="grid">
        <?php foreach ($services as $service): ?>
            <?php
            $modalPayload = service_modal_payload($service);
            $modalJson = json_encode($modalPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            ?>
            <article
                class="card service-card"
                role="button"
                tabindex="0"
                data-service-card
                data-service-payload="<?= e($modalJson ?: '{}') ?>"
                aria-label="Ver detalles de <?= e((string) ($service['name'] ?? 'Servicio')) ?>"
            >
                <img src="<?= e(app_url((string) ($service['logo'] ?? 'img/Icono BB.png'))) ?>" alt="<?= e((string) ($service['name'] ?? 'Servicio')) ?>" />
                <h3><?= e((string) ($service['name'] ?? 'Servicio')) ?></h3>
                <p><strong><?= e((string) ($service['tagline'] ?? '')) ?></strong></p>
                <p><?= e((string) ($service['description'] ?? '')) ?></p>
                <div class="row-actions">
                    <a class="btn-mini" href="<?= e((string) ($modalPayload['demo_url'] ?? '#')) ?>">Modo demo</a>
                    <a class="btn-mini main" href="<?= e(app_url('acceso.php?servicio=' . urlencode((string) ($service['id'] ?? '')))) ?>">Entrar con usuario</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php render_service_modal_shell(); ?>
<?php render_footer(); ?>

