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
		/***    do noting    ***/
	}


	protected function run_init() {
		add_action( 'vkExUnit_package_init', array( $this, 'option_init' ) );
		add_filter( 'the_content',    array( $this, 'set_content' ), 10,1 );
		add_shortcode( 'vkExUnit_ad', array( $this, 'shortcode' ) );
	}

	public function option_init() {
		vkExUnit_register_setting(
			__( 'Insert ads', 'vkExUnit' ),           // tab label.
			'vkExUnit_Ads',                         // name attr
			array( $this, 'sanitize_config' ),      // sanitaise function name
			array( $this, 'render_configPage' )     // setting_page function name
		);
	}


	public function set_content( $content ) {
		if ( vkExUnit_is_excerpt() ) { return $content; }
		global $is_pagewidget;
		if ( $is_pagewidget ) { return $content; }
		$option = $this->get_option();
		$post_types = array('post');
		$post_types = apply_filters( 'veu_add_ad',$post_types );
		foreach ($post_types as $key => $post_type) {
			if ( get_post_type() == $post_type ) {
				$content = preg_replace( '/(<span id="more-[0-9]+"><\/span>)/', '$1'.'[vkExUnit_ad area=more]' , $content );
				$content = '[vkExUnit_ad area=before]'.$content;
				$content .= '[vkExUnit_ad area=after]';
			}
		}


		return $content;
	}


	public function shortcode( $atts ) {
		extract( shortcode_atts( array( 'area' => '' ), $atts ) );

		if ( $area != 'before' && $area != 'after' && $area != 'more' ) { return ''; }

		$option = $this->get_option();

		return $this->render_ad( $option[ $area ], $area );
	}


	private function render_ad( $ads, $area = 'more' ) {
		if ( ! $ads[0] ) { return ''; }
		$class = 'col-md-12';
		if ( isset( $ads[1] ) && $ads[1] ) { $class = 'col-md-6'; }

		$content = '';
		$content .= '<aside class="row veu_insertAds '.$area.'">';
		foreach ( $ads as $ad ) {
			if ( ! $ad ) { break; }

			$content .= '<div class="'.$class.'">';
			$content .= $ad;
			$content .= '</div>';
		}
		$content .= '</aside>';
		return $content;
	}


	public function sanitize_config( $input ) {
		$option['before'][0] = stripslashes( $input['before'][0] );
		$option['before'][1] = stripslashes( $input['before'][1] );
		$option['more'][0]  = stripslashes( $input['more'][0] );
		$option['more'][1]  = stripslashes( $input['more'][1] );
		$option['after'][0] = stripslashes( $input['after'][0] );
		$option['after'][1] = stripslashes( $input['after'][1] );

		if ( ! $option['before'][0] && $option['before'][1] ) {
			$option['before'][0] = $option['before'][1];
			$option['before'][1] = '';
		}
		if ( ! $option['before'][1] ) { unset( $option['before'][1] ); }

		if ( ! $option['more'][0] && isset( $option['more'][1] ) && $option['more'][1] ) {
			$option['more'][0] = $option['more'][1];
			$option['more'][1] = '';
		}
		if ( ! $option['more'][1] ) { unset( $option['more'][1] ); }

		if ( ! $option['after'][0] && $option['after'][1] ) {
			$option['after'][0] = $option['after'][1];
			$option['after'][1] = '';
		}
		if ( ! $option['after'][1] ) { unset( $option['after'][1] ); }

		return $option;
	}


	public static function get_option() {
		$option = get_option( 'vkExUnit_Ads', array( 'before' => array( '' ),  'more' => array( '' ), 'after' => array( '' ) ) );
		$option['before'][0] = ( isset($option['before'][0] ) ) ? $option['before'][0] : '';
		$option['more'][0] = ( isset($option['more'][0] ) ) ? $option['more'][0] : '';
		$option['after'][0] = ( isset($option['after'][0] ) ) ? $option['after'][0] : '';
		return $option;
	}


	public function render_configPage() {
		$option = $this->get_option();
?>
<h3><?php _e( 'Insert ads', 'vkExUnit' ); ?></h3>
<div id="vkExUnit_Ads" class="sectionBox">
<table class="form-table">
<tr><th><?php _e( 'Insert ads to post.', 'vkExUnit' ); ?>
</th><td style="max-width:80em;">
<?php _e( 'Insert ads to before content and more tag and after content.', 'vkExUnit' ); ?><br/><?php _e( 'If you want to separate ads area, you fill two fields.', 'vkExUnit' ); ?>
	<dl>
		<dt><label for="ad_content_before"><?php _e( 'insert the ad [ before content ]', 'vkExUnit' ); ?></label></dt>
		<dd>
		<textarea rows="5" name="vkExUnit_Ads[before][]" id="ad_content_before" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['before'][0] ) && $option['before'][0] )? $option['before'][0]: ''; ?></textarea>
		<br/>
		<textarea rows="5" name="vkExUnit_Ads[before][]" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['before'][1] ) && $option['before'][1] )? $option['before'][1]: ''; ?></textarea>
		</dd>
	</dl>
	<dl>
		<dt><label for="ad_content_moretag"><?php _e( 'insert the ad [ more tag ]', 'vkExUnit' ); ?></label></dt>
		<dd>
		<textarea rows="5" name="vkExUnit_Ads[more][]" id="ad_content_moretag" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['more'][0] ) && $option['more'][0] )? $option['more'][0]: ''; ?></textarea>
		<br/>
		<textarea rows="5" name="vkExUnit_Ads[more][]" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['more'][1] ) && $option['more'][1] )? $option['more'][1]: ''; ?></textarea>
		</dd>
	</dl>
	<dl>
		<dt><label for="ad_content_after"><?php _e( 'insert the ad [ after content ]', 'vkExUnit' ); ?></label></dt>
		<dd>
		<textarea rows="5" name="vkExUnit_Ads[after][]" id="ad_content_after" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['after'][0] ) && $option['after'][0] )? $option['after'][0]: ''; ?></textarea>
		<br/>
		<textarea rows="5" name="vkExUnit_Ads[after][]" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['after'][1] ) && $option['after'][1] )? $option['after'][1]: ''; ?></textarea>
		</dd>
	</dl>
</td></tr></table>
<?php submit_button(); ?>
</div>
<?php
	}
}

vExUnit_Ads::instance();
