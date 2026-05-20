<?php

require_once __DIR__ . '/includes/layout.php';

require_login();
$user = current_user();
$services = services_all();

render_header('Panel', 'panel');
?>
<main class="wrap section">
    <h2>Panel de acceso</h2>
    <p class="lead">Hola, <?= e((string) ($user['name'] ?? $user['username'])) ?>. Desde aquí puedes abrir tus servicios habilitados.</p>

    <div class="panel" style="margin-bottom:1rem;">
        <p><strong>Rol:</strong> <?= e((string) ($user['role'] ?? 'client')) ?></p>
        <p><strong>Estado:</strong> Acceso activo</p>
    </div>

    <div class="grid">
        <?php foreach ($services as $service): ?>
            <article class="card">
                <h3><?= e((string) ($service['name'] ?? 'Servicio')) ?></h3>
                <p><?= e((string) ($service['tagline'] ?? '')) ?></p>
                <div class="row-actions">
                    <a class="btn-mini" href="<?= e(app_url((string) ($service['demo_url'] ?? '#'))) ?>">Demo</a>
                    <a class="btn-mini main" href="<?= e(app_url('acceso.php?servicio=' . urlencode((string) ($service['id'] ?? '')))) ?>">Abrir acceso</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php render_footer(); ?>

