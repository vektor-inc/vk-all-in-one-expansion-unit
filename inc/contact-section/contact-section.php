<?php
/**
 * VkExUnit contact_section.php
 * display contaxt box at after content of page.
 *
 * @package VkExUnit
 * @author  shoji imamura<imamura@vektor-inc.co.jp>
 * @since   26/Jun/2015
 */


/*
	load files
/*
-------------------------------------------*/
/*
	VkExUnit_Contact
			public static function instance() {
			private function __construct() {
			protected function run_init() {
			public function set_content_loopend( $query ) {
			public function activate_metabox( $flag ) {
			public function options_init() {
			public static function get_option() {
			public function options_page() {
			public function option_sanitaize( $option ) {
			public function render_meta_box() {
			public function save_custom_field_postdata( $post_id ) {
			public static function is_my_turn() {
			public function set_content( $content ) {

			// View
			public static function render_contact_section_html() {
			public static function render_widget_contact_btn_html() {
/*
/*
	Contact Button Widget
/*
	Contact Section Widget
/*-------------------------------------------*/



/*
	load files
/*-------------------------------------------*/
require_once 'customizer.php';



/*
	VkExUnit_Contact
/*-------------------------------------------*/
class VkExUnit_Contact {

	// singleton instance
	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance;
		}

		self::$instance = new VkExUnit_Contact();
		self::$instance->run_init();
		return self::$instance;
	}

	private function __construct() {
		/***
		 *
		 * * do noting
		 * */
	}

	protected function run_init() {
		add_action( 'veu_package_init', array( $this, 'options_init' ) );
		add_action( 'save_post', array( $this, 'save_custom_field_postdata' ) );
		add_shortcode( 'vkExUnit_contact_section', array( $this, 'shortcode' ) );
		require_once __DIR__ . '/block/index.php';

		// 固定ページ編集画にお問い合わせ情報を表示のチェックボックスを表示する
		add_action( 'veu_metabox_insert_items', array( $this, 'render_meta_box' ) );

		if ( veu_content_filter_state() == 'content' ) {
			add_filter( 'the_content', array( $this, 'set_content' ), 10, 1 );
		} else {
			add_action( 'loop_end', array( $this, 'set_content_loopend' ), 10, 1 );
		}
	}

	public function set_content_loopend( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}
		echo self::render_contact_section_html( 'veu_contact-layout-horizontal', true );
	}


	public function activate_metabox( $flag ) {
		return true;
	}


	public function options_init() {
		vkExUnit_register_setting(
			__( 'Contact Information', 'vk-all-in-one-expansion-unit' ),      // tab label.
			'vkExUnit_contact',                         // name attr
			array( $this, 'option_sanitaize' ),           // sanitaise function name
			array( $this, 'options_page' )  // setting_page function name
		);
	}

	public static function get_option() {
		$default = array(
			'contact_txt'          => __( 'Please feel free to inquire.', 'vk-all-in-one-expansion-unit' ),
			'tel_icon'             => 'fas fa-phone-square',
			'tel_number'           => '000-000-0000',
			'contact_time'         => __( 'Office hours 9:00 - 18:00 [ Weekdays except holidays ]', 'vk-all-in-one-expansion-unit' ),
			'contact_link'         => home_url(),
			'contact_target_blank' => false,
			'button_text'          => __( 'Contact us', 'vk-all-in-one-expansion-unit' ),
			'button_text_small'    => '',
			'short_text'           => __( 'Contact us', 'vk-all-in-one-expansion-unit' ),
			'contact_image'        => '',
			'contact_html'         => '',
		);
		$option  = get_option( 'vkExUnit_contact' );
		// オプション値が無い時は get_option の第２引数で登録されるが、
		// 既に値が存在しているが、項目があとから追加された時用に wp_parse_args をしている
		return wp_parse_args( $option, $default );
	}


	public function options_page() {
		$options = self::get_option();
		?>
	<h3><?php _e( 'Contact Information', 'vk-all-in-one-expansion-unit' ); ?></h3>
	<div id="meta_description" class="sectionBox">
	<p><?php _e( 'The contents entered here will be reflected in the bottom of each fixed page, the "Contact Section" widget, the "Contact Button" widget, etc.', 'vk-all-in-one-expansion-unit' ); ?>
	<br/>
		<?php _e( 'When I display it on the page, it is necessary to classify a check into "Display Contact Section" checkbox with the edit page of each page.', 'vk-all-in-one-expansion-unit' ); ?></p>

	<table class="form-table">
	<tr>
	<th scope="row"><label for="contact_txt"><?php _e( 'Message', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<input type="text" name="vkExUnit_contact[contact_txt]" id="contact_txt" value="<?php echo esc_attr( $options['contact_txt'] ); ?>" style="width:50%;" /><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?><?php _e( 'Please feel free to inquire.', 'vk-all-in-one-expansion-unit' ); ?></span>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="tel_number"><?php _e( 'Phone number', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<input type="text" name="vkExUnit_contact[tel_number]" id="tel_number" value="<?php echo esc_attr( $options['tel_number'] ); ?>" style="width:50%;" /><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?>000-000-0000</span>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="tel_icon"><?php _e( 'Phone icon', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<input type="text" name="vkExUnit_contact[tel_icon]" id="tel_icon" value="<?php echo esc_attr( $options['tel_icon'] ); ?>" style="width:50%;" /><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?>fas fa-phone-square  [ <a href="https://fontawesome.com/icons?d=gallery&q=phone&m=free" target="_blank" rel="noopener noreferrer">lcon list</a> ]</span>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="contact_time"><?php _e( 'Office hours', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<textarea cols="20" rows="2" name="vkExUnit_contact[contact_time]" id="contact_time" value="" style="width:50%;" /><?php echo esc_attr( $options['contact_time'] ); ?></textarea><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?><?php _e( 'Office hours', 'vk-all-in-one-expansion-unit' ); ?> 9:00 - 18:00 [ <?php _e( 'Weekdays except holidays', 'vk-all-in-one-expansion-unit' ); ?> ]</span>
	</td>
	</tr>
	<!-- he URL of contact page -->
	<tr>
	<th scope="row"><label for="contact_link"><?php _e( 'The contact page URL', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<input type="text" name="vkExUnit_contact[contact_link]" id="contact_link" value="<?php echo esc_attr( $options['contact_link'] ); ?>" class="width-500" /><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?>http://www.********.com/contact/ <?php _e( 'or', 'vk-all-in-one-expansion-unit' ); ?> /contact/</span><br />
		<?php _e( '* If you fill in the blank, widget\'s contact button does not appear.', 'vk-all-in-one-expansion-unit' ); ?>
	</td>
	</tr>
	<!-- Contact Target -->
	<tr>
	<th scope="row"><label for="contact_target_blank"><?php _e( 'Contact button link target setting', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<input type="checkbox" name="vkExUnit_contact[contact_target_blank]" id="contact_target_blank" <?php checked( ! empty( $options['contact_target_blank'] ) ); ?> />
		<?php _e( 'Open in New Tab', 'vk-all-in-one-expansion-unit' ); ?>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="sub_sitename"><?php _e( 'Contact button Text', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<textarea cols="20" rows="2" name="vkExUnit_contact[button_text]" id="sub_sitename" value="" style="width:50%;" /><?php echo esc_attr( $options['button_text'] ); ?></textarea><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?><?php _e( 'Contact Us from email.', 'vk-all-in-one-expansion-unit' ); ?></span>
	</td>
	</tr>
	<!-- Company address -->
	<tr>
	<th scope="row"><label for="button_text_small"><?php _e( 'Contact button text( sub )', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<textarea cols="20" rows="2" name="vkExUnit_contact[button_text_small]" id="button_text_small" value="" style="width:50%;" /><?php echo $options['button_text_small']; ?></textarea><br />
	<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?>
		<?php _e( 'Email contact form', 'vk-all-in-one-expansion-unit' ); ?>
	</span>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="widget_text"><?php _e( 'Contact button short text for side widget', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
		<?php $short_text = ( isset( $options['short_text'] ) && $options['short_text'] ) ? $options['short_text'] : ''; ?>
	<input type="text" name="vkExUnit_contact[short_text]" id="widget_text" value="<?php echo esc_attr( $short_text ); ?>" style="width:50%;" /><br />
	<span><?php _e( 'This will used to "Contact Button" widget.', 'vk-all-in-one-expansion-unit' ); ?></span>
	</td>
	</tr>
	</table>
	<button onclick="javascript:jQuery('#vkEx_contact_info').toggle(); return false;" class="button"><?php _e( 'Advanced Setting', 'vk-all-in-one-expansion-unit' ); ?></button>
		<?php
		$display = '';
		if ( ! $options['contact_image'] and ! $options['contact_html'] ) {
			$display = ' style="display:none;"';
		}
		?>
	<table class="form-table" id="vkEx_contact_info"<?php echo $display; ?>>
	<tr>
	<th><?php _e( 'Inquiry Banner image', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><input type="text" name="vkExUnit_contact[contact_image]" id="contact_image" value="<?php echo $options['contact_image']; ?>" style="width:60%;" />
<button id="media_src_contact_image" class="media_btn button button-default"><?php _e( 'Select Image' ); ?></button>
<p><?php _e( 'Display the image instead of the above inquiry information', 'vk-all-in-one-expansion-unit' ); ?><p>
</td>
</tr>
<tr>
<th><?php _e( 'Display HTML message instead of the standard', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><textarea cols="20" rows="5" name="vkExUnit_contact[contact_html]" id="contact_html" value="" style="width:100%;"><?php echo $options['contact_html']; ?></textarea>
<p><?php _e( 'HTML takes precedence over image', 'vk-all-in-one-expansion-unit' ); ?></p>
</td>
</tr>
</table>
		<?php submit_button(); ?>
</div>
		<?php
	}

	public function option_sanitaize( $option ) {
		$option['contact_txt']       = wp_kses_post( stripslashes( $option['contact_txt'] ) );
		$option['tel_number']        = wp_kses_post( stripslashes( $option['tel_number'] ) );
		$option['tel_icon']          = wp_kses( $option['tel_icon'] , array( 'i' => array( 'class' => array(), 'aria-hidden' => array() ) ) );
		$option['contact_time']      = wp_kses_post( stripslashes( $option['contact_time'] ) );
		$option['contact_link']      = esc_url ( $option['contact_link'] );
		$option['button_text']       = wp_kses_post( stripslashes( $option['button_text'] ) );
		$option['button_text_small'] = wp_kses_post( stripslashes( $option['button_text_small'] ) );
		$option['short_text']        = wp_kses_post( stripslashes( $option['short_text'] ) );
		$option['contact_image']     = esc_url( $option['contact_image'] );
		$option['contact_html']      = wp_kses_post( stripslashes( $option['contact_html'] ) );
		return $option;
	}


	public function render_meta_box() {
		$enable = get_post_meta( get_the_id(), 'vkExUnit_contact_enable', true );
		?>
	<div>
	<input type="hidden" name="_nonce_vkExUnit_contact" id="_nonce_vkExUnit__custom_auto_eyecatch_noonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	<label for="vkExUnit_contact">
	<input type="checkbox" id="vkExUnit_contact" name="vkExUnit_contact_enable"<?php echo ( $enable ) ? ' checked' : ''; ?> />
		<?php _e( 'Display Contact Section', 'vk-all-in-one-expansion-unit' ); ?>
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
			return $post_id;
		}

		$data = isset( $_POST['vkExUnit_contact_enable'] ) ? htmlspecialchars( $_POST['vkExUnit_contact_enable'] ) : null;

		if ( 'page' == $data ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		}

		if ( '' == get_post_meta( $post_id, 'vkExUnit_contact_enable' ) ) {
			add_post_meta( $post_id, 'vkExUnit_contact_enable', $data, true );
		} elseif ( $data != get_post_meta( $post_id, 'vkExUnit_contact_enable' ) ) {
			update_post_meta( $post_id, 'vkExUnit_contact_enable', $data );
		} elseif ( '' == $data ) {
			delete_post_meta( $post_id, 'vkExUnit_contact_enable' );
		}
	}


	public static function is_my_turn() {
		// 固定ページウィジェットの場合出力しない
		global $is_pagewidget;
		if ( $is_pagewidget ) {
			return false;
		}

		// 抜粋では表示しない
		if ( vkExUnit_is_excerpt() ) {
			return false;
		}

		// 固定ページ以外では表示しない
		if ( get_post_type() == 'page' ) {
			// 固定ページで問い合わせ先情報にチェックが入っている時
			if ( get_post_meta( get_the_id(), 'vkExUnit_contact_enable', true ) ) {
				return true;
			}
		} else {
			return false;
		}
	}

	public function set_content( $content ) {
		if ( ! self::is_my_turn() ) {
			return $content;
		}

		$content .= '[vkExUnit_contact_section]';
		return $content;
	}


	/*
		contact_section_html
	/*-------------------------------------------*/

	public static function render_contact_section_html( $additional_classes = '', $show_edit_button = true ) {
		$options     = self::get_option();
		$link_target = ! empty( $options['contact_target_blank'] ) ? ' target="_blank"' : '';
		$cont        = '';

		if ( $additional_classes ) {
			$additional_classes = ' ' . esc_attr( $additional_classes );
		}
		if ( $options['contact_html'] ) {

			$cont .= '<section class="veu_contentAddSection' . $additional_classes . '">';
			$cont .= $options['contact_html'];
			$cont .= '</section>';

		} elseif ( $options['contact_image'] ) {

			$cont .= '<section class="veu_contentAddSection' . $additional_classes . '">';
			$cont .= '<a href="' . esc_url( $options['contact_link'] ) . '"' . $link_target . '>';
			$cont .= '<img src="' . esc_attr( $options['contact_image'] ) . '" alt="contact_txt">';
			$cont .= '</a>';
			$cont .= '</section>';

		} else {

			$cont .= '<section class="veu_contact veu_contentAddSection vk_contact veu_card' . $additional_classes . '">';
			$cont .= '<div class="contact_frame veu_card_inner">';
			$cont .= '<p class="contact_txt">';
			$cont .= '<span class="contact_txt_catch">' . nl2br( esc_textarea( $options['contact_txt'] ) ) . '</span>';

			$tel_icon = '';
			if ( ! empty( $options['tel_icon'] ) ) {
				// $options['tel_icon'] の中が <i class="fas fa-mobile-alt"></i> など i タグの場合
				if ( preg_match( '/<i class="(.+?)"><\/i>/', $options['tel_icon'], $matches ) ) {
					$tel_icon = '<i class="contact_txt_tel_icon ' . esc_attr( $matches[1] ) . '"></i>';
				} else {
					$tel_icon = '<i class="contact_txt_tel_icon ' . esc_attr( $options['tel_icon'] ) . '"></i>';
				}
			}

			if ( wp_is_mobile() ) {
				$cont .= '<a href="tel:' . esc_attr( $options['tel_number'] ) . '" >';
			}
			$cont .= '<span class="contact_txt_tel veu_color_txt_key">' . $tel_icon . esc_html( $options['tel_number'] ) . '</span>';
			if ( wp_is_mobile() ) {
				$cont .= '</a>';
			}
			$cont .= '<span class="contact_txt_time">' . nl2br( esc_textarea( $options['contact_time'] ) ) . '</span>';
			$cont .= '</p>';

			if ( $options['contact_link'] && $options['button_text'] ) {
				$cont .= '<a href="' . $options['contact_link'] . '"' . $link_target . ' class="btn btn-primary btn-lg contact_bt">';
				$cont .= '<span class="contact_bt_txt">';

				// Envelope Icon
				$class = 'far fa-envelope';
				if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
					$class = Vk_Font_Awesome_Versions::class_switch( 'fa fa-envelope-o', 'far fa-envelope' );
				}
				$cont .= '<i class="' . $class . '"></i> ';

				$cont .= wp_kses_post( $options['button_text'] );

				// Arrow Icon
				$class = 'far fa-arrow-alt-circle-right';
				if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
					$class = Vk_Font_Awesome_Versions::class_switch( 'fa fa-arrow-circle-o-right', 'far fa-arrow-alt-circle-right' );
				}
				$cont .= ' <i class="' . $class . '"></i>';

				$cont .= '</span>';

				if ( isset( $options['button_text_small'] ) && $options['button_text_small'] ) {
					$cont .= '<span class="contact_bt_subTxt">' . $options['button_text_small'] . '</span>';
				}

				$cont .= '</a>';
			}
			$cont .= '</div>';
			$cont .= '</section>';
		}

		// if ( $show_edit_button && current_user_can( 'edit_theme_options' ) && ! is_customize_preview() ) {
		// $cont .= '<div class="veu_adminEdit"><a href="' . admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_contact" class="btn btn-default" target="_blank">' . __( 'Edit contact information', 'vk-all-in-one-expansion-unit' ) . '</a></div>';
		// }

		$cont = apply_filters( 'vkExUnit_contact_custom', $cont );

		return wp_kses_post( $cont );
	}

	public function shortcode() {
		return self::render_contact_section_html( 'veu_contact-layout-horizontal', true );
	}

	/*
		render_widget_contact_btn_html
	/*-------------------------------------------*/

	public static function render_widget_contact_btn_html() {
		$options     = self::get_option();
		$link_target = ! empty( $options['contact_target_blank'] ) ? ' target="_blank"' : '';
		$cont        = '';

		if ( ( isset( $options['contact_link'] ) && $options['contact_link'] )
			&& ( isset( $options['short_text'] ) && $options['short_text'] )
		) {

			$cont .= '<a href="' . esc_url( $options['contact_link'] ) . '"' . $link_target . ' class="btn btn-primary btn-lg btn-block contact_bt"><span class="contact_bt_txt">';

			// Envelope Icon
			$class = 'far fa-envelope';
			if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
				$class = Vk_Font_Awesome_Versions::class_switch( 'fa fa-envelope-o', 'far fa-envelope' );
			}
			$cont .= '<i class="' . $class . '"></i> ';

			$cont .= wp_kses_post( $options['short_text'] );

			// Arrow Icon
			$class = 'far fa-arrow-alt-circle-right';
			if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
				$class = Vk_Font_Awesome_Versions::class_switch( 'fa fa-arrow-circle-o-right', 'far fa-arrow-alt-circle-right' );
			}
			$cont .= ' <i class="' . $class . '"></i>';

			$cont .= '</span>';
			if ( isset( $options['button_text_small'] ) && $options['button_text_small'] ) {
				$cont .= '<span class="contact_bt_subTxt contact_bt_subTxt_side">' . wp_kses_post( $options['button_text_small'] ) . '</span>';
			}
			$cont .= '</a>';
		}
		// if ( current_user_can( 'edit_theme_options' ) ) {
		// $class = 'fas fa-edit';
		// if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
		// $class = Vk_Font_Awesome_Versions::class_switch( 'fa fa-pencil-square-o', 'fas fa-edit' );
		// }
		// $cont .= '<div class="veu_adminEdit"><a href="' . admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_contact" class="btn btn-default" target="_blank"><i class="' . $class . '"></i> ' . __( 'Edit contact information', 'vk-all-in-one-expansion-unit' ) . '</a></div>';
		// }
		return $cont;
	}
}

VkExUnit_Contact::instance();

/*
	Contact Button(Button Only) Widget
/*-------------------------------------------*/

class WP_Widget_VkExUnit_Contact_Button extends WP_Widget {

	function __construct() {
		$widget_name         = veu_get_prefix() . __( 'Contact Button', 'vk-all-in-one-expansion-unit' );
		$widget_description  = __( 'Display contact button.', 'vk-all-in-one-expansion-unit' );
		$widget_description .= ' ( ' . sprintf( __( 'It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vk-all-in-one-expansion-unit' ), veu_get_little_short_name() ) . ' ) ';
		parent::__construct(
			'vkExUnit_contact',
			$widget_name,
			array(
				'description' => $widget_description,
			)
		);
	}

	function widget( $args, $instance ) {
		echo $args['before_widget'];
		echo '<div class="veu_contact">';
		echo VkExUnit_Contact::render_widget_contact_btn_html();
		echo '</div>';
		echo $args['after_widget'];
	}


	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}


	function form( $instance ) {
		echo '<div style="padding:1em 0;">';
		$widget_description  = __( 'Display contact button', 'vk-all-in-one-expansion-unit' );
		$widget_description .= ' ( ' . sprintf( __( 'It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vk-all-in-one-expansion-unit' ), veu_get_little_short_name() ) . ' ) ';
		echo $widget_description;
		echo '</div>';
		return $instance;
	}
}

add_action( 'widgets_init', 'veu_widget_contact_button' );
function veu_widget_contact_button() {
	return register_widget( 'WP_Widget_VkExUnit_Contact_Button' );
}


/*
	Contact Section Widget
/*-------------------------------------------*/
class WP_Widget_VkExUnit_Contact_Section extends WP_Widget {

	function __construct() {

		$widget_name         = veu_get_prefix() . __( 'Contact Section', 'vk-all-in-one-expansion-unit' );
		$widget_description  = __( 'Display Phone number and contact button etc.', 'vk-all-in-one-expansion-unit' );
		$widget_description .= ' ( ' . sprintf( __( 'It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vk-all-in-one-expansion-unit' ), veu_get_little_short_name() ) . ' ) ';

		parent::__construct(
			'vkExUnit_contact_section',
			$widget_name,
			array(
				'description' => $widget_description,
			)
		);
	}


	function widget( $args, $instance ) {
		echo $args['before_widget'];
		$additional_classes = '';
		if ( empty( $instance['vertical'] ) ) {
			$additional_classes = 'veu_contact-layout-horizontal';
		}
		echo VkExUnit_Contact::render_contact_section_html( $additional_classes );
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance['vertical'] = $new_instance['vertical'];
		return $instance;
	}


	function form( $instance ) {
		echo '<div style="padding:1em 0;">';
		_e( sprintf( __( '*It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vk-all-in-one-expansion-unit' ), veu_get_little_short_name() ) );
		echo '</div>';
		echo '<h3 class="admin-custom-h3">' . __( 'Display Setting', 'vk-all-in-one-expansion-unit' ) . '</h3>';
		echo '<div style="padding:1em 0;">';
		echo '<label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'vertical' ) . '" ' . checked( isset( $instance['vertical'] ), true, false ) . '">';
		_e( 'Set telephone and mail form vertically', 'vk-all-in-one-expansion-unit' );
		echo '</label>';
		echo '</div>';
		return $instance;
	}
}

add_action( 'widgets_init', 'veu_widget_contact_section' );
function veu_widget_contact_section() {
	return register_widget( 'WP_Widget_VkExUnit_Contact_Section' );
}
