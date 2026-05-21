<?php

require_once __DIR__ . '/includes/layout.php';

$errors = [];
$next = (string) ($_GET['next'] ?? 'index.php');
$allowedNext = ['index.php', 'panel.php', 'servicios.php', 'blog.php', 'admin.php', 'acceso.php'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        $errors[] = 'Solicitud inválida. Intenta nuevamente.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $next = (string) ($_POST['next'] ?? 'index.php');
        if (!in_array($next, $allowedNext, true)) {
            $next = 'index.php';
        }

        $user = find_user($username);
        if (!$user) {
            // Constant-time guard to reduce user enumeration via timing.
            password_verify($password, '$2y$10$xDxiTJoFCV4Ql6kgYxXUCO79avXrPHywPPlnfAf78B0.DGngnlU/q');
        }

        if ($user && user_is_locked($user)) {
            $remainingMinutes = max(1, (int) ceil(user_lock_remaining_seconds($user) / 60));
            $errors[] = 'Cuenta bloqueada temporalmente por seguridad. Intenta de nuevo en ' . $remainingMinutes . ' minuto(s).';
        } elseif ($user && (int) ($user['status'] ?? 1) !== 1) {
            $errors[] = 'Tu cuenta está inactiva. Contacta al administrador.';
        } elseif ($user && isset($user['password_hash']) && password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            user_record_successful_login((string) ($user['username'] ?? $username));
            session_regenerate_id(true);
            $_SESSION['user'] = $user['username'];
            set_flash('success', 'Bienvenido, ' . ($user['name'] ?? $user['username']) . '.');
            header('Location: ' . app_url($next));
            exit;
        } else {
            if ($user) {
                user_record_failed_login((string) ($user['username'] ?? $username));
            }
            $errors[] = 'Usuario o contraseña inválidos.';
        }
    }
}

if (!in_array($next, $allowedNext, true)) {
    $next = 'index.php';
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
                <input type="text" name="username" autocomplete="username" required />
            </label>
            <label>
                Contraseña
                <input type="password" name="password" autocomplete="current-password" required />
            </label>
            <button class="btn-submit" type="submit">Ingresar</button>
        </form>

        <p style="margin-top:0.9rem;color:#4a5873;">
            ¿No tienes cuenta? <a href="<?= e(app_url('register.php')) ?>">Crea una aquí</a>.
        </p>
    </section>
</main>
<?php render_footer(); ?>
