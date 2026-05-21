<?php

require_once __DIR__ . '/includes/layout.php';

$currentUser = current_user();
$q = trim((string) ($_GET['q'] ?? ''));

function tiempo_lectura(string $texto): int
{
    $plano = trim(strip_tags($texto));
    if ($plano === '') {
        return 1;
    }

    preg_match_all('/[\p{L}\p{N}]+/u', $plano, $matches);
    $wordCount = count($matches[0] ?? []);

    return max(1, (int) ceil($wordCount / 130));
}

function format_blog_date(string $dateRaw): string
{
    $dateRaw = trim($dateRaw);
    if ($dateRaw === '') {
        return '';
    }

    $timestamp = strtotime($dateRaw);
    if ($timestamp === false) {
        return $dateRaw;
    }

    $months = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre',
    ];

    $day = (int) date('j', $timestamp);
    $month = $months[(int) date('n', $timestamp)] ?? date('m', $timestamp);
    $year = date('Y', $timestamp);

    return $day . ' de ' . $month . ', ' . $year;
}

function render_article_content(string $content): string
{
    $normalized = preg_replace("/\r\n?/", "\n", $content) ?? $content;
    $blocks = preg_split("/\n{2,}/", trim($normalized)) ?: [];
    $html = '';

    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }

        $lines = array_values(array_filter(
            array_map('trim', explode("\n", $block)),
            static fn (string $line): bool => $line !== ''
        ));

        if (count($lines) >= 2 && str_ends_with($lines[0], ':')) {
            $listLines = array_slice($lines, 1);
            $isBulletBlock = !empty($listLines) && array_reduce(
                $listLines,
                static fn (bool $carry, string $line): bool => $carry && str_starts_with($line, '- '),
                true
            );

            if ($isBulletBlock) {
                $html .= '<aside class="article-callout">';
                $html .= '<p class="article-callout__title">' . e($lines[0]) . '</p>';
                $html .= '<ul class="article-callout__list">';
                foreach ($listLines as $line) {
                    $html .= '<li>' . e(trim(substr($line, 2))) . '</li>';
                }
                $html .= '</ul>';
                $html .= '</aside>';
                continue;
            }
        }

        $joined = implode(' ', $lines);
        $html .= '<p>' . e($joined) . '</p>';
    }

    return $html;
}

function resolve_service_demo_url(string $serviceId): string
{
    $service = service_by_id($serviceId);
    if (!$service) {
        return app_url('servicios.php');
    }

    $demoUrlRaw = trim((string) ($service['demo_url'] ?? ''));
    if ($demoUrlRaw === '') {
        return app_url('servicios.php');
    }

    return str_starts_with($demoUrlRaw, 'http') ? $demoUrlRaw : app_url($demoUrlRaw);
}

function service_for_post(string $postId): ?array
{
    if (!preg_match('/^post-([a-z0-9]+)-\d+$/i', $postId, $matches)) {
        return null;
    }

    $serviceId = strtolower((string) ($matches[1] ?? ''));
    if ($serviceId === '') {
        return null;
    }

    return service_by_id($serviceId);
}

function blog_redirect(string $q = '', string $anchor = ''): void
{
    $url = app_url('blog.php');
    if ($q !== '') {
        $url .= '?q=' . urlencode($q);
    }

    if ($anchor !== '') {
        $url .= '#' . rawurlencode($anchor);
    }

    header('Location: ' . $url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        set_flash('error', 'Solicitud inválida. Intenta de nuevo.');
        blog_redirect($q);
    }

    $action = (string) ($_POST['action'] ?? '');
    $postId = trim((string) ($_POST['post_id'] ?? ''));
    $q = trim((string) ($_POST['q'] ?? $q));

    if ($postId === '' || !post_by_id($postId)) {
        set_flash('error', 'No encontramos la publicación seleccionada.');
        blog_redirect($q);
    }

    if (!$currentUser) {
        set_flash('error', 'Debes iniciar sesión para comentar o dar like.');
        header('Location: ' . app_url('login.php?next=blog.php'));
        exit;
    }

    if ($action === 'like_toggle') {
        $liked = post_toggle_like($postId, (string) ($currentUser['username'] ?? ''));
        set_flash('success', $liked ? 'Te gusta esta publicación.' : 'Quitaste tu like.');
        blog_redirect($q, $postId);
    }

    if ($action === 'comment_create') {
        $comment = trim((string) ($_POST['comment'] ?? ''));
        if ($comment === '') {
            set_flash('error', 'El comentario no puede estar vacío.');
            blog_redirect($q, $postId);
        }

        if (mb_strlen($comment) > 2000) {
            set_flash('error', 'El comentario no debe superar 2000 caracteres.');
            blog_redirect($q, $postId);
        }

        $ok = post_comment_create($postId, (string) ($currentUser['username'] ?? ''), $comment);
        if ($ok) {
            set_flash('success', 'Comentario publicado.');
        } else {
            set_flash('error', 'No se pudo publicar el comentario.');
        }

        blog_redirect($q, $postId);
    }

    set_flash('error', 'Acción no reconocida.');
    blog_redirect($q, $postId);
}

