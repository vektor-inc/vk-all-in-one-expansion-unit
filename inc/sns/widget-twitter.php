<?php

class VK_Twitter_Widget extends WP_Widget {

	/**
	 * ウィジェット名などを設定
	 */
	public function __construct() {
		$widget_name = veu_get_prefix() . __( 'Twitter', 'vk-all-in-one-expansion-unit' );
		parent::__construct(
			'vk_twitter_widget', // Base ID
			$widget_name, // Name
			array( 'description' => __( 'Display Twitter timeline.', 'vk-all-in-one-expansion-unit' ) ) // Args
		);
		// widget actual processes
	}

	/**
	 * ウィジェットの内容を出力
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		echo $args['before_widget'];
		echo '<div class="vk-teitter-plugin">';
		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo $args['before_title'];
			echo $instance['title'];
			echo $args['after_title'];
		}
		if ( isset( $instance['account'] ) && $instance['account'] ) {
			$account = $instance['account'];
			$account = "https://twitter.com/{$account}?ref_src=twsrc%5Etfw";
		} else {
			return $account = '';
		}
		if ( isset( $instance['height'] ) && $instance['height'] ) {
			$height = $instance['height'];
		} else {
			$height = 400;
		}
	?>
	<a class="twitter-timeline" href="<?php echo esc_url( $account ); ?>" data-height="<?php echo $height; ?>" data-theme="<?php echo wp_kses_post( $instance['bg_color'] ); ?>" data-link-color="<?php echo sanitize_hex_color( $instance['link_color'] ); ?>" data-chrome="noheader nofooter">
	</a>
	<?php
		echo '</div>'; // .vk-twitter-plugin
		echo $args['after_widget'];

		veu_set_twitter_script();
	}

	/**
	 * 管理用のオプションのフォームを出力
	 *
	 * @param array $instance ウィジェットオプション
	 */

	static function time_line_color() {
		return array(
			'light' => 'Light',
			'dark'  => 'Dark',
		);
	}

	public function form( $instance ) {
		// 管理用のオプションのフォームを出力
		/**
	  * 入力された値とデフォルト値を結合するメソッド
	*/
		$defaults = array(
			'title'      => '',
			'account'    => '',
			'height'     => '',
			'bg_color'   => 'light',
			'link_color' => '#2b7bb9',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

	<?php // title ?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'vk-all-in-one-expansion-unit' ); ?></label><br>
	<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="admin-custom-input" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</p>

	<?php // account ?>
	<p><label for="<?php echo $this->get_field_id( 'account' ); ?>"><?php _e( 'Account:<br>Please enter your Twitter account.', 'vk-all-in-one-expansion-unit' ); ?></label><br>
	<?php _e( '@', 'vk-all-in-one-expansion-unit' ); ?><input type="" id="<?php echo $this->get_field_id( 'account' ); ?>" class="" name="<?php echo $this->get_field_name( 'account' ); ?>" value="<?php echo esc_attr( $instance['account'] ); ?>" />
	</p>

	<?php // height ?>
	<p><label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:<br>Please enter an arbitrary number.(Example: 300)', 'vk-all-in-one-expansion-unit' ); ?></label><br>
	<input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" class="admin-custom-input" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr( $instance['height'] ); ?>" />
	</p>

	<?php // bg color ?>
	<p><label for="<?php echo $this->get_field_id( 'bg_color' ); ?>"><?php _e( 'Background color:', 'vk-all-in-one-expansion-unit' ); ?></label><br>
	<select id="<?php echo $this->get_field_id( 'bg_color' ); ?>" name="<?php echo $this->get_field_name( 'bg_color' ); ?>" class="admin-custom-input">
	<?php
	if ( ! isset( $instance['bg_color'] ) || ! $instance['bg_color'] ) {
		$instance['bg_color'] = $default['bg_color'];
	}
	foreach ( static::time_line_color() as $key => $label ) :
	?>
	<option value="<?php echo $key; ?>"
		<?php
		if ( $instance['bg_color'] == $key ) {
			echo 'selected';
		}
	?>
	>
		<?php _e( $label, 'vk-all-in-one-expansion-unit' ); ?>
	</option>
	<?php endforeach; ?>
	</select>
	</p>

	<?php // link color ?>
	<p class="color_picker_wrap">
	<label for="<?php echo $this->get_field_id( 'link_color' ); ?>"><?php _e( 'Link color:', 'vk-all-in-one-expansion-unit' ); ?></label><br>
	<input type="text" id="<?php echo $this->get_field_id( 'link_color' ); ?>" class="color_picker admin-custom-input" name="<?php echo $this->get_field_name( 'link_color' ); ?>" value="
										<?php
										if ( $instance['link_color'] ) {
											echo esc_attr( $instance['link_color'] ); }
?>
" />
	</p>

<?php
	}

	/**
	 * ウィジェットオプションの保存処理
	 *
	 * @param array $new_instance 新しいオプション
	 * @param array $old_instance 以前のオプション
	 */
	public function update( $new_instance, $old_instance ) {
		// ウィジェットオプションの保存処理
		$instance               = $old_instance;
		$instance['title']      = wp_kses_post( $new_instance['title'] );
		$instance['account']    = wp_kses_post( $new_instance['account'] );
		$instance['height']     = wp_kses_post( mb_convert_kana( $new_instance['height'], 'a' ) );
		$instance['bg_color']   = in_array( $new_instance['bg_color'], array_keys( self::time_line_color() ) ) ? $new_instance['bg_color'] : static::$button_default;
		$instance['link_color'] = ( isset( $new_instance['link_color'] ) ) ? sanitize_hex_color( $new_instance['link_color'] ) : false;
		return $instance;
	}
}

add_action( 'widgets_init', 'vkExUnit_widget_set_twitter' );
function vkExUnit_widget_set_twitter() {
	return register_widget( 'VK_Twitter_Widget' );
}
