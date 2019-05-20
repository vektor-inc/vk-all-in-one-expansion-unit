<?php
/**
 * VkExUnit icons.php
 * set favicon tag of user uploaded icon
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    8/Jul/2015
 */

class vExUnit_icons {
	// singleton instance
	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new vExUnit_icons;
		self::$instance->run_init();
		return self::$instance;
	}


	private function __construct() {
		/***    do noting    ***/
	}


	protected function run_init() {
		add_action( 'veu_package_init', array( $this, 'option_init' ) );
		add_action( 'wp_head',    array( $this, 'output_tag' ) );
	}


	public function option_init() {
		vkExUnit_register_setting(
			__( 'icon setting', 'vk-all-in-one-expansion-unit' ), 	// tab label.
			'vkExUnit_icon_settings',			// name attr
			array( $this, 'sanitize_config' ), // sanitaise function name
			array( $this, 'render_configPage' )  // setting_page function name
		);
	}


	public static function get_default_option() {
		$option = '';
		return $option;
	}


	public function sanitize_config( $option ) {

		$output = self::get_default_option();
		$output = $option;
		return $output;
	}


	public static function get_option() {
		return get_option( 'vkExUnit_icon_settings', self::get_default_option() );
	}


	public function render_configPage() {
		$options = self::get_option();
?>
<h3><?php _e( 'icon setting', 'vk-all-in-one-expansion-unit' ); ?></h3>

<div id="on_setting" class="sectionBox">
		<p class="text-warning">
			<?php
			$href = '"'.admin_url('customize.php').'"';
			printf( __( 'This feature will be discontinued shortly.<br>You can set the site icon from "Site Identity" panel of [ <a href=%s>Themes > Customize</a> ] After updating the setting.', 'vk-all-in-one-expansion-unit' ), $href );
			?>
		</p>

<table class="form-table">
    <!-- Favicon -->
    <tr>
	<th><?php _e( 'Favicon Setting', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td><input type="text" name="vkExUnit_icon_settings" id="favicon" value="<?php echo $options ?>" style="width:60%;" />
	<button id="media_src_favicon" class="media_btn button button-default"><?php _e( 'Choose icon', 'vk-all-in-one-expansion-unit' ); ?></button>
	<p><?php _e( 'Please upload your ".ico" file', 'vk-all-in-one-expansion-unit' ); ?></p>
    </td>
    </tr>
</table>
<?php submit_button(); ?>
</div>
<?php
	}


	public function output_tag() {
		$options = self::get_option();
		if ( isset( $options ) && $options ) {
			echo '<link rel="SHORTCUT ICON" HREF="'.$options.'" />';
		}
	}
}

	function vkExUnit_icons_discontinue() {
		global $pagenow;
		if ( $pagenow != 'icons.php' ) {
			return;
		}

		$html  = '<div class="error notice is-dismissible">';
		$html .= '  <p>「抜粋」は必ず入力してください！</p>';
		$html .= '  <button type="button" class="notice-dismiss">';
		$html .= '    <span class="screen-reader-text">この通知を非表示にする</span>';
		$html .= '  </button>';
		$html .= '</div>';

		echo $html;
	}
	add_action( 'admin_notices', 'vkExUnit_icons_discontinue' );

vExUnit_icons::instance();
