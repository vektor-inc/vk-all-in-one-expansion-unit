<?php
/*
  footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );
function veu_add_pagetop() {
  $option = get_option( 'vkExUnit_pagetop' );
  if ( ! wp_is_mobile() || ! empty( $option['display_mobile'] ) ) {
    echo '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>';
  }
}


add_action( 'customize_register', 'veu_customize_register_pagetop' );
function veu_customize_register_pagetop( $wp_customize ) {

	/*
		Page Top setting
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'veu_pagetop_setting',
		array(
			'title'    => __( 'Page Top Button', 'vk-all-in-one-expansion-unit' ),
			'priority' => 10000,
			'panel'    => 'veu_setting',
		)
	);

	$wp_customize->add_setting(
		'vkExUnit_pagetop[display_mobile]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'vkExUnit_pagetop[display_mobile]',
		array(
      'label'       => __( 'Display on mobile devices', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_pagetop_setting',
			'settings'    => 'vkExUnit_pagetop[display_mobile]',
			'type'        => 'checkbox',
		)
  );
  
  $wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[display_mobile]', array(
			'selector'        => '.page_top_btn',
			'render_callback' => '',
			'supports' => [],
		)
	);

}