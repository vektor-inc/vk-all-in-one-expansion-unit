<?php
/**
 * VkExUnit contact_section.php
 * display contaxt box at after content of page.
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    26/Jun/2015
 */

class vExUnit_Contact {
	// singleton instance
	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new vExUnit_Contact;
		self::$instance->run_init();
		return self::$instance;
	}

	private function __construct() {
		/***    do noting    ***/
	}


	protected function run_init() {
		add_action( 'admin_init', array( $this, 'options_init' ) );
		add_action( 'save_post', array( $this, 'save_custom_field_postdata' ) );
		add_shortcode( 'vkExUnit_contact_section', array( $this, 'shortcode' ) );
		add_filter( 'the_content',    array( $this, 'set_content' ), 10,1 );
		add_filter( 'vkExUnit_customField_Page_activation', array( $this, 'activate_metavox' ), 10, 1 );
		add_action( 'vkExUnit_customField_Page_box', array( $this, 'render_meta_box' ) );
	}


	public function activate_metavox( $flag ) {
		return true;
	}


	public function options_init() {
		vkExUnit_register_setting(
			__( 'Contact Information', 'vkExUnit' ),      // tab label.
			'vkExUnit_contact',          				// name attr
			array( $this, 'option_sanitaize' ),           // sanitaise function name
			array( $this, 'options_page' )  // setting_page function name
		);
	}


	public static function get_option() {
		$default = array(
			'contact_txt' => __( 'Please feel free to inquire.', 'vkExUnit' ),
			'tel_number' => '000-000-0000',
			'contact_time' => __( 'Office hours 9:00 - 18:00 [ Weekdays except holidays ]', 'vkExUnit' ),
			'contact_link' => '',
			'button_text' => '',
			'button_text_small' => '',
			'short_text' => __( 'Contact us' , 'vkExUnit' ),
		);
		$option = get_option( 'vkExUnit_contact' );
		return wp_parse_args( $option, $default );
	}


	public function options_page() {
		$options = self::get_option();
	?>
<h3><?php _e( 'Contact Information', 'vkExUnit' ); ?></h3>
<div id="meta_description" class="sectionBox">
<table class="form-table">
<tr>
<th scope="row"><label for="contact_txt"><?php _e( 'Message', 'vkExUnit' );?></label></th>
<td>
<input type="text" name="vkExUnit_contact[contact_txt]" id="contact_txt" value="<?php echo esc_attr( $options['contact_txt'] ); ?>" style="width:50%;" /><br />
<span><?php _e( 'ex) ', 'vkExUnit' );?><?php _e( 'Please feel free to inquire.', 'vkExUnit' );?></span>
</td>
</tr>
<tr>
<th scope="row"><label for="tel_number"><?php _e( 'Phone number', 'vkExUnit' );?></label></th>
<td>
<input type="text" name="vkExUnit_contact[tel_number]" id="tel_number" value="<?php echo esc_attr( $options['tel_number'] ); ?>" style="width:50%;" /><br />
<span><?php _e( 'ex) ', 'vkExUnit' );?>000-000-0000</span>
</td>
</tr>
<tr>
<th scope="row"><label for="contact_time"><?php _e( 'Office hours', 'vkExUnit' );?></label></th>
<td>
<textarea cols="20" rows="2" name="vkExUnit_contact[contact_time]" id="contact_time" value="" style="width:50%;" /><?php echo esc_attr( $options['contact_time'] ); ?></textarea><br />
<span><?php _e( 'ex) ', 'vkExUnit' );?><?php _e( 'Office hours', 'vkExUnit' );?> 9:00 - 18:00 [ <?php _e( 'Weekdays except holidays', 'vkExUnit' );?> ]</span>
</td>
</tr>
<!-- he URL of contact page -->
<tr>
<th scope="row"><label for="contact_link"><?php _e( 'The contact page URL', 'vkExUnit' );?></label></th>
<td>
<input type="text" name="vkExUnit_contact[contact_link]" id="contact_link" value="<?php echo esc_attr( $options['contact_link'] ); ?>" class="width-500" /><br />
<span><?php _e( 'ex) ', 'vkExUnit' );?>http://www.********.co.jp/contact/ <?php _e( 'or', 'vkExUnit' );?> /******/</span><br />
<?php _e( '* If you fill in the blank, contact button does not appear.', 'vkExUnit' );?>
</td>
</tr>
<tr>
<th scope="row"><label for="sub_sitename"><?php _e( 'Contact button Text', 'vkExUnit' );?></label></th>
<td>
<textarea cols="20" rows="2" name="vkExUnit_contact[button_text]" id="sub_sitename" value="" style="width:50%;" /><?php echo esc_attr( $options['button_text'] ); ?></textarea><br />
<span><?php _e( 'ex) ', 'vkExUnit' );?><?php _e( 'Contact Us from email.', 'vkExUnit' );?></span>
</td>
</tr>
<!-- Company address -->
<tr>
<th scope="row"><label for="button_text_small"><?php _e( 'Contact button text( sub )', 'vkExUnit' );?></label></th>
<td>
<textarea cols="20" rows="2" name="vkExUnit_contact[button_text_small]" id="button_text_small" value="" style="width:50%;" /><?php echo $options['button_text_small'] ?></textarea><br />
	<span><?php _e( 'ex) ', 'vkExUnit' );?>
	<?php _e( 'Email contact form', 'vkExUnit' );?>
    </span>
</td>
</tr>
<tr>
<th scope="row"><label for="widget_text"><?php _e( 'Contact button short text for side widget', 'vkExUnit' );?></label></th>
<td>
<?php $short_text = ( isset( $options['short_text'] ) && $options['short_text'] ) ? $options['short_text'] : ''; ?>
<input type="text" name="vkExUnit_contact[short_text]" id="widget_text" value="<?php echo esc_attr( $short_text ); ?>" style="width:50%;" /><br />
<span><?php _e( 'This will used to "Contact Button" widget.' , 'vkExUnit' );?></span>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
	<?php
	}


	public function option_sanitaize( $option ) {
		return $option;
	}


	public function render_meta_box() {
		$enable = get_post_meta( get_the_id(), 'vkExUnit_contact_enable', true ); ?>
<div>
<input type="hidden" name="_nonce_vkExUnit_contact" id="_nonce_vkExUnit__custom_auto_eyecatch_noonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
<label for="vkExUnit_contact">
	<input type="checkbox" id="vkExUnit_contact" name="vkExUnit_contact_enable"<?php echo ($enable)? ' checked' : ''; ?> />
	<?php _e( 'Display Contact Section','vkExUnit' ); ?>
</label>
</div>
	<?php
	}

	public function save_custom_field_postdata( $post_id ) {
		$childPageIndex = isset( $_POST['_nonce_vkExUnit_contact'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit_contact'] ) : null;

		if ( ! wp_verify_nonce( $childPageIndex, plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id; }

		$data = isset( $_POST['vkExUnit_contact_enable'] ) ? htmlspecialchars( $_POST['vkExUnit_contact_enable'] ) : null;

		if ( 'page' == $data ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) { return $post_id; }
		}

		if ( '' == get_post_meta( $post_id, 'vkExUnit_contact_enable' ) ) {
			add_post_meta( $post_id, 'vkExUnit_contact_enable', $data, true );
		} else if ( $data != get_post_meta( $post_id, 'vkExUnit_contact_enable' ) ) {
			update_post_meta( $post_id, 'vkExUnit_contact_enable', $data );
		} else if ( '' == $data ) {
			delete_post_meta( $post_id, 'vkExUnit_contact_enable' );
		}
	}


	public static function is_my_turn() {
		global $is_pagewidget;
		if ( $is_pagewidget ) { return false; }
		if ( vkExUnit_is_excerpt() ) { return false; }
		if ( ! is_page() ) { return false; }
		if ( get_post_meta( get_the_id(), 'vkExUnit_contact_enable', true ) ) { return true; }
		return false;
	}


	public function set_content( $content ) {
		if ( ! self::is_my_turn() ) { return $content; }

		$content .= '[vkExUnit_contact_section]';
		return $content;
	}


	/*-------------------------------------------*/
	/*  contact bottom html
	/*-------------------------------------------*/

	public static function render_contact_html() {
		$options = self::get_option();
		$cont = '';
		$cont .= '<section class="veu_contact veu_contentAddSection">';
		$cont .= '<div class="contact_frame">';
		$cont .= '<p class="contact_txt">';
		$cont .= '<span class="contact_txt_catch">'.nl2br( esc_textarea( $options['contact_txt'] ) ).'</span>';
		$cont .= '<span class="contact_txt_tel veu_color_txt_key">'.$options['tel_number'].'</span>';
		$cont .= '<span class="contact_txt_time">'.nl2br( esc_textarea( $options['contact_time'] ) ).'</span>';
		$cont .= '</p>';

		if ( $options['contact_link'] && $options['button_text'] ) {
			$cont .= '<a href="'.$options['contact_link'].'" class="btn btn-primary btn-lg contact_bt">';
			$cont .= '<span class="contact_bt_txt">'.$options['button_text'].'</span>';

			if ( isset( $options['button_text_small'] ) && $options['button_text_small'] ) {
				$cont .= '<span class="contact_bt_subTxt">'.$options['button_text_small'].'</span>';
			}

			$cont .= '</a>';
		}
		$cont .= '</div>';
		$cont .= '</section>';
		if ( current_user_can( 'edit_theme_options' ) ) {
			$cont .= '<div class="veu_adminEdit"><a href="'.admin_url().'admin.php?page=vkExUnit_main_setting#vkExUnit_contact" class="btn btn-default" target="_blank">'.__( 'Edit contact information', 'vkExUnit' ).'</a></div>';
		}
		$cont = apply_filters( 'vkExUnit_contact_custom',$cont );
		return $cont;
	}

	/*-------------------------------------------*/
	/*  widget html
	/*-------------------------------------------*/

	public static function render_widget_html() {
		$options = self::get_option();
		$cont = '';

		if (
			( isset( $options['contact_link'] ) && $options['contact_link'] ) &&
			( isset( $options['short_text'] ) && $options['short_text'] )
			) {
			$cont .= '<a href="'.$options['contact_link'].'" class="btn btn-primary btn-lg btn-block contact_bt"><span class="contact_bt_txt">';
			$cont .= $options['short_text'];
			$cont .= '</span>';
			if ( isset( $options['button_text_small'] ) && $options['button_text_small'] ) {
				$cont .= '<span class="contact_bt_subTxt contact_bt_subTxt_side">'.$options['button_text_small'].'</span>';
			}
			$cont .= '</a>';
		}
		if ( current_user_can( 'edit_theme_options' ) ) {
			$cont .= '<div class="veu_adminEdit"><a href="'.admin_url().'admin.php?page=vkExUnit_main_setting#vkExUnit_contact" class="btn btn-default" target="_blank">'.__( 'Edit contact information', 'vkExUnit' ).'</a></div>';
		}
		return $cont;
	}


	public function shortcode() {
		return self::render_contact_html();
	}
}
vExUnit_Contact::instance();


/*-------------------------------------------*/
/*  Contact widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_contact_link extends WP_Widget {

	function __construct() {
		$widget_name = vkExUnit_get_short_name().'_'.__( 'Contact Button', 'vkExUnit' );

		parent::__construct(
			'vkExUnit_contact',
			$widget_name,
			array(
				'description' => sprintf( __( '*It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vkExUnit' ),vkExUnit_get_little_short_name() ),
				)
		);
	}


	function widget( $args, $instance ) {
		echo $args['before_widget'];
		echo '<div class="veu_contact">';
		echo vExUnit_Contact::render_widget_html();
		echo '</div>';
		echo $args['after_widget'];
	}


	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}


	function form( $instance ) {
		echo '<div style="padding:1em 0;">';
		_e( sprintf( __( '*It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vkExUnit' ),vkExUnit_get_little_short_name() ) );
		echo '</div>';
		return $instance;
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_vkExUnit_contact_link");' ) );