$posts = posts_all($q);
$totalPosts = count($posts);
$postIds = array_values(array_map(static fn(array $post): string => (string) ($post['id'] ?? ''), $posts));
$postImagesMap = post_images_map_for_posts($postIds);
render_header('Blog', 'blog');
?>
<main class="blog-page">
    <section class="blog-hero wrap">
        <div class="blog-hero-image" style="background-image:url('<?= e(app_url('img/Vista principal app.jpeg')) ?>');">
            <div class="blog-hero-overlay">
                <p class="blog-kicker">PERSPECTIVAS DIGITALES</p>
                <h1>Información técnica que impulsa la rentabilidad empresarial.</h1>
                <p>
                    Casos prácticos y documentación detallada sobre cómo implementar arquitectura digital para
                    auditar, controlar y escalar operaciones en tiempo real.
                </p>
                <div class="blog-hero-actions">
                    <a class="btn btn-main" href="#publicaciones">Ver publicaciones</a>
                </div>
            </div>
        </div>

        <div class="blog-metrics">
            <article>
                <strong><?= $totalPosts ?></strong>
                <span><?= $totalPosts === 1 ? 'Publicación activa' : 'Publicaciones activas' ?></span>
            </article>
            <article>
                <strong>5</strong>
                <span>Servicios integrados</span>
            </article>
            <article>
                <strong>100%</strong>
                <span>Enfoque práctico</span>
            </article>
        </div>
    </section>

    <section class="wrap blog-section" style="padding-top:1.1rem;">
        <form method="get" class="blog-search" aria-label="Buscar en el blog">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por tema: ventas, gestión, trazabilidad..." />
            <button class="btn-submit" type="submit">Buscar</button>
        </form>
    </section>

    <?php if ($totalPosts === 0): ?>
        <section class="wrap blog-section">
            <article class="post-card-empty">
                <h3>Sin resultados</h3>
                <p>No encontramos publicaciones con ese término. Intenta con otra búsqueda.</p>
            </article>
        </section>
    <?php else: ?>
        <section id="publicaciones" class="wrap blog-section blog-article-stack">
            <?php foreach ($posts as $index => $post): ?>
                <?php
                $titulo = (string) ($post['title'] ?? 'Sin título');
                $resumen = (string) ($post['excerpt'] ?? '');
                $contenido = (string) ($post['content'] ?? '');
                $autor = (string) ($post['author'] ?? 'Autor');
                $fecha = format_blog_date((string) ($post['published_at'] ?? ''));
                $lectura = tiempo_lectura($contenido);
                $postId = (string) ($post['id'] ?? ('post-' . $index));
                $service = service_for_post($postId);
                $serviceName = trim((string) ($service['name'] ?? ''));
                $serviceDemoUrl = $service ? resolve_service_demo_url((string) ($service['id'] ?? '')) : '';
                $postImages = $postImagesMap[$postId] ?? [];
                $primaryImage = $postImages[0] ?? null;
                $likesCount = post_likes_count($postId);
                $likedByCurrentUser = $currentUser ? post_is_liked_by_user($postId, (string) ($currentUser['username'] ?? '')) : false;
                $comments = post_comments_for_post($postId);
                ?>
                <article id="<?= e($postId) ?>" class="articulo-destacado">
                    <header class="articulo-cabecera">
                        <p class="post-meta"><?= e($autor) ?> · <?= e($fecha) ?> · <?= $lectura ?> min de lectura</p>
                        <h2><?= e($titulo) ?></h2>
                        <?php if ($resumen !== ''): ?>
                            <p class="articulo-resumen"><?= e($resumen) ?></p>
                        <?php endif; ?>
                    </header>

                    <div class="articulo-cuerpo">
                        <?php if ($primaryImage): ?>
                            <figure class="article-image">
                                <img
                                    src="<?= e(app_url((string) ($primaryImage['image_path'] ?? ''))) ?>"
                                    alt="<?= e((string) ($primaryImage['alt_text'] ?? $titulo)) ?>"
                                    loading="lazy"
                                />
                            </figure>
                        <?php endif; ?>
                        <?= render_article_content($contenido) ?>
                    </div>

                    <?php if ($service && $serviceName !== '' && $serviceDemoUrl !== ''): ?>
                        <div class="article-cta">
                            <p class="article-cta__text">¿Listo para implementar <?= e($serviceName) ?> en tu empresa?</p>
                            <a class="article-cta__btn" href="<?= e($serviceDemoUrl) ?>">Solicitar Demo de <?= e($serviceName) ?></a>
                        </div>
                    <?php endif; ?>

                    <section class="post-engagement">
                        <div class="post-engagement__header">
                            <strong><?= $likesCount ?></strong> <?= $likesCount === 1 ? 'like' : 'likes' ?>
                            <span>·</span>
                            <strong><?= count($comments) ?></strong> <?= count($comments) === 1 ? 'comentario' : 'comentarios' ?>
                        </div>

                        <form method="post" class="post-like-form">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                            <input type="hidden" name="action" value="like_toggle" />
                            <input type="hidden" name="post_id" value="<?= e($postId) ?>" />
                            <input type="hidden" name="q" value="<?= e($q) ?>" />
                            <button
                                type="submit"
                                class="post-like-btn <?= $likedByCurrentUser ? 'is-liked' : '' ?>"
                                aria-label="<?= $likedByCurrentUser ? 'Quitar like' : 'Dar like' ?>"
                                title="<?= $likedByCurrentUser ? 'Quitar like' : 'Dar like' ?>"
                            >
                                <svg class="post-like-btn__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M12 21s-6.8-4.2-9.5-8.2C.3 9.5 1.2 5.3 4.8 4.2c2.3-.7 4.4.2 5.7 2 1.3-1.8 3.4-2.7 5.7-2 3.6 1.1 4.5 5.3 2.3 8.6C18.8 16.8 12 21 12 21z"></path>
                                </svg>
                                <span class="sr-only"><?= $likedByCurrentUser ? 'Quitar like' : 'Dar like' ?></span>
                            </button>
                        </form>

                        <?php if ($currentUser): ?>
                            <form method="post" class="post-comment-form">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                                <input type="hidden" name="action" value="comment_create" />
                                <input type="hidden" name="post_id" value="<?= e($postId) ?>" />
                                <input type="hidden" name="q" value="<?= e($q) ?>" />
                                <label>
                                    Comentar como <?= e((string) ($currentUser['name'] ?? $currentUser['username'] ?? 'usuario')) ?>
                                    <textarea name="comment" maxlength="2000" placeholder="Escribe tu comentario..." required></textarea>
                                </label>
                                <div>
                                    <button type="submit" class="btn-submit">Publicar comentario</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <p class="post-engagement__login-hint">
                                Para comentar o dar like debes <a href="<?= e(app_url('login.php?next=blog.php')) ?>">iniciar sesión</a> o
                                <a href="<?= e(app_url('register.php')) ?>">crear una cuenta</a>.
                            </p>
                        <?php endif; ?>

                        <?php if (empty($comments)): ?>
                            <p class="post-comment-empty">Aún no hay comentarios en esta publicación.</p>
                        <?php else: ?>
                            <div class="post-comments">
                                <?php foreach ($comments as $comment): ?>
                                    <?php
                                    $commentAuthor = (string) ($comment['user_name'] ?? $comment['name'] ?? $comment['username'] ?? 'Usuario');
                                    $commentDateRaw = (string) ($comment['created_at'] ?? '');
                                    $commentDate = $commentDateRaw;
                                    $timestamp = strtotime($commentDateRaw);
                                    if ($timestamp !== false) {
                                        $commentDate = date('Y-m-d H:i', $timestamp);
                                    }
                                    ?>
                                    <article class="post-comment-item">
                                        <p class="post-comment-meta"><strong><?= e($commentAuthor) ?></strong> · <?= e($commentDate) ?></p>
                                        <p><?= nl2br(e((string) ($comment['content'] ?? ''))) ?></p>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>
<?php render_footer(); ?>
