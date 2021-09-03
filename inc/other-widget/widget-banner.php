<?php

class WidgetBanner extends WP_Widget {
	function __construct() {
		parent::__construct(
			'vkExUnit_banner',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		$name = veu_get_prefix() . __( 'Banner', 'vk-all-in-one-expansion-unit' );
		// $name .= ' ( ' . __( 'Not recommended', 'vk-all-in-one-expansion-unit' ) . ' )';
		return $name;
	}

	public static function veu_widget_description() {
		$description  = __( 'You can easily set up a banner simply by registering images and link destinations.', 'vk-all-in-one-expansion-unit' );
		// $description .= '<br>* * * * * * * * * * * * * * * * * * * * * * * *<br>' . __( '現在はWordPress標準の画像ウィジェットかブロックエディタの画像ブロックで代用可能です。', 'vk-all-in-one-expansion-unit' );
		return $description;
	}

	public function widget( $args, $instance ) {
		$instance = self::get_bnr_option( $instance );
		$image    = null;
		if ( is_numeric( $instance['id'] ) ) {
			$image = wp_get_attachment_image_src( $instance['id'], 'full' );
			$alt   = ( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		}
		if ( ! $image ) {
			return;
		}
		echo $args['before_widget'];
		if ( $instance['href'] ) {
			echo '<a href="' . esc_url( $instance['href'] ) . '" class="veu_banner"' . ( ( $instance['blank'] ) ? ' target="_blank"' : '' ) . ' >';
		}
		echo '<img src="' . $image[0] . '" alt="' . $alt . '" />';
		if ( $instance['href'] ) {
			echo '</a>';
		}
		echo $args['after_widget'];

		return;
	}

	public function update( $new_instance, $old_instance ) {
		$instance['id']    = $new_instance['id'];
		$instance['href']  = $new_instance['href'];
		$instance['title'] = $new_instance['title'];
		$instance['blank'] = ( isset( $new_instance['blank'] ) && $new_instance['blank'] == 'true' );
		return $new_instance;
	}

	public static function get_bnr_option( $instance = array() ) {

		// 以前は alt に格納していたが後から titile に変更した
		// title が入力されてｋるか 空 の場合 そのままtitleに適用
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} elseif ( ! empty( $instance['alt'] ) ) {
			$title = $instance['alt'];
		} else {
			$title = '';
		}
		$instance['title'] = $title;

		$defaults = array(
			'id'    => null,
			'href'  => '',
			'blank' => false,
			'title' => '',
		);

		return wp_parse_args( $instance, $defaults );
	}


	public function form( $instance ) {
		$instance = self::get_bnr_option( $instance );
		$image    = null;
		if ( is_numeric( $instance['id'] ) ) {
			$image = wp_get_attachment_image_src( $instance['id'], 'large' );
		}
		?>
<div class="vkExUnit_banner_area" style="padding: 2em 0;">

	<!-- [ .media_image_section ] -->
	<div class="media_image_section">

		<div class="_display admin-custom-thumb-outer" style="height:auto">
			<?php if ( ! empty( $image ) ) : ?>
				<img src="<?php echo esc_url( $image[0] ); ?>" class="admin-custom-thumb" />
			<?php endif; ?>
		</div>

		<button
			class="button button-default widget_media_btn_select"
			style="text-align: center; margin:4px 0;"
			onclick="javascript:vk_widget_image_add(this);return false;"
		>
			<?php _e( 'Select image', 'vk-all-in-one-expansion-unit' ); ?>
		</button>

		<button
			class="button button-default widget_media_btn_reset"
			style="text-align: center; margin:4px 0;"
			onclick="javascript:vk_widget_image_del(this);return false;"
		>
			<?php _e( 'Clear image', 'vk-all-in-one-expansion-unit' ); ?>
		</button>

		<div class="_form" style="line-height: 2em">
			<input
				type="hidden"
				class="_id"
				name="<?php echo $this->get_field_name( 'id' ); ?>"
				value="<?php echo esc_attr( $instance['id'] ); ?>"
			/>
		</div>

	</div><!-- [ /.media_image_section ] -->

	<label>
		<?php _e( 'Alternative text', 'vk-all-in-one-expansion-unit' ); ?> :
		<input
			class="_alt"
			type="text"
			id="<?php echo $this->get_field_id( 'title' ); ?>"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			style="width: 100%"
			value="<?php echo esc_attr( $instance['title'] ); ?>" 
		/>
	</label>
	<br/>
	<label>
		URL :
		<input
			type="text"
			name="<?php echo $this->get_field_name( 'href' ); ?>"
			style="width: 100%"
			value="<?php echo esc_attr( $instance['href'] ); ?>"
		/>
	</label>
	<br/>
	<label>
		<input
			type="checkbox"
			name="<?php echo $this->get_field_name( 'blank' ); ?>"
			value="true"
			<?php
			if ( $instance['blank'] ) {
				echo 'checked';
			}
			?>
		/>
		<?php _e( 'Open link new tab.', 'vk-all-in-one-expansion-unit' ); ?>
	</label>

</div>
		<?php
		return $instance;
	}
}
