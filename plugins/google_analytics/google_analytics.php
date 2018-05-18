<?php
/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_ga_options_page() {
	// require dirname( __FILE__ ) . '/ga_admin.php';
	require_once( dirname( __FILE__ ) . '/ga_admin.php' );
}
	// カスタマイザー読み込み
	require_once( dirname( __FILE__ ) . '/ga_customizer.php' );


/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
function vkExUnit_ga_options_init() {
	if ( false === vkExUnit_get_ga_options() ) {
		add_option( 'vkExUnit_ga_options', vkExUnit_get_ga_options_default() );
	}

	vkExUnit_register_setting(
		__( 'Google Analytics Settings', 'vkExUnit' ),  //  Immediately following form tag of edit page.
		'vkExUnit_ga_options',          // name attr
		'vkExUnit_ga_options_validate',
		'vkExUnit_add_ga_options_page'
	);
}
add_action( 'vkExUnit_package_init', 'vkExUnit_ga_options_init' );

function vkExUnit_get_ga_options() {
	$options         = get_option( 'vkExUnit_ga_options', vkExUnit_get_ga_options_default() );
	$options_dafault = vkExUnit_get_ga_options_default();
	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = ( isset( $options[ $key ] ) ) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_ga_options', $options );
}

function vkExUnit_get_ga_options_default() {
	$default_options = array(
		'gaId'   => '',
		'gaType' => 'gaType_gtag',
	);
	return apply_filters( 'vkExUnit_ga_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
function vkExUnit_ga_options_validate( $input ) {
	// デフォルト値を取得
	$defaults = vkExUnit_get_ga_options_default();
	// 入力された値とデフォルト値をマージ
	$input = wp_parse_args( $input, $defaults );

	// 入力値をサニタイズ
	$output['gaId']   = esc_html( $input['gaId'] );
	$output['gaType'] = esc_html( $input['gaType'] );

	return apply_filters( 'vkExUnit_ga_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*  GoogleAnalytics
/*-------------------------------------------*/

add_action( 'init', 'vkExUnit_googleAnalytics_load' );
function vkExUnit_googleAnalytics_load() {
	$options = vkExUnit_get_ga_options();
	$gaType  = esc_html( $options['gaType'] );
	if ( $gaType == 'gaType_gtag' ) {
		$priority = 0;
	} else {
		$priority = 10000;
	}
	add_action( 'wp_head', 'vkExUnit_googleAnalytics', $priority );
}

function vkExUnit_googleAnalytics() {
	$options = vkExUnit_get_ga_options();
	$gaId    = esc_html( $options['gaId'] );
	$gaType  = esc_html( $options['gaType'] );
	if ( $gaId ) {

		if ( $gaType == 'gaType_universal' ) {
			$domainUrl = home_url();
			$delete    = array( 'http://', 'https://' );
			$domain    = str_replace( $delete, '', $domainUrl ); ?>
			<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-<?php echo $gaId; ?>', '<?php echo $domain; ?>');
			ga('send', 'pageview');
			</script>
			<?php

		} elseif ( $gaType == 'gaType_normal' ) {
		?>
			<script type="text/javascript">

			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-<?php echo $gaId; ?>']);
			  _gaq.push(['_trackPageview']);

			  (function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();

			</script>
<?php
		} else {
			// $gaType == 'gaType_gtag'
			?>
			<!-- Global site tag (gtag.js) - Google Analytics -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=UA-<?php echo $gaId; ?>"></script>
		<script>
		 window.dataLayer = window.dataLayer || [];
		 function gtag(){dataLayer.push(arguments);}
		 gtag('js', new Date());

		gtag('config', 'UA-<?php echo $gaId; ?>');
		</script>
	<?php
		}
	} // if ( $gaId ) {
}
