<?php
/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
/*  Add facebook aprication id
/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/


function vkExUnit_sns_options_init() {
	if ( false === vkExUnit_get_sns_options() ) {
		add_option( 'vkExUnit_sns_options', vkExUnit_get_sns_options_default() ); }
	vkExUnit_register_setting(
		__( 'SNS', 'vkExUnit' ), 	// tab label.
		'vkExUnit_sns_options',			// name attr
		'vkExUnit_sns_options_validate', // sanitaise function name
		'vkExUnit_add_sns_options_page'  // setting_page function name
	);
}
add_action( 'admin_init', 'vkExUnit_sns_options_init' );

function vkExUnit_get_sns_options() {
	$options			= get_option( 'vkExUnit_sns_options', vkExUnit_get_sns_options_default() );
	$options_dafault	= vkExUnit_get_sns_options_default();
	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = (isset( $options[ $key ] )) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_sns_options', $options );
}

function vkExUnit_get_sns_options_default() {
	$default_options = array(
		'fbAppId' 				=> '',
		'fbPageUrl' 			=> '',
		'ogImage' 				=> '',
		'twitterId' 			=> '',
		'enableOGTags' 			=> true,
		'enableTwitterCardTags' => true,
		'enableSnsBtns' 		=> true,
		'enableFollowMe' 		=> true,
		'SnsBtn_igronePost'     => '',
		'followMe_title'		=> 'Follow me!',
	);
	return apply_filters( 'vkExUnit_sns_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/

function vkExUnit_sns_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_sns_options_default();

	$output['fbAppId']					= $input['fbAppId'];
	$output['fbPageUrl']				= $input['fbPageUrl'];
	$output['ogImage']					= $input['ogImage'];
	$output['twitterId']				= $input['twitterId'];
	$output['SnsBtn_igronePost']		= preg_replace('/[^0-9,]/', '', $input['SnsBtn_igronePost']);
	$output['enableOGTags']  			= ( isset( $input['enableOGTags'] ) && isset( $input['enableOGTags'] ) == 'true' )? true: false;
	$output['enableTwitterCardTags']  	= ( isset( $input['enableTwitterCardTags'] ) && isset( $input['enableTwitterCardTags'] ) == 'true' )? true: false;
	$output['enableSnsBtns']   			= ( isset( $input['enableSnsBtns'] ) && isset( $input['enableSnsBtns'] ) == 'true' )? true: false;
	$output['enableFollowMe']  			= ( isset( $input['enableFollowMe'] ) && isset( $input['enableFollowMe'] ) == 'true' )? true: false;
	$output['followMe_title']			= $input['followMe_title'];

	return apply_filters( 'vkExUnit_sns_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*  set global
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_set_sns_options',1 );
function vkExUnit_set_sns_options() {
	global $vkExUnit_sns_options;
	$vkExUnit_sns_options = vkExUnit_get_sns_options();
}

/*-------------------------------------------*/
/*  Add facebook aprication id
/*-------------------------------------------*/
add_action( 'wp_footer', 'exUnit_print_fbId_script' );
function exUnit_print_fbId_script() {
?>
<div id="fb-root"></div>
<?php
$options = vkExUnit_get_sns_options();
$fbAppId = (isset( $options['fbAppId'] )) ? $options['fbAppId'] : '';
?>
<script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.3&appId=<?php echo esc_html( $fbAppId );?>";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<?php //endif;
}

$vkExUnit_sns_options = vkExUnit_get_sns_options();

require vkExUnit_get_directory() . '/plugins/sns/function_fbPagePlugin.php';

if ( $vkExUnit_sns_options['enableOGTags'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_og.php'; }
if ( $vkExUnit_sns_options['enableSnsBtns'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_snsBtns.php'; }
if ( $vkExUnit_sns_options['enableTwitterCardTags'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_twitterCard.php'; }
if ( $vkExUnit_sns_options['enableFollowMe'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_follow.php'; }

require vkExUnit_get_directory() . '/plugins/sns/function_meta_box.php';

/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_sns_options_page() {
	require dirname( __FILE__ ) . '/sns_admin.php';
	?>
	<?php
}
