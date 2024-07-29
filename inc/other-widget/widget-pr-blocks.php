<?php

/*-------------------------------------------*/
/*  VK PR Blocks
/*-------------------------------------------*/
class WP_Widget_vkExUnit_PR_Blocks extends WP_Widget {

	/*-------------------------------------------*/
	/*  form
	/*-------------------------------------------*/
	/*  Update
	/*-------------------------------------------*/
	/*  widget
	/*-------------------------------------------*/

	function __construct() {
		parent::__construct(
			'WP_Widget_vkExUnit_PR_Blocks',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		$name = veu_get_prefix() . __( 'PR Blocks', 'vk-all-in-one-expansion-unit' );
		// $name .= ' ( ' . __( 'Not recommended', 'vk-all-in-one-expansion-unit' ) . ' )';
		return $name;
	}

	public static function veu_widget_description() {
		return __( 'Displays a circle image or icon font for pr blocks', 'vk-all-in-one-expansion-unit' );
	}

	public static function default_options( $args = array() ) {
		$defaults = array(
			'block_count'        => 3,

			'label_1'            => __( 'Service', 'vk-all-in-one-expansion-unit' ),
			'media_image_1'      => '',
			'media_alt_1'        => '',
			'iconFont_class_1'   => 'far fa-file-alt',
			'iconFont_bgColor_1' => '#337ab7',
			'iconFont_bgType_1'  => '',
			'summary_1'          => '',
			'linkurl_1'          => '',
			'blank_1'            => '',

			'label_2'            => __( 'Company', 'vk-all-in-one-expansion-unit' ),
			'media_image_2'      => '',
			'media_alt_2'        => '',
			'iconFont_class_2'   => 'fas fa-building',
			'iconFont_bgColor_2' => '#337ab7',
			'iconFont_bgType_2'  => '',
			'summary_2'          => '',
			'linkurl_2'          => '',
			'blank_1'            => '',

			'label_3'            => __( 'Recruit', 'vk-all-in-one-expansion-unit' ),
			'media_image_3'      => '',
			'media_alt_3'        => '',
			'iconFont_class_3'   => 'fas fa-user',
			'iconFont_bgColor_3' => '#337ab7',
			'iconFont_bgType_3'  => '',
			'summary_3'          => '',
			'linkurl_3'          => '',
			'blank_1'            => '',

			'label_4'            => __( 'Contact', 'vk-all-in-one-expansion-unit' ),
			'media_image_4'      => '',
			'media_alt_4'        => '',
			'iconFont_class_4'   => 'fa-envelope',
			'iconFont_bgColor_4' => '#337ab7',
			'iconFont_bgType_4'  => '',
			'summary_4'          => '',
			'linkurl_4'          => '',
			'blank_1'            => '',
		);
		return wp_parse_args( (array) $args, $defaults );
	}


	/*-------------------------------------------*/
	/*  form
	/*-------------------------------------------*/
	public function form( $instance ) {
		$instance = self::default_options( $instance );
	?>

	<?php if ( is_customize_preview() ) { ?>
		<p><?php _e( 'If you want to change the number of columns, please change from Appearance -> Widgets screen.', 'vk-all-in-one-expansion-unit' ); ?></p>
	<?php } else { ?>
	<p>
	<label for="<?php echo $this->get_field_id( 'block_count' ); ?>"><?php _e( 'The choice of the number of columns:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
	<select name="<?php echo $this->get_field_name( 'block_count' ); ?>" id="<?php echo $this->get_field_id( 'block_count' ); ?>">
		<?php
		$selected = '';
		if ( intval( $instance['block_count'] ) === 3 ) {
			$selected = ' selected';
		}
		echo '<option value="3"' . $selected . '>' . __( '3column', 'vk-all-in-one-expansion-unit' ) . '</option>';
		$selected = '';
		if ( intval( $instance['block_count'] ) === 4 ) {
			$selected = ' selected';
		}
		echo '<option value="4"' . $selected . '>' . __( '4column', 'vk-all-in-one-expansion-unit' ) . '</option>';
		?>
		</select><br>
		<?php _e( 'If you change the number of columns, click to "Save" botton and exit the edit page. When restart the edit page, the column input form is increased or decreased.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>
	<?php } ?>

<?php
// PR Blocks
for ( $i = 1; $i <= intval( $instance['block_count'] ); ) {

	// PR Block admin title
	echo '<div class="admin-custom-section">';
	echo '<h2 class="admin-custom-h2">' . __( 'PR Block' . $i . ' setting', 'vk-all-in-one-expansion-unit' ) . '</h2>';

	// PR Block display title

	echo '<p><label for="' . $this->get_field_id( 'label_' . $i ) . '">' . __( 'Title:', 'vk-all-in-one-expansion-unit' ) . '</label><br/>' .
		'<input type="text" id="' . $this->get_field_id( 'label_' . $i ) . '-title" class="admin-custom-input" name="' . $this->get_field_name( 'label_' . $i ) . '" value="' . esc_attr( $instance[ 'label_' . $i ] ) . '" /></p>';

		// summary text
		echo '<p><label for="' . $this->get_field_id( 'summary_' . $i ) . '">' . __( 'Summary Text:', 'vk-all-in-one-expansion-unit' ) . '</label><br/>';
		echo '<textarea rows="4" cols="40" id="' . $this->get_field_id( 'summary_' . $i ) . '_text" class="admin-custom-input" name="' . $this->get_field_name( 'summary_' . $i ) . '">' . esc_textarea( $instance[ 'summary_' . $i ] ) . '</textarea>';
		echo '</p>';

		// link_URL
		echo '<p><label for="' . $this->get_field_id( 'linkurl_' . $i ) . '">' . __( 'Link URL:', 'vk-all-in-one-expansion-unit' ) . '</label><br/>' .
			'<input type="text" id="' . $this->get_field_id( 'linkurl_' . $i ) . '_title" class="admin-custom-input" name="' . $this->get_field_name( 'linkurl_' . $i ) . '" value="' . esc_attr( $instance[ 'linkurl_' . $i ] ) . '" style="margin-bottom:0.5em" />';
		$checked = ( isset( $instance[ 'blank_' . $i ] ) && $instance[ 'blank_' . $i ] ) ? ' checked' : '';
		echo '<input type="checkbox" value="true" id="' . $this->get_field_id( 'blank_' . $i ) . '" name="' . $this->get_field_name( 'blank_' . $i ) . '"' . $checked . ' />';
		echo '<label for="' . $this->get_field_id( 'blank_' . $i ) . '">' . __( 'Open link new tab.', 'vk-all-in-one-expansion-unit' ) . '</label>';
		echo '</p>';

		/*  Icon font
		/*-------------------------------------------*/
		echo '<h3 class="admin-custom-h3">' . __( 'Icon', 'vk-all-in-one-expansion-unit' ) . ' ' . $i . '</h3>';

	// icon font class input
	echo '<p><label for="' . $this->get_field_id( 'iconFont_' . $i ) . '">' . __( 'Class name of the icon font you want to use:', 'vk-all-in-one-expansion-unit' ) . '</label><br/>';
	echo '<input type="text" id="' . $this->get_field_id( 'iconFont_class_' . $i ) . '-font" class="font_class" name="' . $this->get_field_name( 'iconFont_class_' . $i ) . '" value="' . esc_attr( $instance[ 'iconFont_class_' . $i ] ) . '" /><br>';

	if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
		echo Vk_Font_Awesome_Versions::ex_and_link();
	}

	echo '</p>';

	// icon font color
	echo '<p class="color_picker_wrap">' .
		'<label for="' . $this->get_field_id( 'iconFont_bgColor_' . $i ) . '">' . __( 'Icon color:', 'vk-all-in-one-expansion-unit' ) . '</label><br/>' .
		'<input type="text" id="' . $this->get_field_id( 'iconFont_bgColor_' . $i ) . '-color" class="color_picker" name="' . $this->get_field_name( 'iconFont_bgColor_' . $i ) . '" value="' . esc_attr( $instance[ 'iconFont_bgColor_' . $i ] ) . '" /></p>';

	// icon font type
	echo '<p>' . __( 'Icon Background:', 'vk-all-in-one-expansion-unit' ) . '<br>';

	$checked = ( ! isset( $instance[ 'iconFont_bgType_' . $i ] ) || ! $instance[ 'iconFont_bgType_' . $i ] ) ? ' checked' : '';
	echo '<input type="radio" id="' . $this->get_field_id( 'iconFont_bgType_' . $i ) . '_solid" name="' . $this->get_field_name( 'iconFont_bgType_' . $i ) . '" value=""' . $checked . ' />';
	echo '<label for="' . $this->get_field_id( 'iconFont_bgType_' . $i ) . '_solid">' . __( 'Solid color', 'vk-all-in-one-expansion-unit' ) . '</label>  ';

	$checked = ( isset( $instance[ 'iconFont_bgType_' . $i ] ) && $instance[ 'iconFont_bgType_' . $i ] === 'no_paint' ) ? ' checked' : '';
	echo '<input type="radio" id="' . $this->get_field_id( 'iconFont_bgType_' . $i ) . '_no_paint" name="' . $this->get_field_name( 'iconFont_bgType_' . $i ) . '" value="no_paint"' . $checked . ' />';
	echo '<label for="' . $this->get_field_id( 'iconFont_bgType_' . $i ) . '_no_paint">' . __( 'No background', 'vk-all-in-one-expansion-unit' ) . '</label>';
	echo '</p>';

	/*  PR Image
	/*-------------------------------------------*/
	// media uploader imageurl input area
	echo '<h3 class="admin-custom-h3"><label for="' . $this->get_field_id( 'media_image_' . $i ) . '">' . __( 'PR Image', 'vk-all-in-one-expansion-unit' ) . ' ' . $i . '</label></h3>';
	echo '<p>' . __( 'When you have an image. Image is displayed with priority', 'vk-all-in-one-expansion-unit' ) . '</p>';

?>

<div class="media_image_section">
	<div class="_display admin-custom-thumb-outer">
		<?php
		if ( ! empty( $instance[ 'media_image_' . $i ] ) ) :
			?>
			<img src="<?php echo esc_url( $instance[ 'media_image_' . $i ] ); ?>" class="admin-custom-thumb" />
		<?php endif; ?>
	</div>
	<button class="button button-default widget_media_btn_select" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_add(this);return false;"><?php _e( 'Select image', 'vk-all-in-one-expansion-unit' ); ?></button>
	<button class="button button-default widget_media_btn_reset" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_del(this);return false;"><?php _e( 'Clear image', 'vk-all-in-one-expansion-unit' ); ?></button>
	<div class="_form" style="line-height: 2em">
		<input type="hidden" class="_url" name="<?php echo $this->get_field_name( 'media_image_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'media_image_' . $i ] ); ?>" />
			<input type="hidden" class="_alt" name="<?php echo $this->get_field_name( 'media_alt_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'media_alt_' . $i ] ); ?>" />
	</div>
</div><!-- [ /.media_image_section ] -->
</div><!-- [ /.admin-custom-secrion ] -->
<?php
	$i++;
}
	}

	/*-------------------------------------------*/
	/*  Update
	/*-------------------------------------------*/
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! empty( $new_instance['block_count'] ) ) {
			$instance['block_count'] = $new_instance['block_count'];
		}

		for ( $i = 1; $i <= 4; ) {
			$instance[ 'label_' . $i ]            = wp_kses_post( stripslashes( $new_instance[ 'label_' . $i ] ) );
			$instance[ 'media_image_' . $i ]      = esc_url( $new_instance[ 'media_image_' . $i ] );
			$instance[ 'media_alt_' . $i ]        = esc_html( stripslashes( $new_instance[ 'media_alt_' . $i ] ) );
			$instance[ 'iconFont_class_' . $i ]   = esc_html( $new_instance[ 'iconFont_class_' . $i ] );
			$instance[ 'iconFont_bgColor_' . $i ] = esc_html( $new_instance[ 'iconFont_bgColor_' . $i ] );
			$instance[ 'iconFont_bgType_' . $i ]  = $new_instance[ 'iconFont_bgType_' . $i ];
			$instance[ 'summary_' . $i ]          = wp_kses_post( stripslashes( $new_instance[ 'summary_' . $i ] ) );
			$instance[ 'linkurl_' . $i ]          = esc_url( $new_instance[ 'linkurl_' . $i ] );
			$instance[ 'blank_' . $i ]            = ( isset( $new_instance[ 'blank_' . $i ] ) && $new_instance[ 'blank_' . $i ] == 'true' );
			$i++;
		}
		return $instance;
	}


