<?php

/*-------------------------------------------*/
/*  PR area widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_3PR_area extends WP_Widget {
	function __construct() {
		parent::__construct(
			'WP_Widget_vkExUnit_3PR_area',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		$name = veu_get_prefix() . __( '3PR area', 'vk-all-in-one-expansion-unit' );
		// $name .= ' ( ' . __( 'Not recommended', 'vk-all-in-one-expansion-unit' ) . ' )';
		return $name;
	}

	public static function veu_widget_description() {
		return __( 'Displays a 3PR area', 'vk-all-in-one-expansion-unit' );
	}

	public static function default_options( $args = array() ) {
		$defaults = array(
			'label_1'              => __( '3PR area1 title', 'vk-all-in-one-expansion-unit' ),
			'media_3pr_image_1'    => '',
			'media_3pr_alt_1'      => '',
			'media_3pr_image_sp_1' => '',
			'media_3pr_alt_sp_1'   => '',
			'summary_1'            => '',
			'linkurl_1'            => '',
			'blank_1'              => false,
			'label_2'              => __( '3PR area2 title', 'vk-all-in-one-expansion-unit' ),
			'media_3pr_image_2'    => '',
			'media_3pr_alt_2'      => '',
			'media_3pr_image_sp_2' => '',
			'media_3pr_alt_sp_2'   => '',
			'summary_2'            => '',
			'linkurl_2'            => '',
			'blank_2'              => false,
			'label_3'              => __( '3PR area3 title', 'vk-all-in-one-expansion-unit' ),
			'media_3pr_image_3'    => '',
			'media_3pr_alt_3'      => '',
			'media_3pr_image_sp_3' => '',
			'media_3pr_alt_sp_3'   => '',
			'summary_3'            => '',
			'linkurl_3'            => '',
			'blank_3'              => false,
		);
		return wp_parse_args( (array) $args, $defaults );
	}


	function form( $instance ) {
		$instance = self::default_options( $instance );

		for ( $i = 1; $i <= 3; ) { ?>

		<h2 class="admin-custom-h2"><?php echo __( '3PR area setting', 'vk-all-in-one-expansion-unit' ) . ' ' . $i; ?></h2>
		<p>
			<label for="<?php echo $this->get_field_id( 'label_' . $i ); ?>"><?php _e( 'Title:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id( 'label_' . $i ); ?>-title" class="pr-input" name="<?php echo $this->get_field_name( 'label_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'label_' . $i ] ); ?>" />
		</p>

		<?php // 3PR area 1 メディアアップローダー PC ?>

				<label for="<?php echo $this->get_field_id( 'media_3pr_image_' . $i ); ?>"><?php _e( 'Select image for PC:', 'vk-all-in-one-expansion-unit' ); ?></label>

				<div class="media_image_section">
					<div class="_display admin-custom-thumb-outer">
						<?php
						if ( ! empty( $instance[ 'media_3pr_image_' . $i ] ) ) :
							?>
							<img class="media_image" src="<?php echo esc_url( $instance[ 'media_3pr_image_' . $i ] ); ?>" alt="<?php echo esc_attr( $instance[ 'media_3pr_alt_' . $i ] ); ?>" style="width:100%;height:auto;" />
						<?php endif; ?>
					</div>
					<button class="button button-default widget_media_btn_select" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_add(this);return false;"><?php _e( 'Select image', 'vk-all-in-one-expansion-unit' ); ?></button>
					<button class="button button-default widget_media_btn_reset" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_del(this);return false;"><?php _e( 'Clear image', 'vk-all-in-one-expansion-unit' ); ?></button>
					<div class="_form" style="line-height: 2em">
							<input type="hidden" class="_url" name="<?php echo $this->get_field_name( 'media_3pr_image_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'media_3pr_image_' . $i ] ); ?>" />
							<input type="hidden" class="_alt" name="<?php echo $this->get_field_name( 'media_3pr_alt_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'media_3pr_alt_' . $i ] ); ?>" />
					</div>
				</div><!-- [ /.media_image_section ] -->

				<label for="<?php echo $this->get_field_id( 'media_3pr_image_sp_' . $i ); ?>"><?php _e( 'Select image for Mobile:', 'vk-all-in-one-expansion-unit' ); ?></label>

				<div class="media_image_section">
					<div class="_display admin-custom-thumb-outer">
						<?php
						if ( ! empty( $instance[ 'media_3pr_image_sp_' . $i ] ) ) :
							?>
							<img class="media_image" src="<?php echo esc_url( $instance[ 'media_3pr_image_sp_' . $i ] ); ?>" alt="<?php echo esc_attr( $instance[ 'media_3pr_alt_' . $i ] ); ?>" style="width:100%;height:auto;" />
						<?php endif; ?>
					</div>
					<button class="button button-default widget_media_btn_select" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_add(this);return false;"><?php _e( 'Select image', 'vk-all-in-one-expansion-unit' ); ?></button>
					<button class="button button-default widget_media_btn_reset" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_del(this);return false;"><?php _e( 'Clear image', 'vk-all-in-one-expansion-unit' ); ?></button>
					<div class="_form" style="line-height: 2em">
							<input type="hidden" class="_url" name="<?php echo $this->get_field_name( 'media_3pr_image_sp_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'media_3pr_image_sp_' . $i ] ); ?>" />
							<input type="hidden" class="_alt" name="<?php echo $this->get_field_name( 'media_3pr_alt_sp_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'media_3pr_alt_sp_' . $i ] ); ?>" />
					</div>
				</div><!-- [ /.media_image_section ] -->

		<?php // 3PR area 1 メディアアップローダー sp image ?>
		   <br/>

		<?php // 概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id( 'summary_' . $i ); ?>"><?php _e( 'Summary Text:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		</p>

		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id( 'summary_' . $i ); ?>_text" class="admin-custom-input" name="<?php echo $this->get_field_name( 'summary_' . $i ); ?>"><?php echo esc_textarea( $instance[ 'summary_' . $i ] ); ?></textarea>

		<?php // リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id( 'linkurl_' . $i ); ?>"><?php _e( 'Link URL:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'linkurl_' . $i ); ?>_title" class="pr_input text" name="<?php echo $this->get_field_name( 'linkurl_' . $i ); ?>" value="<?php echo esc_attr( $instance[ 'linkurl_' . $i ] ); ?>" style="margin-bottom:0.5em;" /><br/>
		<input type="checkbox" id="<?php echo $this->get_field_id( 'blank_' . $i ); ?>" class="pr_input checkbox" name="<?php echo $this->get_field_name( 'blank_' . $i ); ?>"
												<?php
												if ( $instance[ 'blank_' . $i ] ) {
													echo 'checked';}
?>
 value="true" />
		<label for="<?php echo $this->get_field_id( 'blank_' . $i ); ?>"><?php _e( 'Open link new tab.', 'vk-all-in-one-expansion-unit' ); ?></label>
		</p>

<hr />

<?php
			$i++;
		} // for ( $i = 1; $i <= 3 ;) {

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		for ( $i = 1; $i <= 3; ) {
			$instance[ 'label_' . $i ]              = wp_kses_post( stripslashes($new_instance[ 'label_' . $i ] ) );
			$instance[ 'media_3pr_image_' . $i ]    = esc_url( $new_instance[ 'media_3pr_image_' . $i ] );
			$instance[ 'media_3pr_alt_' . $i ]      = esc_html( stripslashes( $new_instance[ 'media_3pr_alt_' . $i ] ) );
			$instance[ 'media_3pr_image_sp_' . $i ] = esc_url( $new_instance[ 'media_3pr_image_sp_' . $i ] );
			$instance[ 'media_3pr_alt_sp_' . $i ]   = esc_html( stripslashes( $new_instance[ 'media_3pr_alt_sp_' . $i ] ) );
			$instance[ 'summary_' . $i ]            = wp_kses_post( stripslashes( $new_instance[ 'summary_' . $i ] ) );
			$instance[ 'linkurl_' . $i ]            = esc_url( $new_instance[ 'linkurl_' . $i ] );
			$instance[ 'blank_' . $i ]              = ( isset( $new_instance[ 'blank_' . $i ] ) && $new_instance[ 'blank_' . $i ] == 'true' );
			$i++;
		}

		return $instance;
	}


	function widget( $args, $instance ) {
		$instance = self::default_options( $instance );
		echo $args['before_widget'];
		echo '<div class="veu_3prArea row">';
		for ( $i = 1; $i <= 3; ) {

			if ( isset( $instance[ 'label_' . $i ] ) && $instance[ 'label_' . $i ] ) {
				echo '<div class="prArea col-sm-4">';

				echo '<h1 class="subSection-title">';
				if ( isset( $instance[ 'label_' . $i ] ) && $instance[ 'label_' . $i ] ) {
					echo wp_kses_post( $instance[ 'label_' . $i ] );
				} else {
					_e( '3PR area', 'vk-all-in-one-expansion-unit' );
				}
				echo '</h1>';

				$blank = ( isset( $instance[ 'blank_' . $i ] ) && $instance[ 'blank_' . $i ] ) ? ' target="_blank" ' : '';

				if ( isset( $instance[ 'media_3pr_image_' . $i ], $instance[ 'media_3pr_image_sp_' . $i ] ) && $instance[ 'media_3pr_image_' . $i ] ) {

					// media_pr は現在不使用 近日削除
					echo '<div class="media_pr veu_3prArea_image">';

					if ( ! empty( $instance[ 'linkurl_' . $i ] ) ) {
						echo '<a href="' . esc_url( $instance[ 'linkurl_' . $i ] ) . '" class="veu_3prArea_image_link"' . $blank . '>';
					}

					if ( ! empty( $instance[ 'media_3pr_image_' . $i ] ) ) {
						$class = ( ! empty( $instance[ 'media_3pr_image_sp_' . $i ] ) ) ? ' class="image_pc"' : '';
						echo '<img' . $class . ' src="' . esc_url( $instance[ 'media_3pr_image_' . $i ] ) . '" alt="' . esc_attr( $instance[ 'media_3pr_alt_' . $i ] ) . '" />';
					}

					if ( ! empty( $instance[ 'media_3pr_image_sp_' . $i ] ) ) {
						echo '<img class="image_sp" src="' . esc_url( $instance[ 'media_3pr_image_sp_' . $i ] ) . '" alt="' . esc_attr( $instance[ 'media_3pr_alt_sp_' . $i ] ) . '" />';
					}

					if ( ! empty( $instance[ 'linkurl_' . $i ] ) ) {
						echo '</a>';
					}

					echo '</div>';
				}

				if ( ! empty( $instance[ 'summary_' . $i ] ) ) {

					echo '<p class="summary">' . nl2br( wp_kses_post( $instance[ 'summary_' . $i ] ) ) . '</p>';

				}
				if ( ! empty( $instance[ 'linkurl_' . $i ] ) ) {
					echo '<p class="linkurl"><a href="' . esc_url( $instance[ 'linkurl_' . $i ] ) . '" class="btn btn-default btn-sm"' . $blank . '>' . apply_filters( 'vkExUnit_widget_3pr_read_more_txt', __( 'Read more', 'vk-all-in-one-expansion-unit' ) ) . '</a></p>';
				}

				echo '</div>';
			} // if ( isset( $instance['label_'.$i] ) && $instance['label_'.$i] ) {

			$i++;
		} // for ( $i = 1; $i <= 3 ;) {
?>
	</div>
	<?php
		echo $args['after_widget'];
	}
}

// メディアアップローダーjs
function admin_scripts_3pr_media() {
	global $hook_suffix;
	if ( 'widgets.php' === $hook_suffix || 'customize.php' === $hook_suffix) {
		wp_enqueue_media();
		wp_register_script( 'vk-admin-widget', plugin_dir_url( __FILE__ ) . 'js/admin-widget.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'vk-admin-widget' );
	}
}
add_action( 'admin_print_scripts', 'admin_scripts_3pr_media' );

