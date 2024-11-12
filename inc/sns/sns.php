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

require_once dirname( __FILE__ ) . '/sns_customizer.php';
require_once dirname( __FILE__ ) . '/block/index.php';

function veu_sns_inline_styles() {
	$dynamic_css = '
	@font-face {
		font-weight: normal;
		font-style: normal;
		font-family: "vk_sns";
		src: url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.eot?-bq20cj' . '");
		src: url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.eot?#iefix-bq20cj' . '") format("embedded-opentype"),
			url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.woff?-bq20cj' . '") format("woff"),
			url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.ttf?-bq20cj' . '") format("truetype"),
			url("' . VEU_DIRECTORY_URI . '/inc/sns/icons/fonts/vk_sns.svg?-bq20cj#vk_sns' . '") format("svg");
	}';

	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'veu-block-share-button-editor', $dynamic_css );
}
add_action( 'enqueue_block_assets', 'veu_sns_inline_styles' );

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
		'fbAccessToken'               => '',
		'ogImage'                     => '',
		'twitterId'                   => '',
		'enableOGTags'                => true,
		'snsTitle_use_only_postTitle' => false,
		'enableTwitterCardTags'       => true,
		'enableSnsBtns'               => true,
		'snsBtn_exclude_post_types'   => array(
			'post' => false,
			'page' => false,
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
		'useCopy'                     => true,
		'entry_count'                 => 'get',
		'hook_point'                  => '',
	);
	// In case of block theme
	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		$default_options['enableSnsBtns']  = false;
		$default_options['enableFollowMe'] = false;
	}
	return apply_filters( 'vkExUnit_sns_options_default', $default_options );
}

/**
 * [veu_get_the_sns_title description]
 *
 * @return [type] [description]
 */
function veu_get_the_sns_title( $post_id = '' ) {

	/*
	注意 :
	アーカイブなどのループで使われる場合を想定すると、
	is_singular() / is_single() / is_page() / is_archive() / is_front_page()
	などの条件分岐は単体では正常に動作させられない
	例） ループの中の投稿の場合、そのページ自体がsingularページであるとは限らないため is_singular() で条件分岐すると誤動作してしまう
	*/

	$title          = '';
	$site_title     = get_bloginfo( 'name' );
	$options_sns    = veu_get_sns_options();
	$page_on_front  = get_option( 'page_on_front' );
	$page_for_posts = get_option( 'page_for_posts' );
	if ( ! $post_id ) {
		$post_id = get_the_id();
	}

	// 404 ページの場合
	if ( is_404() ) {
		// OGのタイトルを投稿タイトルだけにするチェック判定
		if ( ! empty( $options_sns['snsTitle_use_only_postTitle'] ) ) {
			$title = __( 'Not found', 'vk-all-in-one-expansion-unit' );
		} else {
			$title = __( 'Not found', 'vk-all-in-one-expansion-unit' ) . ' | ' . $site_title;
		}
	}

	// 検索結果ページの場合
	elseif ( is_search() ) {
		if ( ! empty( get_search_query() ) ) {
			$search_text = sprintf( __( 'Search Results for : %s', 'vk-all-in-one-expansion-unit' ), get_search_query() );
		} else {
			$search_text = __( 'Search Results', 'vk-all-in-one-expansion-unit' );
		}
		// OGのタイトルを投稿タイトルだけにするチェック判定
		if ( ! empty( $options_sns['snsTitle_use_only_postTitle'] ) ) {
			$title = $search_text;
		} else {
			$title = $search_text . ' | ' . $site_title;
		}
	}

	// フロントページの場合
	elseif ( is_front_page() ) {
		$options_veu_wp_title = get_option( 'vkExUnit_wp_title' );
		if ( ! empty( $options_veu_wp_title['extend_frontTitle'] ) && veu_package_is_enable( 'wpTitle' ) ) {
			$title = $options_veu_wp_title['extend_frontTitle'];
		} else {
			$title = get_bloginfo( 'name' );
		}
	}

	// フロントページではなくホームページの場合.
	elseif ( is_home() ) {
		if ( get_option( 'show_on_front' ) === 'page' ) {
			// OGのタイトルを投稿タイトルだけにするチェック判定
			if ( ! empty( $options_sns['snsTitle_use_only_postTitle'] ) ) {
				$title = get_the_title( $page_for_posts );
			} else {
				$title = get_the_title( $page_for_posts ) . ' | ' . $site_title;
			}
		}
	}

	// アーカイブページの場合
	elseif ( is_archive() ) {
		// OGのタイトルを投稿タイトルだけにするチェック判定
		if ( ! empty( $options_sns['snsTitle_use_only_postTitle'] ) ) {
			$title = get_the_archive_title();
		} else {
			$title = get_the_archive_title() . ' | ' . $site_title;
		}
	}

	// 投稿詳細ページの場合
	elseif ( is_singular() ) {
		/**
		 *
		 * [ 通常 ]
		 *
		 * → 投稿タイトル + サイト名
		 */

		$title = get_the_title( $post_id ) . ' | ' . $site_title;

		/**
		 *
		 * [ OGのタイトルを投稿タイトルだけにするチェックが入っている場合 ]
		 *
		 * → 投稿タイトル
		 */
		if ( ! empty( $options_sns['snsTitle_use_only_postTitle'] ) ) {
			$title = get_the_title( $post_id );
		}

		/**
		 * [ metaboxでOG用のタイトルが別途登録されている場合 ]
		 *
		 * → OG用のタイトルを返す
		 */
		if ( ! empty( get_post_meta( $post_id, 'vkExUnit_sns_title', true ) ) ) {
			$title = get_post_meta( $post_id, 'vkExUnit_sns_title', true );
		}
	}

	/**
	 * [ どれにも当てはまらなかった場合（基本ないはず） ]
	 *
	 * → wp_title()を返す
	 */

	if ( ! $title ) {
		$title = wp_title( '', false );

	}

	return strip_tags( $title );
}

