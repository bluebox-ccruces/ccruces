<?php

require_once __DIR__ . '/includes/layout.php';

$errors = [];
$next = (string) ($_GET['next'] ?? 'panel.php');
$allowedNext = ['panel.php', 'servicios.php', 'index.php'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        $errors[] = 'Solicitud inválida. Intenta nuevamente.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $next = (string) ($_POST['next'] ?? 'panel.php');
        if (!in_array($next, $allowedNext, true)) {
            $next = 'panel.php';
        }

        $user = find_user($username);
        if ($user && isset($user['password_hash']) && password_verify($password, (string) $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user['username'];
            set_flash('success', 'Bienvenido, ' . ($user['name'] ?? $user['username']) . '.');
            header('Location: ' . app_url($next));
            exit;
        }

        $errors[] = 'Usuario o contraseña inválidos.';
    }
}

if (!in_array($next, $allowedNext, true)) {
    $next = 'panel.php';
}

render_header('Ingresar', 'login');
?>
<main class="auth-box">
    <section class="form-card">
        <h2>Acceso a clientes y administración</h2>
        <p class="lead">Usa tus credenciales para abrir los servicios privados y, si eres admin, gestionar el blog.</p>

        <?php foreach ($errors as $error): ?>
            <div class="flash flash-error" style="width:100%;margin:0 0 .8rem;"><?= e($error) ?></div>
        <?php endforeach; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
            <input type="hidden" name="next" value="<?= e($next) ?>" />
            <label>
                Usuario
                <input type="text" name="username" required />
            </label>
            <label>
                Contraseña
                <input type="password" name="password" required />
            </label>
            <button class="btn-submit" type="submit">Ingresar</button>
        </form>

        <p style="margin-top:1rem;color:#4a5873;"><strong>Demo inicial:</strong> usuario <code>demo</code> / contraseña <code>Demo@2026!</code></p>
    </section>
</main>
<?php render_footer(); ?>

