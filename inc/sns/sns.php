<?php
/*
  Options Init
  validate
  set global $vkExUnit_sns_options
  Add facebook aprication id
  SNSアイコンに出力するCSSを出力する関数
  Add setting page
  Add Customize Panel
/*-------------------------------------------*/

require_once( dirname( __FILE__ ) . '/sns_customizer.php' );

function veu_sns_options_init() {
	if ( false === veu_get_sns_options() ) {
		add_option( 'vkExUnit_sns_options', veu_get_sns_options_default() );
	}
	vkExUnit_register_setting(
		__( 'SNS', 'vk-all-in-one-expansion-unit' ),    // tab label.
		'vkExUnit_sns_options',         // name attr
		'vkExUnit_sns_options_validate', // sanitaise function name
		'vkExUnit_add_sns_options_page'  // setting_page function name
	);
}
add_action( 'veu_package_init', 'veu_sns_options_init' );

function veu_get_sns_options() {
	$options         = get_option( 'vkExUnit_sns_options', veu_get_sns_options_default() );
	$options_dafault = veu_get_sns_options_default();
	$options         = wp_parse_args( $options, $options_dafault );
	return apply_filters( 'vkExUnit_sns_options', $options );
}

function veu_get_sns_options_default() {
	$default_options = array(
		'fbAppId'                     => '',
		'fbPageUrl'                   => '',
		'ogImage'                     => '',
		'twitterId'                   => '',
		'enableOGTags'                => true,
		'snsTitle_use_only_postTitle' => false,
		'enableTwitterCardTags'       => true,
		'enableSnsBtns'               => true,
		'snsBtn_exclude_post_types'   => array(
			'post' => '',
			'page' => '',
		),
		'snsBtn_position'             => array(
			'before' => false,
			'after'  => true,
		),
		'snsBtn_ignorePosts'          => '',
		'snsBtn_bg_fill_not'          => false,
		'snsBtn_color'                => false,
		'enableFollowMe'              => true,
		'followMe_title'              => 'Follow me!',
		'useFacebook'                 => true,
		'useTwitter'                  => true,
		'useHatena'                   => true,
		'usePocket'                   => true,
		'useLine'                     => true,
	);
	return apply_filters( 'vkExUnit_sns_options_default', $default_options );
}

/**
 * [veu_get_the_sns_title description]
 *
 * @return [type] [description]
 */
function veu_get_the_sns_title( $post_id = '' ) {

	if ( ! $post_id ) {
		$post_id = get_the_id();
	}
	$options = veu_get_sns_options();

	$title = '';

	if ( is_front_page() ) {
		$options = get_option( 'vkExUnit_wp_title' );
		if ( ! empty( $options['extend_frontTitle'] ) && veu_package_is_enable( 'wpTitle' ) ) {
			$title = $options['extend_frontTitle'];
		} else {
			$title = get_bloginfo( 'name' );
		}
	} elseif ( is_singular() ) {
		$title = get_post_meta( $post_id, 'vkExUnit_sns_title', true );
	}

	if ( ! $title ) {
		if ( is_singular() && $options['snsTitle_use_only_postTitle'] ) {
			$title = get_the_title( $post_id );
		} else {
			$title = wp_title( '', false );
		}
	}

	return strip_tags( $title );
}

/*
  validate
/*-------------------------------------------*/

