<?php get_header(); ?>

<main class="slc-main-content">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            ?>
            <div style="margin: 2em 0;">
                <h1 style="font-family: 'Montserrat', sans-serif; font-size: 2.5rem; margin-bottom: 20px;"><?php the_title(); ?></h1>
                <?php the_content(); ?>
            </div>
            <?php
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>
