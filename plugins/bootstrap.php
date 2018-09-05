<?php


function vkExUnit_bootstrap_customize_register( $wp_customize ) {

	if ( is_null( $wp_customize->get_section( 'vkExUnit_settings' ) ) ) {
		$wp_customize->add_section(
			'vkExUnit_settings', array(
				'title'    => veu_get_short_name() . ' ' . __( 'Settings', 'vkExUnit' ),
				'priority' => 500,
			)
		);
	}

	$wp_customize->add_setting(
		'vkExUnit_colors[color_key]', array(
			'default'           => '#337ab7',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_setting(
		'vkExUnit_colors[color_key_dark]', array(
			'default'           => '#2e6da4',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize, 'color_key', array(
				'label'    => __( 'Key color', 'vkExUnit' ),
				'section'  => 'vkExUnit_settings',
				'settings' => 'vkExUnit_colors[color_key]',
				'priority' => 502,
			)
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize, 'color_key_dark', array(
				'label'    => __( 'Key color(dark)', 'vkExUnit' ),
				'section'  => 'vkExUnit_settings',
				'settings' => 'vkExUnit_colors[color_key_dark]',
				'priority' => 503,
			)
		)
	);
}
add_action( 'customize_register', 'vkExUnit_bootstrap_customize_register' );


function vkExUnit_bootstrap_custom_keycolor() {
	$options = get_option( 'lightning_theme_options' );
	if ( empty( $options ) ) {
		$options = get_option( 'vkExUnit_colors' );
	}

	$color_key      = ( ! empty( $options['color_key'] ) ) ? esc_html( $options['color_key'] ) : '#337ab7 ';
	$color_key_dark = ( ! empty( $options['color_key_dark'] ) ) ? esc_html( $options['color_key_dark'] ) : '#2e6da4 ';
	?>
<!-- [ <?php echo esc_html( veu_get_name() ); ?> Common ] -->
<style type="text/css">
.veu_color_txt_key { color:<?php echo $color_key_dark; ?> ; }
.veu_color_bg_key { background-color:<?php echo $color_key_dark; ?> ; }
.veu_color_border_key { border-color:<?php echo $color_key_dark; ?> ; }
a { color:<?php echo $color_key_dark; ?> ; }
a:hover { color:<?php echo $color_key; ?> ; }
.btn-default { border-color:<?php echo $color_key; ?>;color:<?php echo $color_key; ?>;}
.btn-default:focus,
.btn-default:hover { border-color:<?php echo $color_key; ?>;background-color: <?php echo $color_key; ?>; }
.btn-primary { background-color:<?php echo $color_key; ?>;border-color:<?php echo $color_key_dark; ?>; }
.btn-primary:focus,
.btn-primary:hover { background-color:<?php echo $color_key_dark; ?>;border-color:<?php echo $color_key; ?>; }
</style>
<!-- [ / <?php echo esc_html( veu_get_name() ); ?> Common ] -->
	<?php

}
add_action( 'wp_head', 'vkExUnit_bootstrap_custom_keycolor' );
