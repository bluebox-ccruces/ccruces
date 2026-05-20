<?php

require_once __DIR__ . '/includes/layout.php';

$serviceId = (string) ($_GET['servicio'] ?? '');
$service = service_by_id($serviceId);

if (!$service) {
    set_flash('error', 'Demo no encontrada.');
    header('Location: ' . app_url('servicios.php'));
    exit;
}

render_header('Demo de servicio', 'services');
?>
<main class="wrap section">
    <h2>Demo: <?= e((string) ($service['name'] ?? 'Servicio')) ?></h2>
    <p class="lead"><?= e((string) ($service['tagline'] ?? '')) ?></p>

    <article class="card" style="display:grid;gap:1rem;">
        <img src="<?= e(app_url((string) ($service['logo'] ?? 'img/Icono BB.png'))) ?>" alt="<?= e((string) ($service['name'] ?? 'Servicio')) ?>" />
        <p><?= e((string) ($service['description'] ?? '')) ?></p>
        <div class="panel">
            <h3 style="margin-top:0;">Qué puede validar un prospecto en demo</h3>
            <ul style="margin:0;padding-left:1rem;color:#4a5873;">
                <li>Flujo principal de la solución</li>
                <li>Experiencia de usuario y navegación</li>
                <li>Ajuste funcional al proceso de negocio</li>
            </ul>
        </div>
        <div class="row-actions">
            <a class="btn-mini main" href="<?= e(app_url('login.php?next=panel.php')) ?>">Ingresar para acceso privado</a>
            <a class="btn-mini" href="<?= e(app_url('servicios.php')) ?>">Volver al catálogo</a>
        </div>
    </article>
</main>
<?php render_footer(); ?>

