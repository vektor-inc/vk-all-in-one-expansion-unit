<?php

add_action('wp_dashboard_setup', 'vkExUnit_disable_dash_hook', 1);
function vkExUnit_disable_dash_hook(){
    remove_action( 'wp_dashboard_setup', 'vkExUnit_dashboard_widget' );
}