function vkExUnit_sns_options_validate( $input ) {
	$output = $defaults = veu_get_sns_options_default();

	$output['fbAppId']                     = esc_attr( $input['fbAppId'] );
	$output['fbPageUrl']                   = esc_url( $input['fbPageUrl'] );
	$output['ogImage']                     = esc_url( $input['ogImage'] );
	$output['twitterId']                   = esc_attr( $input['twitterId'] );
	$output['snsBtn_ignorePosts']          = preg_replace( '/[^0-9,]/', '', $input['snsBtn_ignorePosts'] );
	$output['snsTitle_use_only_postTitle'] = ( isset( $input['snsTitle_use_only_postTitle'] ) && $input['snsTitle_use_only_postTitle'] ) ? true : false;
	$output['enableOGTags']                = ( isset( $input['enableOGTags'] ) && $input['enableOGTags'] ) ? true : false;
	$output['enableTwitterCardTags']       = ( isset( $input['enableTwitterCardTags'] ) && $input['enableTwitterCardTags'] ) ? true : false;
	$output['enableSnsBtns']               = ( isset( $input['enableSnsBtns'] ) && $input['enableSnsBtns'] ) ? true : false;
	$output['snsBtn_exclude_post_types']   = ( isset( $input['snsBtn_exclude_post_types'] ) ) ? $input['snsBtn_exclude_post_types'] : '';
	$output['snsBtn_position']             = ( isset( $input['snsBtn_position'] ) ) ? $input['snsBtn_position'] : '';
	$output['enableFollowMe']              = ( isset( $input['enableFollowMe'] ) && $input['enableFollowMe'] ) ? true : false;
	$output['followMe_title']              = $input['followMe_title'];
	$output['useFacebook']                 = ( isset( $input['useFacebook'] ) && $input['useFacebook'] == 'true' );
	$output['useTwitter']                  = ( isset( $input['useTwitter'] ) && $input['useTwitter'] == 'true' );
	$output['useHatena']                   = ( isset( $input['useHatena'] ) && $input['useHatena'] == 'true' );
	$output['usePocket']                   = ( isset( $input['usePocket'] ) && $input['usePocket'] == 'true' );
	$output['useLine']                     = ( isset( $input['useLine'] ) && $input['useLine'] == 'true' );

	/*
	SNSボタンの塗りつぶし関連は管理画面に値がないので、カスタマイザーで保存された値を入れる必要がある
	既に保存されている値をアップデート用にそのまま返すだけなのでサニタイズしていない
	 */
	$options_old                  = get_option( 'vkExUnit_sns_options' );
	$output['snsBtn_bg_fill_not'] = ( ! empty( $options_old['snsBtn_bg_fill_not'] ) ) ? $options_old['snsBtn_bg_fill_not'] : '';
	$output['snsBtn_color']       = ( ! empty( $options_old['snsBtn_color'] ) ) ? $options_old['snsBtn_color'] : '';

	return apply_filters( 'vkExUnit_sns_options_validate', $output, $input, $defaults );
}

/*
  set global $vkExUnit_sns_options
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_set_sns_options', 1 );
function vkExUnit_set_sns_options() {
	global $vkExUnit_sns_options;
	$vkExUnit_sns_options = veu_get_sns_options();
}

/*
  Add facebook aprication id
/*-------------------------------------------*/
add_action( 'wp_footer', 'exUnit_print_fbId_script' );
function exUnit_print_fbId_script() {
?>
<div id="fb-root"></div>
<?php
$options = veu_get_sns_options();
$fbAppId = ( isset( $options['fbAppId'] ) ) ? $options['fbAppId'] : '';
?>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/<?php echo esc_attr( _x( 'en_US', 'facebook language code', 'vk-all-in-one-expansion-unit' ) ); ?>/sdk.js#xfbml=1&version=v2.9&appId=<?php echo esc_html( $fbAppId ); ?>";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<?php
	// endif;
}

$vkExUnit_sns_options = veu_get_sns_options();

require dirname( __FILE__ ) . '/widget-fb-page-plugin.php';
require dirname( __FILE__ ) . '/widget-twitter.php';


/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
admin_menu のタイミングで読み込んでいる
 */
add_action(
	'admin_menu', function() {
		require dirname( __FILE__ ) . '/class-veu-metabox-sns-title.php';
	}
);

if ( $vkExUnit_sns_options['enableOGTags'] == true ) {
	require dirname( __FILE__ ) . '/function_og.php';
}
if ( $vkExUnit_sns_options['enableSnsBtns'] == true ) {
	// シェアボタンを表示する設定の読み込み
	require dirname( __FILE__ ) . '/function_snsBtns.php';
	/*
	VEU_Metabox 内の get_post_type が実行タイミングによっては
	カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
	admin_menu のタイミングで読み込んでいる
	 */
	add_action(
		'admin_menu', function() {
			require dirname( __FILE__ ) . '/class-veu-metabox-sns-button.php';
		}
	);
}
if ( $vkExUnit_sns_options['enableTwitterCardTags'] == true ) {
	require dirname( __FILE__ ) . '/function_twitterCard.php';
}
if ( $vkExUnit_sns_options['enableFollowMe'] == true ) {
	require dirname( __FILE__ ) . '/function_follow.php';
}




/*
  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_sns_options_page() {
	require dirname( __FILE__ ) . '/sns_admin.php';

}
