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

    return max(1, (int) ceil(str_word_count($plano) / 210));
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

render_header('Blog', 'blog');
?>
<main class="blog-page">
    <section class="blog-hero wrap">
        <div class="blog-hero-image" style="background-image:url('<?= e(app_url('img/Vista principal app.jpeg')) ?>');">
            <div class="blog-hero-overlay">
                <p class="blog-kicker">Centro de contenidos BlueBox</p>
                <h1>Publicaciones técnicas para decisiones de negocio</h1>
                <p>
                    Análisis operativo, diseño de soluciones y casos aplicados para escalar procesos con mayor control,
                    trazabilidad y eficiencia.
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
                $fecha = (string) ($post['published_at'] ?? '');
                $lectura = tiempo_lectura($contenido);
                $postId = (string) ($post['id'] ?? ('post-' . $index));
                $likesCount = post_likes_count($postId);
                $likedByCurrentUser = $currentUser ? post_is_liked_by_user($postId, (string) ($currentUser['username'] ?? '')) : false;
                $comments = post_comments_for_post($postId);
                ?>
                <article id="<?= e($postId) ?>" class="<?= $index === 0 ? 'articulo-destacado' : 'article-panel' ?>">
                    <header class="<?= $index === 0 ? 'articulo-cabecera' : '' ?>">
                        <p class="post-meta"><?= e($autor) ?> · <?= e($fecha) ?> · <?= $lectura ?> min de lectura</p>
                        <?php if ($index === 0): ?>
                            <h2><?= e($titulo) ?></h2>
                            <p class="articulo-resumen"><?= e($resumen) ?></p>
                        <?php else: ?>
                            <h3><?= e($titulo) ?></h3>
                            <?php if ($resumen !== ''): ?>
                                <p class="articulo-resumen"><?= e($resumen) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </header>

                    <div class="<?= $index === 0 ? 'articulo-cuerpo' : 'article-body' ?>">
                        <?= nl2br(e($contenido)) ?>
                    </div>

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
                            <button type="submit" class="btn-mini <?= $likedByCurrentUser ? 'main' : '' ?>">
                                <?= $likedByCurrentUser ? 'Quitar like' : 'Dar like' ?>
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
