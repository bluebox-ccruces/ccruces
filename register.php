<?php

require_once __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
    header('Location: ' . app_url('panel.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        $errors[] = 'Solicitud inválida. Intenta nuevamente.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        if ($username === '' || $email === '' || $name === '' || $password === '') {
            $errors[] = 'Completa todos los campos obligatorios.';
        }

        if (!user_username_is_valid($username)) {
            $errors[] = 'El usuario debe tener 3-64 caracteres (letras, números, punto, guion o guion bajo).';
        }
        if (!user_email_is_valid($email)) {
            $errors[] = 'Ingresa un correo electrónico válido.';
        }

        foreach (user_password_policy_errors($password) as $passwordError) {
            $errors[] = $passwordError;
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if (empty($errors)) {
            $created = user_create($username, $email, $name, 'client', $password);
            if ($created) {
                set_flash('success', 'Cuenta creada. Ya puedes iniciar sesión.');
                header('Location: ' . app_url('login.php?next=blog.php'));
                exit;
            }

            $errors[] = 'No se pudo crear la cuenta. Verifica si el usuario o correo ya existe.';
        }
    }
}

render_header('Crear Cuenta', 'register');
?>
<main class="auth-box">
    <section class="form-card">
        <h2>Crear cuenta</h2>
        <p class="lead">Regístrate para comentar y dar like en las publicaciones.</p>
        <p class="lead" style="margin-top:-0.4rem;">La contraseña debe tener mínimo 10 caracteres, mayúscula, minúscula, número y símbolo.</p>

        <?php foreach ($errors as $error): ?>
            <div class="flash flash-error" style="width:100%;margin:0 0 .8rem;"><?= e($error) ?></div>
        <?php endforeach; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
            <label>
                Usuario
                <input type="text" name="username" autocomplete="username" required />
            </label>
            <label>
                Correo electrónico
                <input type="email" name="email" autocomplete="email" required />
            </label>
            <label>
                Nombre visible
                <input type="text" name="name" autocomplete="name" required />
            </label>
            <label>
                Contraseña
                <input type="password" name="password" autocomplete="new-password" required />
            </label>
            <label>
                Confirmar contraseña
                <input type="password" name="password_confirm" autocomplete="new-password" required />
            </label>
            <button class="btn-submit" type="submit">Crear cuenta</button>
        </form>

        <p style="margin-top:0.9rem;color:#4a5873;">
            ¿Ya tienes cuenta? <a href="<?= e(app_url('login.php?next=blog.php')) ?>">Ingresa aquí</a>.
        </p>
    </section>
</main>
<?php render_footer(); ?>

