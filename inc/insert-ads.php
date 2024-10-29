<?php
/**
 * VkExUnit ad.php
 * insert ads for Content.
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    30/Jul/2015
 */

class vExUnit_Ads {
	// singleton instance
	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new vExUnit_Ads;
		self::$instance->run_init();
		return self::$instance;
	}

	private function __construct() {
		/***    do noting    */
	}


	protected function run_init() {
		add_action( 'veu_package_init', array( $this, 'option_init' ) );
		add_filter( 'the_content', array( $this, 'set_content' ), 10, 1 );
		add_action( 'wp_head', array( $this, 'print_google_auto_ad' ) );
		add_shortcode( 'vkExUnit_ad', array( $this, 'shortcode' ) );
	}

	public function option_init() {
		vkExUnit_register_setting(
			__( 'Insert ads', 'vk-all-in-one-expansion-unit' ),           // tab label.
			'vkExUnit_Ads',                         // name attr
			array( $this, 'sanitize_config' ),      // sanitaise function name
			array( $this, 'render_configPage' )     // setting_page function name
		);
	}


	public function set_content( $content ) {

		if ( vkExUnit_is_excerpt() ) {
			return $content; }

		global $is_pagewidget;
		if ( $is_pagewidget ) {
			return $content; }

		$option        = $this->get_option();
		$post_types    = $option['post_types'];
		$post_types    = apply_filters( 'veu_add_ad', $post_types );
		$post_type_now = get_post_type();

		$print = '';
		if ( ! empty( $post_types[ $post_type_now ] ) ) {
			$print = true;
		}
		/*
		以前は
		$post_types[0][post]
		という配列の持ち方だったが、
		後から作った関数 vk_the_post_type_check_list() の帰り値は
		$post_types[post][true]
		という形式に変更になったので、
		旧形式の配列でフックされた時用
		*/
		foreach ( $post_types as $key => $value ) {
			if ( is_numeric( $key ) ) {
				if ( get_post_type() == $value ) {
					$print = true;
				}
			}
		}

		if ( $print ) {
				$content  = preg_replace( '/(<span id="more-[0-9]+"><\/span>)/', '$1' . '[vkExUnit_ad area=more]', $content );
				$content  = '[vkExUnit_ad area=before]' . $content;
				$content .= '[vkExUnit_ad area=after]';
		}

		return $content;
	}


	public function shortcode( $atts ) {
		extract( shortcode_atts( array( 'area' => '' ), $atts ) );

		if ( $area != 'before' && $area != 'after' && $area != 'more' ) {
			return ''; }

		$option = $this->get_option();

		return $this->render_ad( $option[ $area ], $area );
	}


	private function render_ad( $ads, $area = 'more' ) {
		if ( ! $ads[0] ) {
			return ''; }
		$class = 'col-md-12';
		if ( isset( $ads[1] ) && $ads[1] ) {
			$class = 'col-md-6'; }

		$content  = '';
		$content .= '<aside class="row veu_insertAds ' . $area . '">';
		foreach ( $ads as $ad ) {
			if ( ! $ad ) {
				break; }

			$content .= '<div class="' . $class . '">';
			$content .= $ad;
			$content .= '</div>';
		}
		$content .= '</aside>';
		return $content;
	}

	public function print_google_auto_ad() {
		$option = $this->get_option();
		if ( $option['google-ads-active'] && $option['google-pub-id'] ) {

?><!-- [ <?php echo veu_get_name(); ?> GoogleAd ] -->
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
	 (adsbygoogle = window.adsbygoogle || []).push({
		  google_ad_client: "ca-pub-<?php echo esc_attr( $option['google-pub-id'] ); ?>",
		  enable_page_level_ads: true
			<?php
			if ( $option['google-ads-overlays-bottom'] ) {
				echo ',overlays: {bottom: true}';}
?>
	 });
</script>
<!-- [ / <?php echo veu_get_name(); ?> GoogleAd ] -->
		<?php
		}
	}

	public function sanitize_config( $input ) {
		$option                               = $input;
		$option['google-ads-active']          = ( isset( $input['google-ads-active'] ) ) ? esc_attr( $input['google-ads-active'] ) : '';
		$option['google-ads-overlays-bottom'] = ( isset( $input['google-ads-overlays-bottom'] ) ) ? esc_attr( $input['google-ads-overlays-bottom'] ) : '';
		$option['google-pub-id']              = ( isset( $input['google-pub-id'] ) ) ? stripslashes( esc_attr( $input['google-pub-id'] ) ) : '';
		$option['before'][0]                  = stripslashes( $input['before'][0] );
		$option['before'][1]                  = stripslashes( $input['before'][1] );
		$option['more'][0]                    = stripslashes( $input['more'][0] );
		$option['more'][1]                    = stripslashes( $input['more'][1] );
		$option['after'][0]                   = stripslashes( $input['after'][0] );
		$option['after'][1]                   = stripslashes( $input['after'][1] );

		if ( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) {
			foreach ( $input['post_types'] as $key => $value ) {
				$option['post_types'][ $key ] = esc_attr( $value );
			}
		} else {
			// 'post_types' 自体が存在しないと、デフォルト値として ['post_types']['post'] = true を返すように作ってあり、
			// チェックボックスのチェックが外れなくなるので
			// チェックが全部外れている時に 'post' => false をいれておく
			$option['post_types']['post'] = false;
		}

		if ( ! $option['before'][0] && $option['before'][1] ) {
			$option['before'][0] = $option['before'][1];
			$option['before'][1] = '';
		}
		if ( ! $option['before'][1] ) {
			unset( $option['before'][1] ); }

		if ( ! $option['more'][0] && isset( $option['more'][1] ) && $option['more'][1] ) {
			$option['more'][0] = $option['more'][1];
			$option['more'][1] = '';
		}
		if ( ! $option['more'][1] ) {
			unset( $option['more'][1] ); }

		if ( ! $option['after'][0] && $option['after'][1] ) {
			$option['after'][0] = $option['after'][1];
			$option['after'][1] = '';
		}
		if ( ! $option['after'][1] ) {
			unset( $option['after'][1] ); }

		return $option;
	}


	public static function get_option() {
		$default = array(
			'google-ads-active'          => false,
			'google-ads-overlays-bottom' => true,
			'google-pub-id'              => '',
			'before'                     => array( '' ),
			'more'                       => array( '' ),
			'after'                      => array( '' ),
			'post_types'                 => array( 'post' => true ),
		);
		$option  = get_option( 'vkExUnit_Ads' );

		// post_types を後で追加したので、option値に保存されてない時にデフォルトの post とマージする
		$option = wp_parse_args( $option, $default );

		$option['before'][0] = ( isset( $option['before'][0] ) ) ? $option['before'][0] : '';
		$option['more'][0]   = ( isset( $option['more'][0] ) ) ? $option['more'][0] : '';
		$option['after'][0]  = ( isset( $option['after'][0] ) ) ? $option['after'][0] : '';
		return $option;
	}

	public function render_configPage() {
		$option = $this->get_option();
	?>
	<h3><?php _e( 'Insert ads', 'vk-all-in-one-expansion-unit' ); ?></h3>
<div id="vkExUnit_Ads" class="sectionBox">

<table class="form-table">
<?php
/*
  Google Auto ads
/*--------------------------------------------------*/
	?>
<tr>
	<th><?php _e( 'Google Auto ads', 'vk-all-in-one-expansion-unit' ); ?><br>
		<?php
		$lang          = ( get_locale() == 'ja' ) ? 'ja' : 'en';
		$Google_ad_url = 'https://support.google.com/adsense/answer/7478040?hl=' . $lang;
		?>
		[ <a href="<?php echo esc_url( $Google_ad_url ); ?>" target="_blank"><?php _e( 'About Google Auto ads', 'vk-all-in-one-expansion-unit' ); ?></a> ]
	</th>
	<td>
		<?php _e( 'If you would like to set to Google Auto ads,Please fill in Publisher ID.', 'vk-all-in-one-expansion-unit' ); ?>
		<p><label>
			<input type="checkbox" name="vkExUnit_Ads[google-ads-active]" id="google-ads-active" value="true"<?php vk_is_checked( 'true', $option['google-ads-active'] ); ?>> <?php _e( 'Enable Google Auto ads', 'vk-all-in-one-expansion-unit' ); ?></label></p>
		<p>
		<label><?php _e( 'Publisher ID', 'vk-all-in-one-expansion-unit' ); ?></label><br>
		pub-<input type="text" name="vkExUnit_Ads[google-pub-id]" id="gaId" value="<?php echo esc_attr( $option['google-pub-id'] ); ?>" style="width:90%;">
	</p>
	<?php
	$link = '<a href="https://www.google.com/adsense/" target="_blank">' . __( 'Google AdSense dashboard', 'vk-all-in-one-expansion-unit' ) . '</a>';
	?>
	<p>* <?php printf( __( 'Publisher ID is you can investigate from the %s > Account information page.', 'vk-all-in-one-expansion-unit' ), $link ); ?>
	</p>
	<p><label>
		<input type="checkbox" name="vkExUnit_Ads[google-ads-overlays-bottom]" id="google-ads-overlays-bottom" value="true"<?php vk_is_checked( 'true', $option['google-ads-overlays-bottom'] ); ?>> <?php _e( 'Designate anchor ads at the bottom.', 'vk-all-in-one-expansion-unit' ); ?></label></p>

	<p>* <?php _e( 'The layout may collapse by inserting Google Auto ads, but the correspondence varies depending on the kind, specification, theme etc. of advertisement, so please write CSS according to your needs about the display collapse and correct it.', 'vk-all-in-one-expansion-unit' ); ?></p>
	</td>
</tr>
<?php
/*
  Manual set Ads
/*--------------------------------------------------*/
	?>
<tr><th><?php _e( 'Insert ads to post.', 'vk-all-in-one-expansion-unit' ); ?>
</th><td style="max-width:80em;">
<?php _e( 'Insert ads to before content and more tag and after content.', 'vk-all-in-one-expansion-unit' ); ?><br/><?php _e( 'If you want to separate ads area, you fill two fields.', 'vk-all-in-one-expansion-unit' ); ?>
<dl>
	<dt><label for="ad_content_before"><?php _e( 'insert the ad [ before content ]', 'vk-all-in-one-expansion-unit' ); ?></label></dt>
	<dd>
	<textarea rows="5" name="vkExUnit_Ads[before][]" id="ad_content_before" value="" style="width:100%;max-width:50em;" /><?php echo ( isset( $option['before'][0] ) && $option['before'][0] ) ? $option['before'][0] : ''; ?></textarea>
	<br/>
	<textarea rows="5" name="vkExUnit_Ads[before][]" value="" style="width:100%;max-width:50em;" /><?php echo ( isset( $option['before'][1] ) && $option['before'][1] ) ? $option['before'][1] : ''; ?></textarea>
	</dd>
</dl>
<dl>
	<dt><label for="ad_content_moretag"><?php _e( 'insert the ad [ more tag ]', 'vk-all-in-one-expansion-unit' ); ?></label></dt>
	<dd>
	<textarea rows="5" name="vkExUnit_Ads[more][]" id="ad_content_moretag" value="" style="width:100%;max-width:50em;" /><?php echo ( isset( $option['more'][0] ) && $option['more'][0] ) ? $option['more'][0] : ''; ?></textarea>
	<br/>
	<textarea rows="5" name="vkExUnit_Ads[more][]" value="" style="width:100%;max-width:50em;" /><?php echo ( isset( $option['more'][1] ) && $option['more'][1] ) ? $option['more'][1] : ''; ?></textarea>
	</dd>
</dl>
<dl>
	<dt><label for="ad_content_after"><?php _e( 'insert the ad [ after content ]', 'vk-all-in-one-expansion-unit' ); ?></label></dt>
	<dd>
	<textarea rows="5" name="vkExUnit_Ads[after][]" id="ad_content_after" value="" style="width:100%;max-width:50em;" /><?php echo ( isset( $option['after'][0] ) && $option['after'][0] ) ? $option['after'][0] : ''; ?></textarea>
	<br/>
	<textarea rows="5" name="vkExUnit_Ads[after][]" value="" style="width:100%;max-width:50em;" /><?php echo ( isset( $option['after'][1] ) && $option['after'][1] ) ? $option['after'][1] : ''; ?></textarea>
	</dd>
</dl>
</td></tr>
<tr>
<th><?php echo esc_html( 'Post type to display', 'vk-all-in-one-expansion-unit' );?></th>
<td>
		<?php
		$args = array(
			'name'    => 'vkExUnit_Ads[post_types]',
			'checked' => $option['post_types'],
		);
		vk_the_post_type_check_list( $args );
		?>
		</td>
	</tr>
	</table>
	<?php submit_button(); ?>
	</div>
	<?php
	}
}

vExUnit_Ads::instance();
