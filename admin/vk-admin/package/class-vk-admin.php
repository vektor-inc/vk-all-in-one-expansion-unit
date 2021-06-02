<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。
修正の際は上記リポジトリのデータを修正してください。
編集権限を持っていない方で何か修正要望などありましたら
各プラグインのリポジトリにプルリクエストで結構です。
*/

if ( ! class_exists( 'Vk_Admin' ) ) {
	/*
	congif.phpの方で既に ! class_exists( 'Vk_Admin' ) しているが、
	今後読み込みファイルが増えた時にVk-Adminの中で別のファイルを読み込むために
	このファイルにも更にclass_exists( 'Vk_Admin' ) がある。
	*/
	class Vk_Admin {

		public static $version = '2.1.0';

		static function init() {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_common_css' ) );
			add_action( 'customize_register', array( __CLASS__, 'admin_common_css' ) );
			add_action( 'wp_dashboard_setup', array( __CLASS__, 'dashboard_widget' ), 1 );
		}

		static function admin_common_css() {
			wp_enqueue_style( 'vk-admin-style', plugin_dir_url( __FILE__ ) . 'css/vk_admin.css', array(), self::$version, 'all' );
		}

		static function admin_enqueue_scripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();
			wp_enqueue_script( 'vk-admin-js', plugin_dir_url( __FILE__ ) . 'js/vk_admin.js', array( 'jquery' ), self::$version );
		}

		// 管理画面用のjsを読み込むページを配列で指定する
		// $admin_pages は vk-admin-config.php に記載
		static function admin_scripts( $admin_pages ) {
			foreach ( $admin_pages as $key => $value ) {
				$hook = 'admin_print_styles-' . $value;
				add_action( $hook, array( __CLASS__, 'admin_enqueue_scripts' ) );
			}
		}

		/**
		 * Plugin Exists
		 *
		 * @param string $plugin '${plugin_dir}/${plugin_file}.php'.
		 */
		public static function plugin_exists( $plugin ) {
			return file_exists( WP_PLUGIN_DIR . '/' . $plugin );
		}

		/**
		 * Theme Exists
		 * 
		 * @param string $theme '${theme_dir}/style.css'.
		 */
		public static function theme_exists( $theme ) {
			return file_exists( WP_CONTENT_DIR . '/themes/' . $theme );
		}

		/*
		get_admin_banner
		get_news_body_api
		get_news_from_rss
		admin _ Dashboard Widget
		admin _ sub
		admin _ page_frame
		/*--------------------------------------------------*/

		/*
		get_admin_banner
		/*--------------------------------------------------*/
		public static function get_admin_banner() {



			$banner_html = '';
			$dir_url     = plugin_dir_url( __FILE__ );
			$lang        = ( get_locale() == 'ja' ) ? 'ja' : 'en';

			// 画像を配置したディレクトリの URL
			$img_base_url = 'https://raw.githubusercontent.com/vektor-inc/vk-admin-banners/main/images/';

			// 変数の初期化
			$product_array  = array();

			// WP File System で JSON ファイルを読み込み
			require_once ABSPATH . 'wp-admin/includes/file.php';
			if ( WP_Filesystem() ) {
				global $wp_filesystem;

				// プロダクトの配列を取得・生成
				$product_json_url = 'https://raw.githubusercontent.com/vektor-inc/vk-admin-banners/main/vk-admin-banners.json';
				$product_json     = $wp_filesystem->get_contents( $product_json_url );
				$product_json     = mb_convert_encoding( $product_json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' );
				$product_array    = json_decode( $product_json,true );

			}

			$banner_html .= '<div class="vk-admin-banner">';

			if ( $lang == 'ja' ) {
				// $banner_html .= '<a class="admin_banner" href="https://recruit.vektor-inc.co.jp/?rel=vkadmin" target="_blank">';
				// $banner_html .= '<img src="' . $dir_url . 'images/admin_banner_recruit.jpg" alt="[ Vektor,Inc. 採用情報 ]" />';
				// $banner_html .= '</a>';
			} else {
				$banner_html .= '<a href="https://lightning.nagoya/lightning_copyright_customizer/?rel=vkadmin" target="_blank" class="button button-primary button-primary button-block" style="margin-bottom:1em;">Lightning Copyright Customizer <span class="screen-reader-text">(opens in a new tab)</span><span aria-hidden="true" class="dashicons dashicons-external" style="position:relative;top:3px;"></span></a>';
			}

			$banner_html .= '<div class="vk-admin-banner-grid">';

			if( ! empty( $product_array ) ) {

				// テーマのバナーを設置
				foreach( $product_array as $product ) {

					if ( 'theme' === $product['type'] ) {
						if ( ! self::theme_exists( $product['slug'] ) ) {
							if ( $lang === $product['language'] ) {

								// プラグインの検索結果に飛ばす場合 URL を変換する必要がある
								$product_url = true === $product['admin_url'] ? admin_url( $product['link_url'] ) : $product['link_url'];

								// バナーを追加
								$banner_html .= '<a href="' . $product_url . '" target="_blank" class="admin_banner">';
								$banner_html .= '<img src="' . $img_base_url . $product['image_file'] . '" alt="' . $product['alt'] . '" />';
								$banner_html .= '</a>';

							}
						}
					}

					if ( 'plugin' === $product['type'] ) {
						if ( ! self::plugin_exists( $product['slug'] ) ) {
							if ( $lang === $product['language'] ) {

								// プラグインの検索結果に飛ばす場合 URL を変換する必要がある
								$product_url = true === $product['admin_url'] ? admin_url( $product['link_url'] ) : $product['link_url'];

								// バナーを追加
								$banner_html .= '<a href="' . $product_url . '" target="_blank" class="admin_banner">';
								$banner_html .= '<img src="' . $img_base_url . $product['image_file'] . '" alt="' . $product['alt'] . '" />';
								$banner_html .= '</a>';

							}
						}
					}
				}
			}

			$banner_html .= '</div>';

			$banner_html .= '<a href="//www.vektor-inc.co.jp" class="vektor_logo" target="_blank" class="admin_banner"><img src="' . $img_base_url . 'vektor_logo-2020.png" alt="Vektor,Inc." /></a>';

			$banner_html .= '</div>';

			return apply_filters( 'vk_admin_banner_html', $banner_html );
		}

		/*
		get_news_body
		/*--------------------------------------------------*/
		public static function get_news_body() {
			if ( 'ja' == get_locale() ) {
				return self::get_news_from_rest_api();
			}
			// English
			if ( 'ja' != get_locale() ) {
				return self::get_news_from_rss();
			}
		}

		/*
		get_news_body_api
		/*--------------------------------------------------*/

		public static function get_news_from_rest_api() {

			$html = '<h4 class="vk-metabox-sub-title">';
			$html .= 'Vektor製品更新情報';
			$html .= '<a href="https://www.vektor-inc.co.jp/product-update/?rel=vkadmin" target="_blank" class="vk-metabox-more-link">記事一覧<span aria-hidden="true" class="dashicons dashicons-external"></span></a>';
			$html .= '</h4>';
			$html .= '<ul id="vk-product-update" class="vk-metabox-post-list"></ul>';

			$html .= '<h4 class="vk-metabox-sub-title">';
			$html .= 'ベクトルからのお知らせ';
			$html .= '<a href="https://www.vektor-inc.co.jp/info/?rel=vkadmin" target="_blank" class="vk-metabox-more-link">記事一覧<span aria-hidden="true" class="dashicons dashicons-external"></span></a>';
			$html .= '</h4>';
			$html .= '<ul id="vk-wp-info" class="vk-metabox-post-list"></ul>';

			$html .= '<h4 class="vk-metabox-sub-title">';
			$html .= 'Vektor WordPress ブログ';
			$html .= '<a href="https://www.vektor-inc.co.jp/category/wordpress-info/?rel=vkadmin" target="_blank" class="vk-metabox-more-link">記事一覧<span aria-hidden="true" class="dashicons dashicons-external"></span></a>';
			$html .= '</h4>';
			$html .= '<ul id="vk-wp-blog" class="vk-metabox-post-list"></ul>';

			$html .= '<h4 class="vk-metabox-sub-title">';
			$html .= 'Vektor WordPress フォーラム';
			$html .= '<a href="https://vws.vektor-inc.co.jp/forums/?rel=vkadmin" target="_blank" class="vk-metabox-more-link">記事一覧<span aria-hidden="true" class="dashicons dashicons-external"></span></a>';
			$html .= '</h4>';
			$html .= '<ul id="vk-wp-forum" class="vk-metabox-post-list"></ul>';

			$html = apply_filters( 'vk_admin_news_html', $html );

			add_action( 'admin_footer', array( __CLASS__, 'load_rest_api_js' ) );

			return $html;
			?>
			<?php
		}

		public static function load_rest_api_js() {

			?>
		<script>
		/*-------------------------------------------*/
		/* REST API でお知らせを取得
		/*-------------------------------------------*/
		;(function($){
			jQuery(document).ready(function($){

				// お知らせ
				$.getJSON( "https://vektor-inc.co.jp/wp-json/wp/v2/info/?per_page=3",
				function(results) {
						// 取得したJSONの内容をループする
						$.each(results, function(i, item) {
							// 日付のデータを取得
							var date = new Date(item.date_gmt);
							var formate_date = date.toLocaleDateString();
							// JSONの内容の要素を</ul>の前に出力する
							$("ul#vk-wp-info").append('<li><span class="date">'+ formate_date +'</span><a href="' + item.link + '?rel=vkadmin" target="_blank">' + item.title.rendered + '</a></li>');
						});
				});

				// 製品更新情報
				$.getJSON( "https://vektor-inc.co.jp/wp-json/wp/v2/product-update/?per_page=5",
				function(results) {
						// 取得したJSONの内容をループする
						$.each(results, function(i, item) {
							// 日付のデータを取得
							var date = new Date(item.date_gmt);
							var formate_date = date.toLocaleDateString();
							// JSONの内容の要素を</ul>の前に出力する
							$("ul#vk-product-update").append('<li><span class="date">'+ formate_date +'</span><a href="' + item.link + '?rel=vkadmin" target="_blank">' + item.title.rendered + '</a></li>');
						});
				});

				// ブログ
				$.getJSON( "https://www.vektor-inc.co.jp/wp-json/wp/v2/posts/?categories=55&per_page=3",
				function(results) {
						// 取得したJSONの内容をループする
						$.each(results, function(i, item) {
							// 日付のデータを取得
							var date = new Date(item.date_gmt);
							var formate_date = date.toLocaleDateString();
							// JSONの内容の要素を</ul>の前に出力する
							$("ul#vk-wp-blog").append('<li><span class="date">'+ formate_date +'</span><a href="' + item.link + '?rel=vkadmin" target="_blank">' + item.title.rendered + '</a></li>');
						});
				});

				// フォーラム
				$.getJSON( "https://vws.vektor-inc.co.jp/wp-json/wp/v2/topics/?per_page=5",
				function(results) {
						$.each(results, function(i, item) {
							var date = new Date(item.date_gmt);
							var formate_date = date.toLocaleDateString();
							 $("ul#vk-wp-forum").append('<li><a href="' + item.link + '?rel=vkadmin" target="_blank">' + item.title.rendered + '</a></li>');
						});
				});

		});
		})(jQuery);
		</script>
			<?php
		}

		/*
		get_news_from_rss
		/*  RSS方針で現在は日本語以外でのみ使用
		/*--------------------------------------------------*/
		public static function get_news_from_rss() {

			$output = '';

			include_once ABSPATH . WPINC . '/feed.php';

			if ( 'ja' == get_locale() ) {
				$exUnit_feed_url = apply_filters( 'vkAdmin_news_RSS_URL_ja', 'https://ex-unit.nagoya/ja/feed' );
				// $exUnit_feed_url = apply_filters( 'vkAdmin_news_RSS_URL_ja', 'https://www.vektor-inc.co.jp/feed/?category_name=internship' );
			} else {
				$exUnit_feed_url = apply_filters( 'vkAdmin_news_RSS_URL', 'https://ex-unit.nagoya/feed' );
			}

			$my_feeds = array(
				array( 'feed_url' => $exUnit_feed_url ),
			);

			foreach ( $my_feeds as $feed ) {
				$rss = fetch_feed( $feed['feed_url'] );

				if ( ! is_wp_error( $rss ) ) {
					$output = '';

					$maxitems  = $rss->get_item_quantity( 5 ); // number of news to display (maximum)
					$rss_items = $rss->get_items( 0, $maxitems );
					$output   .= '<div class="rss-widget">';
					$output   .= '<h4 class="vk-metabox-sub-title">' . apply_filters( 'vk-admin-sub-title-text', 'Information' ) . '</h4>';
					$output   .= '<ul>';

					if ( $maxitems == 0 ) {
						$output .= '<li>';
						$output .= __( 'Sorry, there is no post', 'vk-all-in-one-expansion-unit' );
						$output .= '</li>';
					} else {
						foreach ( $rss_items as $item ) {
							$test_date = $item->get_local_date();
							$content   = $item->get_content();

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

		public static function is_dashboard_active() {
			$flag = false;
			if ( 'ja' == get_locale() ) {
				$flag = true;
			}
			if ( self::plugin_exists( 'vk-all-in-one-expansion-unit/vkExUnit.php' ) ) {
				$flag = true;
			}
			if ( ! self::plugin_exists( 'vk-post-author-display/post-author-display.php' ) ) {
				$flag = true;
			}
			$theme = wp_get_theme()->get( 'Template' );
			if ( $theme != 'lightning' ) {
				$flag = true;
			}

			return apply_filters( 'vk-admin-is-dashboard-active', $flag );
		}
		/*
		admin _ Dashboard Widget
		/*--------------------------------------------------*/
		public static function dashboard_widget() {

			if ( self::is_dashboard_active() ) {
				wp_add_dashboard_widget(
					'vk_dashboard_widget',
					__( 'Vektor WordPress Information', 'vk-all-in-one-expansion-unit' ),
					array( __CLASS__, 'dashboard_widget_body' )
				);
			}
		}

		public static function dashboard_widget_body() {
			if ( 'ja' == get_locale() ) {
				echo self::get_news_body();
			}
			echo self::get_admin_banner();
		}

		/*
		admin _ sub
		/*--------------------------------------------------*/
		// 2016.08.07 ExUnitの有効化ページでは直接 admin_subを呼び出しているので注意
		public static function admin_sub() {
			$display = apply_filters( 'vk_admin_sub_display', true );
			if ( ! $display ) {
				return;
			}
			$adminSub = '<div class="adminSub scrTracking">' . "\n";
			if ( 'ja' == get_locale() ) {
				$adminSub .= '<div class="infoBox">' . self::get_news_body() . '</div>' . "\n";
			}
			$adminSub .= '<div class="vk-admin-banner">' . self::get_admin_banner() . '</div>' . "\n";

			$adminSub .= '</div><!-- [ /.adminSub ] -->' . "\n";
			return $adminSub;
		}

		/*
		admin _ page_frame
		/*--------------------------------------------------*/
		public static function admin_page_frame( $get_page_title, $the_body_callback, $get_logo_html = '', $get_menu_html = '', $get_layout = 'column_3' ) {
			?>
			<div class="wrap vk_admin_page">

				<div class="adminMain <?php echo $get_layout; ?>">

					<?php if ( $get_layout == 'column_3' ) : ?>
				<div id="adminContent_sub" class="scrTracking adminMain_sub">
					<div class="pageLogo"><?php echo $get_logo_html; ?></div>
						<?php if ( $get_page_title ) : ?>
					<h2 class="page_title"><?php echo $get_page_title; ?></h2>
					<?php endif; ?>
					<div class="vk_option_nav">
						<ul>
						<?php echo $get_menu_html; ?>
						</ul>
					</div>
				</div><!-- [ /#adminContent_sub ] -->
				<?php endif; ?>

					<?php if ( $get_layout == 'column_2' ) : ?>
					<div class="pageLogo"><?php echo $get_logo_html; ?></div>
						<?php if ( $get_page_title ) : ?>
						<h1 class="page_title"><?php echo $get_page_title; ?></h1>
					<?php endif; ?>
				<?php endif; ?>

					<div id="adminContent_main" class="adminMain_main">
					<?php call_user_func_array( $the_body_callback, array() ); ?>
					</div><!-- [ /#adminContent_main ] -->

				</div><!-- [ /.adminMain ] -->

				<?php echo self::admin_sub(); ?>

			</div><!-- [ /.vkExUnit_admin_page ] -->
			<?php
		}

		public function __construct() {

		}
	}
} // if ( ! class_exists( 'Vk_Admin' ) )

Vk_Admin::init();
$Vk_Admin = new Vk_Admin();
