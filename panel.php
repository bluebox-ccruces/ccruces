<?php

require_once __DIR__ . '/includes/layout.php';

require_login();
$user = current_user();
$username = (string) ($user['username'] ?? '');
$services = user_accessible_services($username);
$globalRoles = user_roles($username);

render_header('Panel', 'panel');
?>
<main class="wrap section">
    <h2>Panel de acceso</h2>
    <p class="lead">Hola, <?= e((string) ($user['name'] ?? $user['username'])) ?>. Desde aquí puedes abrir tus servicios habilitados.</p>

    <div class="panel" style="margin-bottom:1rem;">
        <p><strong>Rol base:</strong> <?= e((string) ($user['role'] ?? 'client')) ?></p>
        <p><strong>Roles activos:</strong> <?= e(!empty($globalRoles) ? implode(', ', $globalRoles) : 'Sin roles asignados') ?></p>
        <p><strong>Estado:</strong> Acceso activo</p>
    </div>

    <?php if (empty($services)): ?>
        <article class="panel">
            <p>No tienes servicios habilitados todav&iacute;a. Solicita acceso al administrador del holding.</p>
        </article>
    <?php endif; ?>

    <div class="grid">
        <?php foreach ($services as $service): ?>
            <?php
            $serviceId = (string) ($service['id'] ?? '');
            $roles = user_roles($username, $serviceId);
            $permissions = user_permissions($username, $serviceId);
            ?>
            <article class="card">
                <h3><?= e((string) ($service['name'] ?? 'Servicio')) ?></h3>
                <p><?= e((string) ($service['tagline'] ?? '')) ?></p>
                <p><strong>Roles:</strong> <?= e(!empty($roles) ? implode(', ', $roles) : 'Sin rol') ?></p>
                <p><strong>Permisos:</strong> <?= e(!empty($permissions) ? implode(', ', $permissions) : 'Sin permisos') ?></p>
                <div class="row-actions service-actions">
                    <a class="btn-mini main" href="<?= e(service_private_entry_url($service)) ?>">Ingresar</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php render_footer(); ?>

