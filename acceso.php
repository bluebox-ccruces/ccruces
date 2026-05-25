<?php

require_once __DIR__ . '/includes/layout.php';

$serviceId = (string) ($_GET['servicio'] ?? '');
$service = service_by_id($serviceId);

if (!$service) {
    set_flash('error', 'Servicio no encontrado.');
    header('Location: ' . app_url('servicios.php'));
    exit;
}

require_login();
$privateEntryUrl = service_private_entry_url($service);

render_header('Acceso a servicio', 'services');
?>
<main class="wrap section">
    <h2><?= e((string) ($service['name'] ?? 'Servicio')) ?></h2>
    <p class="lead"><?= e((string) ($service['description'] ?? '')) ?></p>

    <article class="panel">
        <p><strong>Modo actual:</strong> Acceso autenticado</p>
        <p>
            Este enlace debe apuntar al entorno productivo de tu servicio.
            Ahora esta configurado como: <code><?= e($privateEntryUrl) ?></code>
        </p>
        <div class="row-actions">
            <a class="btn-mini main" href="<?= e($privateEntryUrl) ?>" target="_blank" rel="noopener noreferrer">Abrir plataforma</a>
            <a class="btn-mini" href="<?= e(app_url('servicios.php')) ?>">Volver a servicios</a>
        </div>
    </article>
</main>
<?php render_footer(); ?>

