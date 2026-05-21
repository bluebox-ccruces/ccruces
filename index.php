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
            <h1>Innovación digital y gestión inteligente en un solo lugar.</h1>
            <p>
                Centraliza tus operaciones con herramientas diseñadas para optimizar la productividad de tu empresa.
                Explora nuestras soluciones disponibles o ingresa a tu panel privado como cliente.
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
        <div class="services-slider" data-service-slider>
            <button class="services-slider__control" type="button" data-slider-prev aria-label="Servicio anterior">&#8249;</button>
            <div class="services-slider__viewport" data-slider-viewport tabindex="0" aria-label="Carrusel de servicios">
                <div class="services-slider__track">
                    <?php foreach ($services as $service): ?>
                        <?php
                        $modalPayload = service_modal_payload($service);
                        $modalJson = json_encode($modalPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        ?>
                        <article
                            class="card service-card services-slider__item"
                            role="button"
                            tabindex="0"
                            data-service-id="<?= e((string) ($service['id'] ?? '')) ?>"
                            data-service-card
                            data-service-payload="<?= e($modalJson ?: '{}') ?>"
                            aria-label="Ver detalles de <?= e((string) ($service['name'] ?? 'Servicio')) ?>"
                        >
                            <div class="service-card__logo-frame">
                                <img class="service-card__logo" src="<?= e(app_url((string) ($service['logo'] ?? 'img/Icono BB.png'))) ?>" alt="<?= e((string) ($service['name'] ?? 'Servicio')) ?>" />
                            </div>
                            <h3><?= e((string) ($service['name'] ?? 'Servicio')) ?></h3>
                            <p><?= e((string) ($service['description'] ?? '')) ?></p>
                            <div class="row-actions service-actions">
                                <button class="btn-mini" type="button" data-service-open>Información</button>
                                <a class="btn-mini main" href="<?= e(service_private_entry_url($service)) ?>">Ingresar</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="services-slider__control" type="button" data-slider-next aria-label="Siguiente servicio">&#8250;</button>
        </div>
    </section>

    <section class="section wrap">
        <h2>Últimas publicaciones</h2>
        <p class="lead">Tu blog personal listo para construir marca y comunicar avances.</p>
        <div class="latest-posts-grid">
            <?php foreach ($featuredPosts as $post): ?>
                <article class="articulo-destacado latest-post-card">
                    <header class="articulo-cabecera">
                        <p class="post-meta"><?= e((string) ($post['author'] ?? 'Autor')) ?> · <?= e((string) ($post['published_at'] ?? '')) ?></p>
                        <h3><?= e((string) ($post['title'] ?? 'Sin tÃ­tulo')) ?></h3>
                    </header>
                    <div class="articulo-cuerpo">
                        <p><?= e((string) ($post['excerpt'] ?? '')) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="row-actions" style="margin-top:1rem;">
            <a class="btn-mini main blog-view-all-btn" href="<?= e(app_url('blog.php')) ?>">Ver todo el blog</a>
        </div>
    </section>
</main>
<?php render_service_modal_shell(); ?>
<?php render_footer(); ?>


