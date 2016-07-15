<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

class Vk_Admin {

	public static $version = '1.0.0';
	
	static function init(){
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_common_css' ) );
	}

	static function admin_directory_url (){
		$vk_admin_url = plugin_dir_url( __FILE__ );
		return $vk_admin_url;
	}

	static function admin_common_css (){
		wp_enqueue_style( 'vk-admin-style', self::admin_directory_url().'/css/vk_admin.css', array(), self::$version, 'all' );
	}

	static function admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_media();
		wp_enqueue_script( 'vk-admin-js', self::admin_directory_url().'/js/vk_admin.js', array( 'jquery' ), self::$version );
	}

	static function admin_scripts( $admin_pages ){
		foreach ($admin_pages as $key => $value) {
			$hook = 'admin_print_styles-'.$value;
			add_action( $hook, array( __CLASS__, 'admin_enqueue_scripts' ) );
		}
	}

	/*--------------------------------------------------*/
	/*  admin_banner
	/*--------------------------------------------------*/
	public static function admin_banner() {
		// print '<pre style="text-align:left">';print_r($a);print '</pre>';
		$dir_url = plugin_dir_url( __FILE__ );
		if ( get_locale() == 'ja' ) {
			$banner = '<a href="http://lightning.vektor-inc.co.jp/ja/" target="_blank"><img src="'.$dir_url.'/images/lightning_bnr_ja.jpg" alt="lightning_bnr_ja" /></a>';
		} else {
			$banner = '<a href="http://lightning.vektor-inc.co.jp/" target="_blank"><img src="'.$dir_url.'/images/lightning_bnr_en.jpg" alt="lightning_bnr_en" /></a>';
		}
		return apply_filters( 'vkExUnit_news_admin_banner_html' , $banner );
	}

	/*--------------------------------------------------*/
	/*  get_news_body
	/*--------------------------------------------------*/
	public static function get_news_body() {

		include_once( ABSPATH . WPINC . '/feed.php' );

		if ( 'ja' == get_locale() ) {
			$exUnit_feed_url = apply_filters( 'vkExUnit_news_RSS_URL_ja', 'http://ex-unit.vektor-inc.co.jp/ja/?feed?'.date( 'his' ) );
		} else {
			$exUnit_feed_url = apply_filters( 'vkExUnit_news_RSS_URL', 'http://ex-unit.vektor-inc.co.jp/?feed?'.date( 'his' ) );
		}

		$my_feeds = array(
			array( 'feed_url' => $exUnit_feed_url ),
		);

		foreach ( $my_feeds as $feed ) {
			$rss = fetch_feed( $feed['feed_url'] );

			if ( ! is_wp_error( $rss ) ) {
				$output = '';

				$maxitems = $rss->get_item_quantity( 5 ); //number of news to display (maximum)
				$rss_items = $rss->get_items( 0, $maxitems );

				$output .= '<div class="rss-widget">';
				$output .= '<h1 class="veu_admin_subTitle">Information</h1>';
				$output .= '<ul>';

				if ( $maxitems == 0 ) {
					$output .= '<li>';
					$output .= __( 'Sorry, there is no post', 'vkExUnit' );
					$output .= '</li>';
				} else {
					foreach ( $rss_items as $item ) {
						$test_date 	= $item->get_local_date();
						$content 	= $item->get_content();

						if ( isset( $test_date ) && ! is_null( $test_date ) ) {
							$item_date = $item->get_date( get_option( 'date_format' ) ) . '<br />'; } else {
							$item_date = ''; }

							$output .= '<li style="color:#777;">';
							$output .= $item_date;
							$output .= '<a href="' . esc_url( $item->get_permalink() ) . '" title="' . $item_date . '" target="_blank">';
							$output .= esc_html( $item->get_title() );
							$output .= '</a>';
							$output .= '</li>';
					}
				}

				$output .= '</ul>';
				$output .= '</div>';
			}

		} // if ( ! is_wp_error( $rss ) ) {

		return $output;
	}

	/*--------------------------------------------------*/
	/*  admin_sub
	/*--------------------------------------------------*/
	public static function admin_sub() {
		$adminSub = '<div class="adminSub">'."\n";
		$adminSub .= '<div class="infoBox">'.Vk_Admin::get_news_body().'</div>'."\n";
		$adminSub .= '<div class="adminBnr">'.Vk_Admin::admin_banner().'</div>'."\n";
		$adminSub .= '</div><!-- [ /.adminSub ] -->'."\n";
		return $adminSub;
	}

	/*--------------------------------------------------*/
	/*  admin_banner
	/*--------------------------------------------------*/
	public static function admin_page_frame( $get_page_title, $the_body_callback, $get_logo_html = '' , $get_menu_html = '' ) { ?>
		<div class="wrap vk_admin_page">
			<div class="adminMain">

				<div id="adminContent_sub">
					<?php echo $get_logo_html; ?>
					<h2 class="page_title"><?php echo $get_page_title;?></h2>
					<div class="vk_option_nav">
						<ul>
						<?php echo $get_menu_html; ?>
						</ul>
					</div>
				</div><!-- [ /#adminContent_sub ] -->

				<div id="adminContent_main">

				<?php call_user_func_array( $the_body_callback, array() );?>
				</div><!-- [ /#adminContent_main ] -->

			</div><!-- [ /.adminMain ] -->

			<div class="adminSub">
			<div class="infoBox"><?php echo Vk_Admin::get_news_body();?></div>
			<div class="adminBnr"><?php echo Vk_Admin::admin_banner();?></div>
			</div><!-- [ /.adminSub ] -->

		</div><!-- [ /.vkExUnit_admin_page ] -->
	<?php 
	}

	public function __construct(){

	}
}

Vk_Admin::init();
$Vk_Admin = new Vk_Admin();