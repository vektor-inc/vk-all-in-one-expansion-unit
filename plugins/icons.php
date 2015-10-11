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
		add_action( 'admin_init', array( $this, 'option_init' ) );
		add_action( 'wp_head',    array( $this, 'output_tag' ) );
	}


	public function option_init() {
		vkExUnit_register_setting(
			__( 'icon setting', 'vkExUnit' ), 	// tab label.
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
<h3><?php _e( 'icon setting', 'vkExUnit' ); ?></h3>
<div id="on_setting" class="sectionBox">
<table class="form-table">
    <!-- Favicon -->
    <tr>
	<th><?php _e( 'Favicon Setting', 'vkExUnit' ); ?></th>
		<td><input type="text" name="vkExUnit_icon_settings" id="favicon" value="<?php echo $options ?>" style="width:60%;" /> 
	<button id="media_favicon" class="media_btn button button-default"><?php _e( 'Choose icon', 'vkExUnit' ); ?></button>
	<p><?php _e( 'Please upload your ".ico" file','vkExUnit' ); ?></p>
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

vExUnit_icons::instance();
