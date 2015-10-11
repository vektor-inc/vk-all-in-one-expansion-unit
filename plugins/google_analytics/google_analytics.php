<?php
/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_ga_options_page() {
	require dirname( __FILE__ ) . '/ga_admin.php';
}

/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
function vkExUnit_ga_options_init() {
	if ( false === vkExUnit_get_ga_options() ) {
		add_option( 'vkExUnit_ga_options', vkExUnit_get_ga_options_default() ); }

	vkExUnit_register_setting(
		__( 'Google Analytics Settings', 'vkExUnit' ), 	//  Immediately following form tag of edit page.
		'vkExUnit_ga_options',			// name attr
		'vkExUnit_ga_options_validate',
		'vkExUnit_add_ga_options_page'
	);
}
add_action( 'admin_init', 'vkExUnit_ga_options_init' );

function vkExUnit_get_ga_options() {
	$options			= get_option( 'vkExUnit_ga_options', vkExUnit_get_ga_options_default() );
	$options_dafault	= vkExUnit_get_ga_options_default();
	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = (isset( $options[ $key ] )) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_ga_options', $options );
}

function vkExUnit_get_ga_options_default() {
	$default_options = array(
		'gaId' => '',
		'gaType' => 'gaType_universal',
	);
	return apply_filters( 'vkExUnit_ga_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
function vkExUnit_ga_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_ga_options_default();

	$paras = array(
			'gaId',
			'gaType',
			);

	foreach ( $paras as $key => $value ) {
		$output[ $value ] = (isset( $input[ $value ] )) ? $input[ $value ] : '';
	}

	return apply_filters( 'vkExUnit_ga_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*  GoogleAnalytics
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_googleAnalytics', 10000 );
function vkExUnit_googleAnalytics() {
	$options = vkExUnit_get_ga_options();
	$gaId = esc_html( $options['gaId'] );
	$gaType = esc_html( $options['gaType'] );
	if ( $gaId ) {

		if ( ( ! $gaType) || ($gaType == 'gaType_normal') || ($gaType == 'gaType_both') ) {  ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-<?php echo $gaId ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
		<?php }
		if ( ($gaType == 'gaType_both') || ($gaType == 'gaType_universal') ) {
			$domainUrl = site_url();
			$delete = array( 'http://', 'https://' );
			$domain = str_replace( $delete, '', $domainUrl ); ?>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-<?php echo $gaId ?>', '<?php echo $domain ?>');
ga('send', 'pageview');
</script>
<?php
		}
	}
}
