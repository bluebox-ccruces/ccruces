<?php

require_once __DIR__ . '/includes/layout.php';

require_admin();

$section = (string) ($_GET['section'] ?? 'posts');
$validSections = ['posts', 'services', 'users'];
if (!in_array($section, $validSections, true)) {
    $section = 'posts';
}

$current = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        set_flash('error', 'Token CSRF inválido.');
        header('Location: ' . app_url('admin.php?section=' . urlencode($section)));
        exit;
    }

    $entity = (string) ($_POST['entity'] ?? '');
    $action = (string) ($_POST['action'] ?? '');
    $section = (string) ($_POST['section'] ?? $section);
    if (!in_array($section, $validSections, true)) {
        $section = 'posts';
    }

    if ($entity === 'post') {
        if ($action === 'create') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));
            $authorInput = trim((string) ($_POST['author'] ?? ''));
            $author = $authorInput !== '' ? $authorInput : (string) ($current['name'] ?? $current['username'] ?? 'Carlos Cruces');

            if ($title === '' || $content === '') {
                set_flash('error', 'Título y contenido son obligatorios.');
            } elseif (post_create($title, $excerpt, $content, $author)) {
                set_flash('success', 'Publicación creada correctamente.');
            } else {
                set_flash('error', 'No se pudo crear la publicación.');
            }
        }

        if ($action === 'update') {
            $id = (string) ($_POST['id'] ?? '');
            $title = trim((string) ($_POST['title'] ?? ''));
            $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));
            $author = trim((string) ($_POST['author'] ?? ''));
            $publishedAt = trim((string) ($_POST['published_at'] ?? date('Y-m-d')));

            if (post_update($id, $title, $excerpt, $content, $author, $publishedAt)) {
                set_flash('success', 'Publicación actualizada.');
            } else {
                set_flash('error', 'No se pudo actualizar la publicación.');
            }
        }

        if ($action === 'delete') {
            $id = (string) ($_POST['id'] ?? '');
            if (post_delete($id)) {
                set_flash('success', 'Publicación eliminada.');
            } else {
                set_flash('error', 'No se pudo eliminar la publicación.');
            }
        }
    }

    if ($entity === 'service') {
        if ($action === 'create') {
            $data = [
                'id' => trim((string) ($_POST['id'] ?? '')),
                'name' => trim((string) ($_POST['name'] ?? '')),
                'tagline' => trim((string) ($_POST['tagline'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'logo' => trim((string) ($_POST['logo'] ?? 'img/Icono BB.png')),
                'demo_url' => trim((string) ($_POST['demo_url'] ?? '')),
                'private_url' => trim((string) ($_POST['private_url'] ?? '')),
                'status' => trim((string) ($_POST['status'] ?? 'Activo')),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            ];

            if (service_create($data)) {
                set_flash('success', 'Servicio creado correctamente.');
            } else {
                set_flash('error', 'No se pudo crear el servicio. Verifica ID único y campos obligatorios.');
            }
        }

        if ($action === 'update') {
            $id = (string) ($_POST['id'] ?? '');
            $data = [
                'name' => trim((string) ($_POST['name'] ?? '')),
                'tagline' => trim((string) ($_POST['tagline'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'logo' => trim((string) ($_POST['logo'] ?? 'img/Icono BB.png')),
                'demo_url' => trim((string) ($_POST['demo_url'] ?? '')),
                'private_url' => trim((string) ($_POST['private_url'] ?? '')),
                'status' => trim((string) ($_POST['status'] ?? 'Activo')),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            ];

            if (service_update($id, $data)) {
                set_flash('success', 'Servicio actualizado.');
            } else {
                set_flash('error', 'No se pudo actualizar el servicio.');
            }
        }

        if ($action === 'delete') {
            $id = (string) ($_POST['id'] ?? '');
            if (service_delete($id)) {
                set_flash('success', 'Servicio eliminado.');
            } else {
                set_flash('error', 'No se pudo eliminar el servicio.');
            }
        }
    }

    if ($entity === 'user') {
        if ($action === 'create') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $name = trim((string) ($_POST['name'] ?? ''));
            $role = trim((string) ($_POST['role'] ?? 'client'));
            $password = (string) ($_POST['password'] ?? '');

            if (user_create($username, $name, $role, $password)) {
                set_flash('success', 'Usuario creado correctamente.');
            } else {
                set_flash('error', 'No se pudo crear el usuario. Verifica username único y política de contraseña segura.');
            }
        }

        if ($action === 'update') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $name = trim((string) ($_POST['name'] ?? ''));
            $role = trim((string) ($_POST['role'] ?? 'client'));
            $status = (int) ($_POST['status'] ?? 1);
            $password = trim((string) ($_POST['password'] ?? ''));

            $passwordOrNull = $password === '' ? null : $password;
            if (user_update($username, $name, $role, $passwordOrNull, $status)) {
                set_flash('success', 'Usuario actualizado.');
            } else {
                set_flash('error', 'No se pudo actualizar el usuario. Verifica la política de contraseña segura.');
            }
        }

        if ($action === 'delete') {
            $username = trim((string) ($_POST['username'] ?? ''));
            if ($current && strcasecmp((string) ($current['username'] ?? ''), $username) === 0) {
                set_flash('error', 'No puedes eliminar tu propio usuario administrador activo.');
            } elseif (user_delete($username)) {
                set_flash('success', 'Usuario eliminado.');
            } else {
                set_flash('error', 'No se pudo eliminar el usuario.');
            }
        }
    }

    header('Location: ' . app_url('admin.php?section=' . urlencode($section)));
    exit;
}

$posts = posts_all();
$services = services_all();
$users = users_all();

render_header('Administración', 'admin');
?>
<main class="wrap section">
    <h2>Panel de administración</h2>
    <p class="lead">Administra publicaciones, servicios y usuarios desde un único panel.</p>

    <nav class="admin-tabs" aria-label="Secciones de administración">
        <a class="<?= $section === 'posts' ? 'is-active' : '' ?>" href="<?= e(app_url('admin.php?section=posts')) ?>">Publicaciones</a>
        <a class="<?= $section === 'services' ? 'is-active' : '' ?>" href="<?= e(app_url('admin.php?section=services')) ?>">Servicios</a>
        <a class="<?= $section === 'users' ? 'is-active' : '' ?>" href="<?= e(app_url('admin.php?section=users')) ?>">Usuarios</a>
    </nav>

    <?php if ($section === 'posts'): ?>
        <section class="admin-panel">
            <h3>Nueva publicación</h3>
            <form method="post" class="admin-form">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                <input type="hidden" name="entity" value="post" />
                <input type="hidden" name="action" value="create" />
                <input type="hidden" name="section" value="posts" />

                <label>Título <input type="text" name="title" required /></label>
                <label>Resumen <input type="text" name="excerpt" /></label>
                <label>Contenido <textarea name="content" required></textarea></label>
                <label>Autor <input type="text" name="author" value="<?= e((string) ($current['name'] ?? $current['username'] ?? 'Carlos Cruces')) ?>" required /></label>
                <button class="btn-submit" type="submit">Crear publicación</button>
            </form>
        </section>

        <section class="admin-stack">
            <?php foreach ($posts as $post): ?>
                <article class="admin-item">
                    <form method="post" class="admin-form compact">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                        <input type="hidden" name="entity" value="post" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="section" value="posts" />
                        <input type="hidden" name="id" value="<?= e((string) ($post['id'] ?? '')) ?>" />

                        <label>Título <input type="text" name="title" value="<?= e((string) ($post['title'] ?? '')) ?>" required /></label>
                        <label>Resumen <input type="text" name="excerpt" value="<?= e((string) ($post['excerpt'] ?? '')) ?>" /></label>
                        <label>Contenido <textarea name="content" required><?= e((string) ($post['content'] ?? '')) ?></textarea></label>
                        <div class="admin-grid-2">
                            <label>Autor <input type="text" name="author" value="<?= e((string) ($post['author'] ?? '')) ?>" required /></label>
                            <label>Fecha <input type="date" name="published_at" value="<?= e((string) ($post['published_at'] ?? date('Y-m-d'))) ?>" required /></label>
                        </div>

                        <div class="admin-actions">
                            <button class="btn-submit" type="submit">Guardar cambios</button>
                        </div>
                    </form>

                    <form method="post" onsubmit="return confirm('¿Eliminar esta publicación?');" class="admin-delete-form">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                        <input type="hidden" name="entity" value="post" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="section" value="posts" />
                        <input type="hidden" name="id" value="<?= e((string) ($post['id'] ?? '')) ?>" />
                        <button class="btn-mini" type="submit">Eliminar</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <?php if ($section === 'services'): ?>
        <section class="admin-panel">
            <h3>Nuevo servicio</h3>
            <form method="post" class="admin-form">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                <input type="hidden" name="entity" value="service" />
                <input type="hidden" name="action" value="create" />
                <input type="hidden" name="section" value="services" />

                <div class="admin-grid-2">
                    <label>ID <input type="text" name="id" placeholder="ej: bluesalesv2" required /></label>
                    <label>Nombre <input type="text" name="name" required /></label>
                </div>
                <label>Tagline <input type="text" name="tagline" /></label>
                <label>Descripción <textarea name="description" required></textarea></label>
                <div class="admin-grid-2">
                    <label>Logo <input type="text" name="logo" value="img/Icono BB.png" /></label>
                    <label>Estado <input type="text" name="status" value="Activo" /></label>
                </div>
                <div class="admin-grid-2">
                    <label>URL demo <input type="text" name="demo_url" required /></label>
                    <label>URL privada <input type="text" name="private_url" required /></label>
                </div>
                <label>Orden <input type="number" name="sort_order" value="0" min="0" /></label>
                <button class="btn-submit" type="submit">Crear servicio</button>
            </form>
        </section>

        <section class="admin-stack">
            <?php foreach ($services as $service): ?>
                <article class="admin-item">
                    <form method="post" class="admin-form compact">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                        <input type="hidden" name="entity" value="service" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="section" value="services" />
                        <input type="hidden" name="id" value="<?= e((string) ($service['id'] ?? '')) ?>" />

                        <p class="admin-id">ID: <strong><?= e((string) ($service['id'] ?? '')) ?></strong></p>
                        <div class="admin-grid-2">
                            <label>Nombre <input type="text" name="name" value="<?= e((string) ($service['name'] ?? '')) ?>" required /></label>
                            <label>Tagline <input type="text" name="tagline" value="<?= e((string) ($service['tagline'] ?? '')) ?>" /></label>
                        </div>
                        <label>Descripción <textarea name="description" required><?= e((string) ($service['description'] ?? '')) ?></textarea></label>
                        <div class="admin-grid-2">
                            <label>Logo <input type="text" name="logo" value="<?= e((string) ($service['logo'] ?? '')) ?>" /></label>
                            <label>Estado <input type="text" name="status" value="<?= e((string) ($service['status'] ?? '')) ?>" /></label>
                        </div>
                        <div class="admin-grid-2">
                            <label>URL demo <input type="text" name="demo_url" value="<?= e((string) ($service['demo_url'] ?? '')) ?>" required /></label>
                            <label>URL privada <input type="text" name="private_url" value="<?= e((string) ($service['private_url'] ?? '')) ?>" required /></label>
                        </div>
                        <label>Orden <input type="number" name="sort_order" value="<?= e((string) ($service['sort_order'] ?? '0')) ?>" min="0" /></label>

                        <div class="admin-actions">
                            <button class="btn-submit" type="submit">Guardar cambios</button>
                        </div>
                    </form>

                    <form method="post" onsubmit="return confirm('¿Eliminar este servicio?');" class="admin-delete-form">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                        <input type="hidden" name="entity" value="service" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="section" value="services" />
                        <input type="hidden" name="id" value="<?= e((string) ($service['id'] ?? '')) ?>" />
                        <button class="btn-mini" type="submit">Eliminar</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <?php if ($section === 'users'): ?>
        <section class="admin-panel">
            <h3>Nuevo usuario</h3>
            <form method="post" class="admin-form">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                <input type="hidden" name="entity" value="user" />
                <input type="hidden" name="action" value="create" />
                <input type="hidden" name="section" value="users" />

                <div class="admin-grid-2">
                    <label>Usuario <input type="text" name="username" required /></label>
                    <label>Nombre <input type="text" name="name" required /></label>
                </div>
                <div class="admin-grid-2">
                    <label>Rol
                        <select name="role">
                            <option value="client">Cliente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </label>
                    <label>Contraseña <input type="password" name="password" required /></label>
                </div>
                <button class="btn-submit" type="submit">Crear usuario</button>
            </form>
        </section>

        <section class="admin-stack">
            <?php foreach ($users as $user): ?>
                <?php $isCurrent = $current && strcasecmp((string) ($current['username'] ?? ''), (string) ($user['username'] ?? '')) === 0; ?>
                <article class="admin-item">
                    <form method="post" class="admin-form compact">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                        <input type="hidden" name="entity" value="user" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="section" value="users" />
                        <input type="hidden" name="username" value="<?= e((string) ($user['username'] ?? '')) ?>" />

                        <p class="admin-id">Usuario: <strong><?= e((string) ($user['username'] ?? '')) ?></strong><?= $isCurrent ? ' (actual)' : '' ?></p>

                        <div class="admin-grid-2">
                            <label>Nombre <input type="text" name="name" value="<?= e((string) ($user['name'] ?? '')) ?>" required /></label>
                            <label>Rol
                                <select name="role">
                                    <option value="client" <?= (($user['role'] ?? '') === 'client') ? 'selected' : '' ?>>Cliente</option>
                                    <option value="admin" <?= (($user['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Administrador</option>
                                </select>
                            </label>
                        </div>

                        <div class="admin-grid-2">
                            <label>Estado
                                <select name="status">
                                    <option value="1" <?= ((int) ($user['status'] ?? 1) === 1) ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= ((int) ($user['status'] ?? 1) === 0) ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </label>
                            <label>Nueva contraseña (opcional) <input type="password" name="password" /></label>
                        </div>

                        <div class="admin-actions">
                            <button class="btn-submit" type="submit">Guardar cambios</button>
                        </div>
                    </form>

                    <form method="post" onsubmit="return confirm('¿Eliminar este usuario?');" class="admin-delete-form">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                        <input type="hidden" name="entity" value="user" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="section" value="users" />
                        <input type="hidden" name="username" value="<?= e((string) ($user['username'] ?? '')) ?>" />
                        <button class="btn-mini" type="submit" <?= $isCurrent ? 'disabled' : '' ?>>Eliminar</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>
<?php render_footer(); ?>

