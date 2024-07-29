<?php
/*-------------------------------------------*/
/*  Button Widget
/*-------------------------------------------*/
class WP_Widget_Button extends WP_Widget {

	static function button_otherlabels() {
		return array(
			'primary' => 'Key Color(.primary)',
			'default' => 'No paint(.default)',
			'success' => 'Light green(.success)',
			'info'    => 'Light blue(.info)',
			'warning' => 'Orange(.warning)',
			'danger'  => 'Red(.danger)',
		);
	}

	static function defaults() {
		return array(
			'title'       => '',
			'icon_before' => '',
			'icon_after'  => '',
			'subtext'     => '',
			'linkurl'     => '',
			'blank'       => false,
			'size'        => '',
			'color'       => 'primary',
		);
	}

	function __construct() {
		parent::__construct(
			'vkExUnit_button',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		$name = veu_get_prefix() . __( 'Button', 'vk-all-in-one-expansion-unit' );
		// $name .= ' ( ' . __( 'Not recommended', 'vk-all-in-one-expansion-unit' ) . ' )';
		return $name;
	}

	public static function veu_widget_description() {
		$description  = __( 'You can set buttons for arbitrary text.', 'vk-all-in-one-expansion-unit' );
		// $description .= '<br>* * * * * * * * * * * * * * * * * * * * * * * *<br>' . __( '現在はブロックエディタで「VK ボタン」ブロックか WordPress標準の「ボタン」ブロックで代用可能です。', 'vk-all-in-one-expansion-unit' );
		return $description;
	}

	function widget( $args, $instance ) {
		$options = self::get_btn_options( $instance );

		$classes   = array(
			'btn',
			'btn-block',
		);
		$classes[] = 'btn-' . $options['color'];
		if ( in_array( $options['size'], array( 'sm', 'lg' ) ) ) {
			$classes[] = 'btn-' . $options['size'];
		}
		echo $args['before_widget'];

		if ( $options['blank'] ) {
			$blank = ' target="_blank"';
		} else {
			$blank = '';
		}

		if ( $options['linkurl'] && $options['title'] ) : ?>
		<div class="veu_button">
			<a class="<?php echo implode( ' ', $classes ); ?>" href="<?php echo esc_url( $options['linkurl'] ); ?>"<?php echo $blank; ?>>
			<span class="button_mainText">

			<?php

			$fa = '';
			if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
				$fa = Vk_Font_Awesome_Versions::print_fa();
			}

			if ( isset( $instance['icon_before'] ) && $instance['icon_before'] ) {
				echo '<i class="' . $fa . esc_attr( $instance['icon_before'] ) . ' font_icon"></i>';
			}

			echo wp_kses_post( $options['title'] );

			if ( isset( $instance['icon_after'] ) && $instance['icon_after'] ) {
				echo '<i class="' . $fa . esc_attr( $instance['icon_after'] ) . ' font_icon"></i>';
			}
			?>

			</span>
			<?php if ( $options['subtext'] ) : ?>
				<span class="veu_caption button_subText"><?php echo htmlspecialchars( $options['subtext'] ); ?></span>
			<?php endif; ?>
			</a>
		</div>
		<?php endif; ?>
	<?php echo $args['after_widget']; ?>
	<?php
	}

	public static function get_btn_options( $option = array() ) {
		// 以前は maintext に格納していたが後から titile に変更した
		// title が入力されてｋるか 空 の場合 そのままtitleに適用
		if ( isset( $option['title'] ) ) {
			$title = $option['title'];
		} elseif ( ! empty( $option['maintext'] ) ) {
			$title = $option['maintext'];
		} else {
			$title = '';
		}
		$defaults        = static::defaults();
		$option['title'] = $title;
		return wp_parse_args( $option, $defaults );
	}

	function form( $instance ) {
		$instance = self::get_btn_options( $instance );

		?>
		<div class="warp" style="padding: 1em 0;line-height: 2.5em;">

		<?php _e( 'Main text(Required):', 'vk-all-in-one-expansion-unit' ); ?>
		<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" style="width:100%; margin-bottom: 0.5em;" value="<?php echo $instance['title']; ?>">

		<?php
		// icon font class input
		echo '<p>' . __( 'Class name of the icon font', 'vk-all-in-one-expansion-unit' ) . '</label><br/>';
		echo  __( 'To choose your favorite icon, and enter the class.', 'vk-all-in-one-expansion-unit' ) . '<br>';
		echo '<label for="' . $this->get_field_id( 'icon_before' ) . '">' . __( 'Before :', 'vk-all-in-one-expansion-unit' );
		echo '<input type="text" id="' . $this->get_field_id( 'icon_before' ) . '-font" class="font_class" name="' . $this->get_field_name( 'icon_before' ) . '" value="' . esc_attr( $instance['icon_before'] ) . '" /><br>';
		echo '<label for="' . $this->get_field_id( 'icon_after' ) . '">' . __( 'After :', 'vk-all-in-one-expansion-unit' );
		echo '<input type="text" id="' . $this->get_field_id( 'icon_after' ) . '-font" class="font_class" name="' . $this->get_field_name( 'icon_after' ) . '" value="' . esc_attr( $instance['icon_after'] ) . '" /><br>';

		if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
			echo Vk_Font_Awesome_Versions::ex_and_link();
		}

