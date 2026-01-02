<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div class="container">
    <header>
        <a href="<?php echo home_url(); ?>" class="logo">Club<span class="stop">Council</span></a>

        <nav>
            <ul>
                <li><a href="<?php echo home_url('/'); ?>">Home</a></li>
                <li><a href="<?php echo home_url('/clubs'); ?>">Clubs</a></li>
                <li><a href="<?php echo home_url('/events'); ?>">Events</a></li>
            </ul>
        </nav>
    </header>
</div>