/*
  validate
/*-------------------------------------------*/

function vkExUnit_sns_options_validate( $input ) {
	$output = $defaults = veu_get_sns_options_default();

	$output['fbAppId']                     = stripslashes( esc_attr( $input['fbAppId'] ) );
	$output['fbPageUrl']                   = esc_url( $input['fbPageUrl'] );
	$output['fbAccessToken']               = stripslashes( esc_attr( $input['fbAccessToken'] ) );
	$output['ogImage']                     = esc_url( $input['ogImage'] );
	$output['twitterId']                   = stripslashes( esc_attr( $input['twitterId'] ) );
	$output['snsBtn_ignorePosts']          = preg_replace( '/[^0-9,]/', '', esc_attr( $input['snsBtn_ignorePosts'] ) );
	$output['snsTitle_use_only_postTitle'] = ( isset( $input['snsTitle_use_only_postTitle'] ) && $input['snsTitle_use_only_postTitle'] ) ? true : false;
	$output['enableOGTags']                = ( isset( $input['enableOGTags'] ) && $input['enableOGTags'] ) ? true : false;
	$output['enableTwitterCardTags']       = ( isset( $input['enableTwitterCardTags'] ) && $input['enableTwitterCardTags'] ) ? true : false;
	$output['enableSnsBtns']               = ( isset( $input['enableSnsBtns'] ) && $input['enableSnsBtns'] ) ? true : false;
	$output['snsBtn_exclude_post_types']   = ( isset( $input['snsBtn_exclude_post_types'] ) ) ? $input['snsBtn_exclude_post_types'] : '';
	$output['snsBtn_position']             = ( isset( $input['snsBtn_position'] ) ) ? $input['snsBtn_position'] : '';
	$output['enableFollowMe']              = ( isset( $input['enableFollowMe'] ) && $input['enableFollowMe'] ) ? true : false;
	$output['followMe_title']              = stripslashes( $input['followMe_title'] );
	$output['useFacebook']                 = ( isset( $input['useFacebook'] ) && $input['useFacebook'] == 'true' );
	$output['useTwitter']                  = ( isset( $input['useTwitter'] ) && $input['useTwitter'] == 'true' );
	$output['useHatena']                   = ( isset( $input['useHatena'] ) && $input['useHatena'] == 'true' );
	$output['usePocket']                   = ( isset( $input['usePocket'] ) && $input['usePocket'] == 'true' );
	$output['useCopy']                     = ( isset( $input['useCopy'] ) && $input['useCopy'] == 'true' );
	$output['useLine']                     = ( isset( $input['useLine'] ) && $input['useLine'] == 'true' );
	$output['entry_count']                 = esc_attr( $input['entry_count'] );

	$output['hook_point'] = esc_html( $input['hook_point'] );
	$output['hook_point'] = str_replace( array( ' ', '　', "\t", "\r\n", "\r", "\n", ',' ), "\n", $output['hook_point'] );
	$output['hook_point'] = str_replace( "\n\n", "\n", $output['hook_point'] );

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
function veu_set_facebook_script() {
	add_action( 'wp_footer', 'exUnit_print_fbId_script', 100 );
}

function exUnit_print_fbId_script() {
	?>
<div id="fb-root"></div>
	<?php
	$options = veu_get_sns_options();
	$fbAppId = ( isset( $options['fbAppId'] ) ) ? $options['fbAppId'] : '';
	?>
<script>
;(function(w,d){
	var load_contents=function(){
		(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/<?php echo esc_attr( _x( 'en_US', 'facebook language code', 'vk-all-in-one-expansion-unit' ) ); ?>/sdk.js#xfbml=1&version=v2.9&appId=<?php echo esc_html( $fbAppId ); ?>";
		fjs.parentNode.insertBefore(js, fjs);
		}(d, 'script', 'facebook-jssdk'));
	};
	var f=function(){
		load_contents();
		w.removeEventListener('scroll',f,true);
	};
	var widget = d.getElementsByClassName("fb-page")[0];
	var view_bottom = d.documentElement.scrollTop + d.documentElement.clientHeight;
	var widget_top = widget.getBoundingClientRect().top + w.scrollY;
	if ( widget_top < view_bottom) {
		load_contents();
	} else {
		w.addEventListener('scroll',f,true);
	}
})(window,document);
</script>
	<?php
}

