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
$user = current_user();
$username = (string) ($user['username'] ?? '');
if (!user_has_service_access($username, $serviceId)) {
    set_flash('error', 'No tienes acceso habilitado para este servicio.');
    header('Location: ' . app_url('panel.php'));
    exit;
}

$privateEntryUrl = service_private_platform_url($service);

render_header('Acceso a servicio', 'services');
?>
<main class="wrap section">
    <h2><?= e((string) ($service['name'] ?? 'Servicio')) ?></h2>
    <p class="lead"><?= e((string) ($service['description'] ?? '')) ?></p>

    <article class="panel">
        <p><strong>Modo actual:</strong> Acceso autenticado</p>
        <?php if ($privateEntryUrl !== ''): ?>
            <p>Tu usuario tiene acceso activo a este servicio.</p>
            <p>Destino configurado: <code><?= e($privateEntryUrl) ?></code></p>
        <?php else: ?>
            <p>Tu usuario tiene acceso activo, pero el entorno privado todav&iacute;a no tiene URL configurada.</p>
        <?php endif; ?>
        <div class="row-actions">
            <?php if ($privateEntryUrl !== ''): ?>
                <a class="btn-mini main" href="<?= e($privateEntryUrl) ?>" target="_blank" rel="noopener noreferrer">Abrir plataforma</a>
            <?php endif; ?>
            <a class="btn-mini" href="<?= e(app_url('servicios.php')) ?>">Volver a servicios</a>
        </div>
    </article>
</main>
<?php render_footer(); ?>
