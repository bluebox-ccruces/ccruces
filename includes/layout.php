<?php

require_once __DIR__ . '/bootstrap.php';

function asset_url(string $path): string
{
    $absolutePath = dirname(__DIR__) . '/' . ltrim($path, '/');
    $version = is_file($absolutePath) ? (string) filemtime($absolutePath) : (string) time();
    return app_url($path) . '?v=' . rawurlencode($version);
}

function render_header(string $title, string $active = ''): void
{
    if (!headers_sent()) {
        apply_security_headers();
        header('Content-Type: text/html; charset=UTF-8');
    }

    $user = current_user();
    $flash = flash();
    ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title><?= e($title) ?> | ccruces.com</title>
    <meta name="description" content="CCruces Holding: blog personal, demos de servicios y acceso privado a plataformas empresariales." />
    <link rel="icon" type="image/png" href="<?= e(app_url('img/Icono BB.png')) ?>" />
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/site.css')) ?>" />
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(app_url('index.php')) ?>">CCruces<span>Holding</span></a>
    <button class="menu-btn" type="button" aria-label="Abrir menu" data-menu-btn>Menu</button>
    <nav class="nav" data-menu>
        <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="<?= e(app_url('index.php')) ?>">Inicio</a>
        <a class="<?= $active === 'services' ? 'is-active' : '' ?>" href="<?= e(app_url('servicios.php')) ?>">Servicios</a>
        <a class="<?= $active === 'blog' ? 'is-active' : '' ?>" href="<?= e(app_url('blog.php')) ?>">Blog</a>
        <?php if ($user): ?>
            <a class="<?= $active === 'panel' ? 'is-active' : '' ?>" href="<?= e(app_url('panel.php')) ?>">Panel</a>
            <?php if (is_admin()): ?>
                <a class="<?= $active === 'admin' ? 'is-active' : '' ?>" href="<?= e(app_url('admin.php')) ?>">Admin</a>
            <?php endif; ?>
            <a href="<?= e(app_url('logout.php')) ?>">Salir</a>
        <?php else: ?>
            <a class="<?= $active === 'login' ? 'is-active' : '' ?>" href="<?= e(app_url('login.php')) ?>">Ingresar</a>
            <a class="<?= $active === 'register' ? 'is-active' : '' ?>" href="<?= e(app_url('register.php')) ?>">Crear Cuenta</a>
        <?php endif; ?>
    </nav>
    <button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema" aria-pressed="false">
        <span class="theme-toggle__icon" data-theme-icon aria-hidden="true">&#9790;</span>
    </button>
</header>
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
<?php
}

function render_footer(): void
{
    ?>
<footer class="footer">
    <p>&copy; <?= date('Y') ?> ccruces.com | Holding digital de productos y servicios.</p>
</footer>
<script src="<?= e(asset_url('assets/js/site.js')) ?>" defer></script>
</body>
</html>
<?php
}
