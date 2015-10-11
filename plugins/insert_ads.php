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
		add_action( 'admin_init', array( $this, 'option_init' ) );
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
		if ( get_post_type() == 'post' ) {
			$content = preg_replace( '/(<span id="more-[0-9]+"><\/span>)/', '$1'.'[vkExUnit_ad area=more]' , $content );
			$content .= '[vkExUnit_ad area=after]';
		}

		return $content;
	}


	public function shortcode( $atts ) {
		extract( shortcode_atts( array( 'area' => '' ), $atts ) );

		if ( $area != 'after' && $area != 'more' ) { return ''; }

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
		$option['more'][0]  = stripslashes( $input['more'][0] );
		$option['more'][1]  = stripslashes( $input['more'][1] );
		$option['after'][0] = stripslashes( $input['after'][0] );
		$option['after'][1] = stripslashes( $input['after'][1] );

		if ( ! $option['more'][0] && $option['more'][1] ) {
			$option['more'][0] = $option['more'][1];
			$option['more'][1] = '';
		}
		if ( ! $option['more'][1] ) { unset( $option['more'][1] ); }

		if ( ! $option['after'][0] && $option['after'][1] ) {
			$option['after'][0] = $option['after'][1];
			$option['after'][1] = '';
		}
		if ( ! $option['after'][1] ) { unset( $option['more'][1] ); }

		return $option;
	}


	public static function get_option() {
		return get_option( 'vkExUnit_Ads', array( 'more' => array( '' ), 'after' => array( '' ) ) );
	}


	public function render_configPage() {
		$option = $this->get_option();
?>
<h3><?php _e( 'Insert ads', 'vkExUnit' ); ?></h3>
<div id="vkExUnit_Ads" class="sectionBox">
<table class="form-table">
<tr><th><?php _e( 'Insert ads to post.', 'vkExUnit' ); ?>
</th><td style="max-width:80em;">
<?php _e( 'Insert ads to more tag and after content.', 'vkExUnit' ); ?><br/><?php _e( 'If you want to separate ads area, you fill two fields.', 'vkExUnit' ); ?>
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
