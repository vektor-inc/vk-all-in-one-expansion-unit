<?php
$wp_theme = wp_get_theme();

$customize = new veu_css_customize();

class veu_css_customize {

	public function __construct() {
		$this->set_hook();
		/**
		 * Reason of Using through the after_setup_theme is 
		 * to be able to change the action hook point of css load from theme..
		 */
		add_action( 'after_setup_theme', array( get_called_class(), 'load_css_action' ) );
	}

	public static function load_css_action() {
		$hook_point = apply_filters( 'veu_enqueue_point_css_customize_common', 'wp_head' );
		// get_called_class()じゃないと外しにくい
		add_action( $hook_point, array( get_called_class(), 'css_customize_push_css' ), 200 );
	}

	public function set_hook() {
		add_action( 'admin_footer', array( $this, 'css_customize_page_js_and_css' ) );

	
		// 編集画面への反映
		// add_filter( 'tiny_mce_before_init', array( $this, 'css_customize_push_editor_css' ) );
		//
		add_action( 'admin_menu', array( $this, 'css_customize_menu' ), 20 );
		add_action( 'vkExUnit_action_adminbar', array( $this, 'admin_bar' ) );
		require_once( dirname( __FILE__ ) . '/css-customize-single.php' );

		/*
		VEU_Metabox 内の get_post_type が実行タイミングによっては
		カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
		admin_menu のタイミングで読み込んでいる
		 */
		add_action(
			'admin_menu', function() {
				require_once( dirname( __FILE__ ) . '/class-veu-metabox-css-customize.php' );
			}
		);

	}

	public function admin_bar( $wp_admin_bar ) {
		// 「CSSカスタマイズ」は edit_theme_options 権限にはアクセスさせない
		if ( current_user_can( 'activate_plugins' ) ) {
			$wp_admin_bar->add_node(
				array(
					'parent' => 'veu_adminlink',
					'id'     => 'veu_adminlink_css',
					'title'  => __( 'CSS Customize', 'vk-all-in-one-expansion-unit' ),
					'href'   => admin_url() . 'admin.php?page=vkExUnit_css_customize',
				)
			);
		}
	}

	/*
	  「CSSカスタマイズ」のメニュー
	/*-------------------------------------------*/
	public function css_customize_menu() {
		// $capability_required = veu_get_capability_required();
		add_submenu_page(
			'vkExUnit_setting_page',
			__( 'CSS Customize', 'vk-all-in-one-expansion-unit' ),
			__( 'CSS Customize', 'vk-all-in-one-expansion-unit' ),
			// $capability_required, // edit_theme_optionsのユーザーにもアクセスさせないため
			'activate_plugins',
			'vkExUnit_css_customize',
			array( $this, 'css_customize_render_page' )
		);
	}


	public function css_customize_render_page() {

		$data = $this->css_customize_valid_form();

		include( VEU_DIRECTORY_PATH . '/inc/css-customize/css-customize-edit.php' );
	}


	/*
	  設定画面のCSSとJS
	/*-------------------------------------------*/
	public function css_customize_page_js_and_css( $hook_suffix ) {
		global $hook_suffix;
		if (
			$hook_suffix == 'appearance_page_theme-css-customize' ||
			$hook_suffix == 'appearance_page_bv_grid_unit_options'
			) {	
		?>
	 <script type="text/javascript">
	jQuery(document).ready(function($){
		jQuery("#tipsBody dl").each(function(){
			var targetId = jQuery(this).attr("id");
			var targetTxt = jQuery(this).find("dt").text();
			var listItem = '<li><a href="#'+ targetId +'">'+ targetTxt +'</a></li>'
			jQuery('#tipsList ul').append(listItem);
		});
	});
	</script>
		<?php
		}
	}


	public function css_customize_valid_form() {

		$data = array(
			'mess'      => '',
			'customCss' => '',
		);

		if (isset($_POST['bv-css-submit']) && !empty($_POST['bv-css-submit'])
        && isset($_POST['bv-css-css'])
        && isset($_POST['biz-vektor-css-nonce']) && wp_verify_nonce($_POST['biz-vektor-css-nonce'], 'biz-vektor-css-submit')) {
            // 生のCSSをそのまま保存
            $cleanCSS = stripslashes(trim($_POST['bv-css-css']));
        
            if (update_option('vkExUnit_css_customize', $cleanCSS)) {
                $data['mess'] = '<div id="message" class="updated"><p>' . __('Your custom CSS was saved.', 'biz-vektor') . '</p></div>';
            }
        } else {
            if (isset($_POST['bv-css-submit']) && !empty($_POST['bv-css-submit'])) {
                $data['mess'] = '<div id="message" class="error"><p>' . __('Error occured. Please try again.', 'biz-vektor') . '</p></div>';
            }
        }
    
        $custom_css_option = get_option('vkExUnit_css_customize');
        // htmlspecialchars_decode を使ってデコード
        $custom_css_option = htmlspecialchars_decode($custom_css_option);
        // 特定のHTMLエンティティを置換
        $custom_css_option = str_replace('&gt;=', '>=', $custom_css_option);
        $custom_css_option = str_replace('&lt;=', '<=', $custom_css_option);
        $data['customCss'] = $custom_css_option !== false ? $custom_css_option : '';
    
        return $data;
		}
	
		public static function css_customize_get_css_min() {
			$css_customize = get_option( 'vkExUnit_css_customize' );
			if ( $css_customize ) {
				// Remove HTML tags, but keep <style> and <media> tags
				$css_customize = preg_replace('/<(?!\/?style|\/?media\b)[^>]+>/', '', $css_customize);
				// Delete br
				$css_customize = str_replace( PHP_EOL, '', $css_customize );
				// Delete tab
				$css_customize = preg_replace( '/[\n\r\t]/', '', $css_customize );
				// Multi space convert to single space
				$css_customize = preg_replace( '/\s+/', ' ', $css_customize );
				// Ensure proper spacing and remove extra spaces
				$css_customize = preg_replace( '/\s*([{}:;])\s*/', '$1', $css_customize );
				// Delete comment
				$css_customize = preg_replace( '/\/\*.*?\*\//', '', $css_customize );
				// Trim leading and trailing spaces
				$css_customize = trim($css_customize);
			}
			return $css_customize;
		}
	
		public static function css_customize_get_the_css_min() {
			$css_customize = self::css_customize_get_css_min();
			return $css_customize;
		}
	
		public static function css_customize_push_css() {
			$css_customize = self::css_customize_get_the_css_min();
			if ( $css_customize ) {
			?>
		<style type="text/css">/* <?php echo veu_get_short_name(); ?> CSS Customize */<?php echo $css_customize; ?>/* End <?php echo veu_get_short_name(); ?> CSS Customize */</style>
				<?php
			}
		}
	
		// public function css_customize_push_editor_css( $settings ) {
		// $css_customize = $this->css_customize_get_css_min();
		//
		// .editor-styles-wrapper h2 { font-size:30px; }
		//
		// if ( isset( $settings['content_style'] ) ) {
		// $settings['content_style'] .= $css_customize;
		// } else {
		// $settings['content_style'] = $css_customize;
		// }
		// $settings['content_style'] = $css_customize;
		// return $settings;
		// }
	}