		echo '</p>';
?>
		<?php _e( 'Sub text:', 'vk-all-in-one-expansion-unit' ); ?>
		<input type="text" id="<?php echo $this->get_field_id( 'subtext' ); ?>" name="<?php echo $this->get_field_name( 'subtext' ); ?>" style="width:100%; margin-bottom: 0.5em;" value="<?php echo esc_attr( $instance['subtext'] ); ?>">

		<br/>
			<?php _e( 'Link URL(Required):', 'vk-all-in-one-expansion-unit' ); ?>
		<input type="text" id="<?php echo $this->get_field_id( 'linkurl' ); ?>" name="<?php echo $this->get_field_name( 'linkurl' ); ?>" value="<?php echo esc_attr( $instance['linkurl'] ); ?>" style="width: 100%" />

		<br/>
		<input type="checkbox" id="<?php echo $this->get_field_id( 'blank' ); ?>" name="<?php echo $this->get_field_name( 'blank' ); ?>" value="true"
												<?php
												if ( $instance['blank'] ) {
													echo 'checked';}
?>
  />
		<label for="<?php echo $this->get_field_id( 'blank' ); ?>"><?php _e( 'Open with new tab', 'vk-all-in-one-expansion-unit' ); ?></label>

		<br/>
		<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size', 'vk-all-in-one-expansion-unit' ); ?> :</label>
		<select id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>">
			<option value="sm"
			<?php
			if ( $instance['size'] == 'sm' ) {
				echo 'selected';}
?>
 ><?php _e( 'Small', 'vk-all-in-one-expansion-unit' ); ?></option>
			<option value="md"
			<?php
			if ( ! in_array( $instance['size'], array( 'sm', 'lg' ) ) ) {
				echo 'selected';}
?>
 ><?php _e( 'Medium', 'vk-all-in-one-expansion-unit' ); ?></option>
			<option value="lg"
			<?php
			if ( $instance['size'] == 'lg' ) {
				echo 'selected';}
?>
 ><?php _e( 'Large', 'vk-all-in-one-expansion-unit' ); ?></option>
		</select>

		<br/>
		<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Button color:', 'vk-all-in-one-expansion-unit' ); ?> </label>
		<select id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>">
		<?php
		if ( ! isset( $instance['color'] ) || ! $instance['color'] ) {
			$instance['color'] = $default['color'];
		}
		foreach ( static::button_otherlabels() as $key => $label ) :
		?>
			<option value="<?php echo $key; ?>"
										<?php
										if ( $instance['color'] == $key ) {
											echo 'selected';}
?>
 >
			<?php _e( $label, 'vk-all-in-one-expansion-unit' ); ?>
			</option>
		<?php endforeach; ?>
		</select>
		</div>
		<?php
	}


	function update( $new_instance, $old_instance ) {
		$opt                = array();
		$opt['title']       = wp_kses_post( stripslashes( $new_instance['title'] ) );
		$opt['icon_before'] = wp_kses_post( $new_instance['icon_before'] );
		$opt['icon_after']  = wp_kses_post( $new_instance['icon_after'] );
		$opt['subtext']     = wp_kses_post( stripslashes( $new_instance['subtext'] ) );
		$opt['linkurl']     = esc_url( $new_instance['linkurl'] );
		$opt['blank']       = ( isset( $new_instance['blank'] ) && $new_instance['blank'] == 'true' );
		$opt['size']        = in_array( $new_instance['size'], array( 'sm', 'lg' ) ) ? $new_instance['size'] : 'md';
		$opt['color']       = in_array( $new_instance['color'], array_keys( self::button_otherlabels() ) ) ? $new_instance['color'] : static::$button_default;
		return $opt;
	}

	public static function dummy() {
		__( 'Key Color(.primary)', 'vk-all-in-one-expansion-unit' );
		__( 'No paint(.default)', 'vk-all-in-one-expansion-unit' );
		__( 'Light green(.success)', 'vk-all-in-one-expansion-unit' );
		__( 'Light blue(.info)', 'vk-all-in-one-expansion-unit' );
		__( 'Orange(.warning)', 'vk-all-in-one-expansion-unit' );
		__( 'Red(.danger)', 'vk-all-in-one-expansion-unit' );
	}
} // class WP_Widget_Button extends WP_Widget {
