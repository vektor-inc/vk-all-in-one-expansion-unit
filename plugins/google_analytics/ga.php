<?php
/*-------------------------------------------*/
/*	Add menu
/*-------------------------------------------*/




/*-------------------------------------------*/
/*	Add menu
/*-------------------------------------------*/
function vk_add_ga_menu() {
	$capability_required = vkExUnit_get_capability_required();
	$custom_page = add_submenu_page(
		'vk_setting_page',			// parent
		'GoogleAnalytics setting',	// Name of page
		'GA setting',				// Label in menu
		$capability_required,		// Capability required　このメニューページを閲覧・使用するために最低限必要なユーザーレベルまたはユーザーの種類と権限。
		'vk_ga_options',			// ユニークなこのサブメニューページの識別子
		'add_vk_ga_options'			// メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page )
	return;
}
add_action( 'admin_menu', 'vk_add_ga_menu' );

/*-------------------------------------------*/
/*	Add setting page
/*-------------------------------------------*/

function add_vk_ga_options(){
	require dirname( __FILE__ ) . '/ga_admin.php';
}

function biz_vektor_ga_options_init() {
	if ( false === biz_vektor_get_ga_options() )
		add_option( 'biz_vektor_ga_options', biz_vektor_get_ga_options_default() );

	register_setting(
		'biz_vektor_ga_options_fields', 	//  Immediately following form tag of edit page.
		'biz_vektor_ga_options',			// name attr
		'biz_vektor_ga_options_validate'
	);
}
add_action( 'admin_init', 'biz_vektor_ga_options_init' );

function biz_vektor_get_ga_options() {
	return get_option( 'biz_vektor_ga_options', biz_vektor_get_ga_options_default() );
}

function biz_vektor_get_ga_options_default() {
	$default_options = array(

	);
	return apply_filters( 'biz_vektor_default_options', $default_options );
}


/*-------------------------------------------*/
/*	Set option default
/*	$opstions_default = biz_vektor_get_ga_options_default(); に移行して順次廃止	// 0.11.0
/*-------------------------------------------*/
function biz_vektor_ga_options_default() {
	global $biz_vektor_ga_options_default;
	$biz_vektor_ga_options_default = array(
		// 'pr1_title' => __('Rich theme options', 'biz-vektor'),
	);
}

/*-------------------------------------------*/
/*	Print option
/*-------------------------------------------*/
function biz_vektor_ga_options($optionLabel) {
	$options = biz_vektor_get_ga_options();
	if ( $options[$optionLabel] != false ) { // If !='' that 0 true
		return $options[$optionLabel];
	} else {
		$options_default = biz_vektor_get_ga_options_default();
		if (isset($options_default[$optionLabel]))
		return $options_default[$optionLabel];
	}
}

/*-------------------------------------------*/
/*	validate
/*-------------------------------------------*/
// function biz_vektor_ga_options_validate( $input ) {
// 	$output = $defaults = biz_vektor_get_default_theme_options();

function biz_vektor_ga_options_validate( $input ) {
	$output = $defaults = biz_vektor_get_ga_options_default();

	$paras = array(
			'gaID',
			'gaType',
			);

	foreach ($paras as $key => $value) {
		$output[$value] = (isset($input[$value])) ? $input[$value] : '';
	}

	return apply_filters( 'biz_vektor_ga_options_validate', $output, $input, $defaults );
}


/*-------------------------------------------*/
/*	GoogleAnalytics
/*-------------------------------------------*/
add_action('wp_head', 'biz_vektor_googleAnalytics', 10000 );
function biz_vektor_googleAnalytics(){
	$options = biz_vektor_get_ga_options();
	$gaID = $options['gaID'];
	$gaType = $options['gaType'];
	if ($gaID) {

		if ((!$gaType) || ($gaType == 'gaType_normal') || ($gaType == 'gaType_both')){ ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-<?php echo $gaID ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
		<?php }
		if (($gaType == 'gaType_both') || ($gaType == 'gaType_universal')){
			$domainUrl = site_url();
			$delete = array("http://", "https://");
			$domain = str_replace($delete, "", $domainUrl); ?>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-<?php echo $gaID ?>', '<?php echo $domain ?>');
ga('send', 'pageview');
</script>
<?php
		}
	}
}