function veu_set_twitter_script() {
	add_action( 'wp_footer', 'veu_print_twitter_script', 100 );
}

function veu_print_twitter_script() {
	?>
<script type="text/javascript">
;(function(w,d){
	var load_contents=function(){
		var s=d.createElement('script');
		s.async='async';
		s.charset='utf-8';
		s.src='//platform.twitter.com/widgets.js';
		d.body.appendChild(s);
	};
	var f=function(){
		load_contents();
		w.removeEventListener('scroll',f,true);
	};
	var widget = d.getElementsByClassName("twitter-timeline")[0];
	var view_bottom = d.documentElement.scrollTop + d.documentElement.clientHeight;
	var widget_top = widget.getBoundingClientRect().top + w.scrollY;
	if ( widget_top < view_bottom) {
		load_contents();
	} else {
		w.addEventListener('scroll',f,true);
	}
})(window,document);
</script>
	<?php
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
	'admin_menu',
	function() {
		require dirname( __FILE__ ) . '/class-veu-metabox-sns-title.php';
	}
);

if ( $vkExUnit_sns_options['enableOGTags'] == true ) {
	require dirname( __FILE__ ) . '/function_og.php';
}

// シェアボタンを表示する設定の読み込み
require dirname( __FILE__ ) . '/function-sns-btns.php';
/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
admin_menu のタイミングで読み込んでいる
	*/
add_action(
	'admin_menu',
	function() {
		require dirname( __FILE__ ) . '/class-veu-metabox-sns-button.php';
	}
);

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

/**
 * Load clopboard.js
 */
function veu_enqueue_clipboard() {
	$options = veu_get_sns_options();
	if ( ! empty( $options['useCopy'] ) ) {
		wp_enqueue_script( 'copy-button', plugin_dir_url( __FILE__ ) . 'assets/js/copy-button.js', array( 'clipboard' ), null, true );
	}
}
add_action( 'wp_enqueue_scripts', 'veu_enqueue_clipboard' );

