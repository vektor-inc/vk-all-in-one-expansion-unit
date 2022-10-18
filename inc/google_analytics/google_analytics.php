<?php
/*
  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_ga_options_page() {
	// require dirname( __FILE__ ) . '/ga_admin.php';
	require_once dirname( __FILE__ ) . '/ga_admin.php';
}

/*
  Options Init
/*-------------------------------------------*/
function vkExUnit_ga_options_init() {
	if ( false === vkExUnit_get_ga_options() ) {
		add_option( 'vkExUnit_ga_options', vkExUnit_get_ga_options_default() );
	}

	vkExUnit_register_setting(
		__( 'Google Analytics Settings', 'vk-all-in-one-expansion-unit' ),  // Immediately following form tag of edit page.
		'vkExUnit_ga_options',          // name attr
		'vkExUnit_ga_options_validate',
		'vkExUnit_add_ga_options_page'
	);
}
add_action( 'veu_package_init', 'vkExUnit_ga_options_init' );

function vkExUnit_get_ga_options() {
	$options         = get_option( 'vkExUnit_ga_options', vkExUnit_get_ga_options_default() );
	$options_dafault = vkExUnit_get_ga_options_default();

	// UA・GA4 両対応時の互換処理を追加
	if ( ! empty( $options['gaId'] ) || ! empty( $output['gaType'] ) ) {
		if ( preg_match( '/G-/', $options['gaId'] ) ) {
			$options['gaId-GA4'] = $options['gaId'];
		} elseif ( preg_match( '/UA-/', $options['gaId'] ) ) {
			$options['gaId-UA'] = $options['gaId'];
		} else {
			$options['gaId-UA'] = 'UA-' . $options['gaId'];
		}
		if ( isset( $options['gaId'] ) ){
			unset( $options['gaId'] );
		}
		if ( isset( $options['gaType'] ) ){
			unset( $options['gaType'] );
		}
		update_option( 'vkExUnit_ga_options', $options );
	}

	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = ( isset( $options[ $key ] ) ) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_ga_options', $options );
}

function vkExUnit_get_ga_options_default() {
	$default_options = array(
		'gaId-GA4'        => '',
		'gaId-UA'         => '',
		'disableLoggedin' => false,
	);

	return apply_filters( 'vkExUnit_ga_options_default', $default_options );
}

/*
  validate
/*-------------------------------------------*/
function vkExUnit_ga_options_validate( $input ) {
	// デフォルト値を取得
	$defaults = vkExUnit_get_ga_options_default();
	// 入力された値とデフォルト値をマージ
	$input = wp_parse_args( $input, $defaults );

	// 入力値をサニタイズ
	$output['gaId-GA4']        = stripslashes( esc_html( $input['gaId-GA4'] ) );
	$output['gaId-UA']         = stripslashes( esc_html( $input['gaId-UA'] ) );
	$output['disableLoggedin'] = ( $input['disableLoggedin'] ) ? true : false;

	return apply_filters( 'vkExUnit_ga_options_validate', $output, $input, $defaults );
}

/*
  GoogleAnalytics
/*-------------------------------------------*/
function make_ga_script() {
	$options  = vkExUnit_get_ga_options();
	$gaId_GA4 = esc_html( $options['gaId-GA4'] );
	$gaId_UA  = esc_html( $options['gaId-UA'] );

	// メインの GAID を設定
	$gaId_main = '';
	if ( ! empty( $gaId_GA4 ) ) {
		$gaId_main = $gaId_GA4;
	} elseif ( ! empty( $gaId_UA ) ) {
		$gaId_main = $gaId_UA;
	}

	$disableLoggedin = ( $options['disableLoggedin'] ) ? true : false;

	$script = '';

	if ( ! empty( $gaId_main ) && ! ( $disableLoggedin && is_user_logged_in() ) ) {
		$script .= '<!-- Google tag (gtag.js) -->';
		$script .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $gaId_main . '"></script>';
		$script .= '<script>';
		$script .= 'window.dataLayer = window.dataLayer || [];';
		$script .= 'function gtag(){dataLayer.push(arguments);}';
		$script .= 'gtag(\'js\', new Date());';
		if ( ! empty( $gaId_GA4 ) ) {
			$script .= 'gtag(\'config\', \'' . $gaId_GA4 . '\');';
		}
		if ( ! empty( $gaId_UA ) ) {
			$script .= 'gtag(\'config\', \'' . $gaId_UA . '\');';
		}
		$script .= '</script>';
		return $script;
	}
}

function load_ga_script() {
	echo make_ga_script() . PHP_EOL;
}
add_action( 'wp_head', 'load_ga_script', 0 );
