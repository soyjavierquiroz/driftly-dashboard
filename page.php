<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div class="d-page-card">
    <div class="d-page-card__header">
        <div>
            <h1 class="d-page-card__title">
                <?php the_title(); ?>
            </h1>
            <p class="d-page-card__subtitle">
                Contenido de página estándar dentro del layout Driftly.
            </p>
        </div>
    </div>

    <div class="d-page-card__body">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</div>

<?php
get_footer();
