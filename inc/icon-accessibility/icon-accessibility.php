<?php

require_once 'class-icon-accessibility.php';

add_filter('the_content', array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ));
add_filter('render_block',  array( 'VEU_Icon_Accessibility', 'add_aria_hidden_to_fontawesome' ), 10, 2);
