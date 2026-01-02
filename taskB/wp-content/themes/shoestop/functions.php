<?php
function shoestop_scripts() {
    wp_enqueue_style( 'shoestop-style', get_stylesheet_uri() );
    wp_enqueue_script( 'shoestop-font-awesome', 'https://kit.fontawesome.com/06c135d702.js', array(), null, false );
}
add_action( 'wp_enqueue_scripts', 'shoestop_scripts' );
