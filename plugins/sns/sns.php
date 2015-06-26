<?php
/*-------------------------------------------*/
/*	Options Init
/*-------------------------------------------*/
/*	Add facebook aprication id
/*-------------------------------------------*/
/*	Add menu
/*-------------------------------------------*/
/*	Add setting page
/*-------------------------------------------*/
/*	Options Init
/*-------------------------------------------*/


function vkExUnit_sns_options_init() {
	if ( false === vkExUnit_get_sns_options() )
		add_option( 'vkExUnit_sns_options', vkExUnit_get_sns_options_default() );
	vkExUnit_register_setting(
		'vkExUnit_sns_options_fields', 	//  Immediately following form tag of edit page.
		'vkExUnit_sns_options',			// name attr
		'vkExUnit_sns_options_validate'
	);
}
add_action( 'admin_init', 'vkExUnit_sns_options_init' );

function vkExUnit_get_sns_options() {
	$options			= get_option( 'vkExUnit_sns_options', vkExUnit_get_sns_options_default() );
	$options_dafault	= vkExUnit_get_sns_options_default();
	foreach ($options_dafault as $key => $value) {
		$options[$key] = (isset($options[$key])) ? $options[$key] : $options_dafault[$key];
	}
	return apply_filters( 'vkExUnit_sns_options', $options );
}

function vkExUnit_get_sns_options_default() {
	$default_options = array(
		'fbAppId' => '',
		'fbPageUrl' => '',
		'ogTagDisplay' => 'og_on',
		'ogImage' => '',
		'twitterId' => '',
	);
	return apply_filters( 'vkExUnit_sns_options_default', $default_options );
}

/*-------------------------------------------*/
/*	validate
/*-------------------------------------------*/

function vkExUnit_sns_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_sns_options_default();

	$output['fbAppId']			= $input['fbAppId'];
	$output['fbPageUrl']		= $input['fbPageUrl'];
	$output['ogTagDisplay']		= $input['ogTagDisplay'];
	$output['ogImage']			= $input['ogImage'];
	$output['twitterId']		= $input['twitterId'];

	return apply_filters( 'vkExUnit_sns_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*	set global
/*-------------------------------------------*/
add_action('wp_head', 'vkExUnit_set_sns_options',1 );
function vkExUnit_set_sns_options() {
	global $vkExUnit_sns_options;
	$vkExUnit_sns_options = vkExUnit_get_sns_options();
}

/*-------------------------------------------*/
/*	Add facebook aprication id
/*-------------------------------------------*/
add_action('wp_footer', 'exUnit_print_fbId_script');
function exUnit_print_fbId_script(){
?>
<div id="fb-root"></div>
<?php
$options = vkExUnit_get_sns_options();
$fbAppId = (isset($options['fbAppId'])) ? $options['fbAppId'] : '';
?>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.3&appId=<?php echo esc_html($fbAppId);?>";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<?php //endif;
}


require vkExUnit_get_directory() . '/plugins/sns/function_fbPagePlugin.php';
require vkExUnit_get_directory() . '/plugins/sns/function_og.php';
require vkExUnit_get_directory() . '/plugins/sns/function_snsBtns.php';
require vkExUnit_get_directory() . '/plugins/sns/function_twitterCard.php';
require vkExUnit_get_directory() . '/plugins/sns/function_follow.php';

/*-------------------------------------------*/
/*	Add setting page
/*-------------------------------------------*/

function vkExUnit_add_sns_options_page(){
	require dirname( __FILE__ ) . '/sns_admin.php';
	?>
	<?php
}

add_action( 'vkExUnit_main_config' , 'vkExUnit_add_sns_options_page' );