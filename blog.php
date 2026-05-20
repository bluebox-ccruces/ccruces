<?php

require_once __DIR__ . '/includes/layout.php';

$q = trim((string) ($_GET['q'] ?? ''));
$posts = posts_all($q);
$totalPosts = count($posts);

function tiempo_lectura(string $texto): int
{
    $plano = trim(strip_tags($texto));
    if ($plano === '') {
        return 1;
    }

    return max(1, (int) ceil(str_word_count($plano) / 210));
}

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
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>
<?php render_footer(); ?>
