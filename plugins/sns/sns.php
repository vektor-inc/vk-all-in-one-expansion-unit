<?php
/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
/*  set global $vkExUnit_sns_options
/*-------------------------------------------*/
/*  Add facebook aprication id
/*-------------------------------------------*/
/*  SNSアイコンに出力するCSSを出力する関数
/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/


function veu_sns_options_init() {
	if ( false === veu_get_sns_options() ) {
		add_option( 'vkExUnit_sns_options', veu_get_sns_options_default() );
	}
	vkExUnit_register_setting(
		__( 'SNS', 'vkExUnit' ), 	// tab label.
		'vkExUnit_sns_options',			// name attr
		'vkExUnit_sns_options_validate', // sanitaise function name
		'vkExUnit_add_sns_options_page'  // setting_page function name
	);
}
add_action( 'vkExUnit_package_init', 'veu_sns_options_init' );

function veu_get_sns_options() {
	$options			= get_option( 'vkExUnit_sns_options', veu_get_sns_options_default() );
	$options_dafault	= veu_get_sns_options_default();
	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = (isset( $options[ $key ] )) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_sns_options', $options );
}

function veu_get_sns_options_default() {
	$default_options = array(
		'fbAppId' 									=> '',
		'fbPageUrl' 								=> '',
		'ogImage' 									=> '',
		'twitterId' 								=> '',
		'enableOGTags' 							=> true,
		'enableTwitterCardTags' 		=> true,
		'enableSnsBtns' 						=> true,
		'snsBtn_exclude_post_types' => array( 'post' => '', 'page' => '' ),
		'snsBtn_ignorePosts'				=> '',
		'snsBtn_bg_fill_not'				=> false,
		'snsBtn_color'							=> false,
		'enableFollowMe' 						=> true,
		'followMe_title'						=> 'Follow me!',
		'useFacebook'								=> true,
		'useTwitter'								=> true,
		'useHatena'									=> true,
		'usePocket'									=> true,
		'useLine'										=> true,
	);
	return apply_filters( 'vkExUnit_sns_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/

function vkExUnit_sns_options_validate( $input ) {
	$output = $defaults = veu_get_sns_options_default();

	$output['fbAppId']										= esc_attr( $input['fbAppId'] );
	$output['fbPageUrl']									= esc_url( $input['fbPageUrl'] );
	$output['ogImage']										= esc_url( $input['ogImage'] );
	$output['twitterId']									= esc_attr( $input['twitterId'] );
	$output['snsBtn_ignorePosts']					= preg_replace('/[^0-9,]/', '', $input['snsBtn_ignorePosts']);
	$output['enableOGTags']  							= ( isset( $input['enableOGTags'] ) && $input['enableOGTags'] ) ? true: false;
	$output['enableTwitterCardTags']  		= ( isset( $input['enableTwitterCardTags'] ) && $input['enableTwitterCardTags'] ) ? true: false;
	$output['enableSnsBtns']   						= ( isset( $input['enableSnsBtns'] ) && $input['enableSnsBtns'] ) ? true: false;
	$output['snsBtn_exclude_post_types']	= ( isset( $input['snsBtn_exclude_post_types'] ) ) ? $input['snsBtn_exclude_post_types'] : '';
	$output['enableFollowMe']  						= ( isset( $input['enableFollowMe'] ) && $input['enableFollowMe'] )? true: false;
	$output['followMe_title']							= $input['followMe_title'];
	$output['useFacebook']								= ( isset( $input['useFacebook'] ) && $input['useFacebook'] == 'true' );
	$output['useTwitter']									= ( isset( $input['useTwitter'] ) && $input['useTwitter'] == 'true' );
	$output['useHatena']									= ( isset( $input['useHatena'] ) && $input['useHatena'] == 'true' );
	$output['usePocket']									= ( isset( $input['usePocket'] ) && $input['usePocket'] == 'true' );
	$output['useLine']										= ( isset( $input['useLine'] ) && $input['useLine'] == 'true' );

	/*
	SNSボタンの塗りつぶし関連は管理画面に値がないので、カスタマイザーで保存された値を入れる必要がある
	既に保存されている値をアップデート用にそのまま返すだけなのでサニタイズしていない
	 */
	$options_old = get_option( 'vkExUnit_sns_options');
	$output['snsBtn_bg_fill_not']	=	$options_old['snsBtn_bg_fill_not'];
	$output['snsBtn_color']	=	$options_old['snsBtn_color'];

	return apply_filters( 'vkExUnit_sns_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*  set global $vkExUnit_sns_options
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_set_sns_options',1 );
function vkExUnit_set_sns_options() {
	global $vkExUnit_sns_options;
	$vkExUnit_sns_options = veu_get_sns_options();
}

/*-------------------------------------------*/
/*  Add facebook aprication id
/*-------------------------------------------*/
add_action( 'wp_footer', 'exUnit_print_fbId_script' );
function exUnit_print_fbId_script() {
?>
<div id="fb-root"></div>
<?php
$options = veu_get_sns_options();
$fbAppId = (isset( $options['fbAppId'] )) ? $options['fbAppId'] : '';
?>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/<?php echo esc_attr(_x('en_US', 'facebook language code', 'vkExUnit'));?>/sdk.js#xfbml=1&version=v2.9&appId=<?php echo esc_html( $fbAppId );?>";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<?php //endif;
}

$vkExUnit_sns_options = veu_get_sns_options();

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

/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_sns' );
function veu_customize_panel_activation_sns(){
	return true;
}

if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_sns' );
}

function veu_customize_register_sns( $wp_customize ) {

 	/*-------------------------------------------*/
 	/*	SNS Settings
 	/*-------------------------------------------*/
 	$wp_customize->add_section( 'veu_sns_setting', array(
 		'title'				=> __('SNS Settings', 'vkExUnit'),
 		'priority'			=> 1000,
 		'panel'				=> 'veu_setting',
 	) );

   // Bin bg fill
 	$wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
 		'default'			=> false,
     'type'				=> 'option', // 保存先 option or theme_mod
 		'capability'		=> 'edit_theme_options',
 		'sanitize_callback' => 'veu_sanitize_boolean',
 	) );

 	$wp_customize->add_control( 'snsBtn_bg_fill_not', array(
 		'label'		=> __( 'No background', 'vkExUnit' ),
 		'section'	=> 'veu_sns_setting',
 		'settings'  => 'vkExUnit_sns_options[snsBtn_bg_fill_not]',
 		'type'		=> 'checkbox',
 		'priority'	=> 1,
 	) );

   // Btn color
   $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_color]', array(
 		'default'			=> false,
     'type'				=> 'option', // 保存先 option or theme_mod
 		'capability'		=> 'edit_theme_options',
 		'sanitize_callback' => 'sanitize_hex_color',
 	) );

   $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'snsBtn_color', array(
     'label'    => __('Btn color', 'vkExUnit'),
     'section'  => 'veu_sns_setting',
     'settings' => 'vkExUnit_sns_options[snsBtn_color]',
     'priority' => 2,
   )));

   // $wp_customize->get_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]' )->transport        = 'postMessage';

   /*-------------------------------------------*/
 	/*	Add Edit Customize Link Btn
 	/*-------------------------------------------*/
   $wp_customize->selective_refresh->add_partial( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
     'selector' => '.veu_socialSet',
     'render_callback' => '',
   ) );
 }