	/*-------------------------------------------*/
	/*  widget
	/*-------------------------------------------*/
	public function widget( $args, $instance ) {
		$instance = self::default_options( $instance );
		echo $args['before_widget'];
		echo PHP_EOL . '<article class="veu_prBlocks prBlocks row">' . PHP_EOL;

		$widget_block_count = ( isset( $instance['block_count'] ) ) ? intval( $instance['block_count'] ) : 3;

		$col_class = 'col-sm-4';
		if ( $widget_block_count == 4 ) {
			$col_class = 'col-sm-3';
		}

		// Print widget area
		for ( $i = 1; $i <= $widget_block_count; ) {
			if ( isset( $instance[ 'label_' . $i ] ) && $instance[ 'label_' . $i ] ) {
				echo '<div class="prBlock ' . $col_class . '">' . PHP_EOL;
				if ( ! empty( $instance[ 'linkurl_' . $i ] ) ) {
					$blank = ( isset( $instance[ 'blank_' . $i ] ) && $instance[ 'blank_' . $i ] ) ? 'target="_blank"' : '';
					echo '<a href="' . esc_url( $instance[ 'linkurl_' . $i ] ) . '" ' . $blank . '>' . PHP_EOL;
				}
				// icon font display

				if (
					// 画像が未登録 &&
					empty( $instance[ 'media_image_' . $i ] ) &&
					// iconFont_class_が空じゃない（ font-awesomeのアイコンが入力されている ）場合
					! empty( $instance[ 'iconFont_class_' . $i ] )
				) {

					$styles = 'border:1px solid ' . esc_attr( $instance[ 'iconFont_bgColor_' . $i ] ) . ';';

					if (
						! isset( $instance[ 'iconFont_bgType_' . $i ] ) ||
						$instance[ 'iconFont_bgType_' . $i ] != 'no_paint'
					) {
						$styles .= 'background-color:' . esc_attr( $instance[ 'iconFont_bgColor_' . $i ] ) . ';';
					}

					echo '<div class="prBlock_icon_outer" style="' . esc_attr( $styles ) . '">';

					if ( isset( $instance[ 'iconFont_bgType_' . $i ] ) && $instance[ 'iconFont_bgType_' . $i ] == 'no_paint' ) {
						$icon_styles = ' style="color:' . esc_attr( $instance[ 'iconFont_bgColor_' . $i ] ) . ';"';
					} else {
						$icon_styles = ' style="color:#fff;"';
					}

					$fa = '';
					if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
						$fa = Vk_Font_Awesome_Versions::print_fa();
					}
					echo '<i class="' . $fa . esc_attr( $instance[ 'iconFont_class_' . $i ] ) . ' font_icon prBlock_icon"' . $icon_styles . '></i></div>' . PHP_EOL;

					// image display
				} elseif ( ! empty( $instance[ 'media_image_' . $i ] ) ) {
					echo '<div class="prBlock_image" style="background:url(' . esc_url( $instance[ 'media_image_' . $i ] ) . ') no-repeat 50% center;background-size: cover;">';
					echo '<img src="' . esc_url( $instance[ 'media_image_' . $i ] ) . '" alt="' . esc_attr( $instance[ 'media_alt_' . $i ] ) . '" />';
					echo '</div><!--//.prBlock_image -->';
				}
				// title text
				echo '<h1 class="prBlock_title">';
				if ( isset( $instance[ 'label_' . $i ] ) && $instance[ 'label_' . $i ] ) {
					echo wp_kses_post( $instance[ 'label_' . $i ] );
				} else {
					_e( 'PR Block', 'vk-all-in-one-expansion-unit' );
				}
				echo '</h1>' . PHP_EOL;

				// summary text
				if ( ! empty( $instance[ 'summary_' . $i ] ) ) {

					echo '<p class="prBlock_summary">' . nl2br( wp_kses_post( $instance[ 'summary_' . $i ] ) ) . '</p>' . PHP_EOL;
				}

				if ( ! empty( $instance[ 'linkurl_' . $i ] ) ) {
					echo '</a>' . PHP_EOL;
				}

				echo '</div>' . PHP_EOL;
				echo '<!--//.prBlock -->' . PHP_EOL;
			}
			$i++;
		}
		echo '</article>' . $args['after_widget'] . PHP_EOL . '<!-- //.pr_blocks -->';
	}
}

// uploader js
function admin_scripts_pr_media() {
	global $hook_suffix;
	if ( 'widgets.php' === $hook_suffix || 'customize.php' === $hook_suffix) {
		wp_enqueue_media();
		wp_register_script( 'vk-admin-widget', plugin_dir_url( __FILE__ ) . 'js/admin-widget.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'vk-admin-widget' );
	}
}
add_action( 'admin_print_scripts', 'admin_scripts_pr_media' );
