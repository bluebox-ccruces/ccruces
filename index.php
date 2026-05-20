<?php

require_once __DIR__ . '/includes/layout.php';

$services = services_all();
$posts = posts_all();
$featuredPosts = array_slice($posts, 0, 2);

render_header('Inicio', 'home');
?>
<main>
    <section class="hero wrap">
        <article class="hero-card">
            <p class="kicker">CCruces Holding</p>
            <h1>Descubre soluciones digitales reales para tu empresa, en un solo lugar.</h1>
            <p>
                En ccruces.com puedes conocer nuestros servicios, probar demos funcionales y acceder a plataformas
                privadas si ya eres cliente. Todo pensado para que tomes decisiones rápidas y con confianza.
            </p>
            <div class="hero-metrics">
                <span class="metric"><?= count($services) ?> soluciones disponibles</span>
                <span class="metric">Demos para evaluar antes de contratar</span>
                <span class="metric">Acceso seguro para clientes</span>
            </div>
            <div class="cta-row">
                <a class="btn btn-main" href="<?= e(app_url('servicios.php')) ?>">Ver soluciones</a>
                <a class="btn btn-soft" href="<?= e(app_url('blog.php')) ?>">Conocer novedades</a>
            </div>
        </article>
    </section>

    <section class="section wrap">
        <h2>Servicios del holding</h2>
        <p class="lead">Modo demo para prospectos y acceso privado para clientes con permisos.</p>
        <div class="grid">
            <?php foreach ($services as $service): ?>
                <?php
                $modalPayload = service_modal_payload($service);
                $modalJson = json_encode($modalPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                ?>
                <article
                    class="card service-card"
                    role="button"
                    tabindex="0"
                    data-service-card
                    data-service-payload="<?= e($modalJson ?: '{}') ?>"
                    aria-label="Ver detalles de <?= e((string) ($service['name'] ?? 'Servicio')) ?>"
                >
                    <img src="<?= e(app_url((string) ($service['logo'] ?? 'img/Icono BB.png'))) ?>" alt="<?= e((string) ($service['name'] ?? 'Servicio')) ?>" />
                    <h3><?= e((string) ($service['name'] ?? 'Servicio')) ?></h3>
                    <p><?= e((string) ($service['description'] ?? '')) ?></p>
                    <div class="row-actions">
                        <a class="btn-mini" href="<?= e((string) ($modalPayload['demo_url'] ?? '#')) ?>">Ver demo</a>
                        <a class="btn-mini main" href="<?= e(app_url('acceso.php?servicio=' . urlencode((string) ($service['id'] ?? '')))) ?>">Acceso privado</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section wrap">
        <h2>Últimas publicaciones</h2>
        <p class="lead">Tu blog personal listo para construir marca y comunicar avances.</p>
        <div class="grid">
            <?php foreach ($featuredPosts as $post): ?>
                <article class="post">
                    <h3><?= e((string) ($post['title'] ?? 'Sin título')) ?></h3>
                    <small><?= e((string) ($post['author'] ?? 'Autor')) ?> · <?= e((string) ($post['published_at'] ?? '')) ?></small>
                    <p><?= e((string) ($post['excerpt'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="row-actions" style="margin-top:1rem;">
            <a class="btn-mini main" href="<?= e(app_url('blog.php')) ?>">Ver todo el blog</a>
        </div>
    </section>
</main>
<?php render_service_modal_shell(); ?>
<?php render_footer(); ?>

