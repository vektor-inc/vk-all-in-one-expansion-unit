<?php
/*
	fbPagePlugin widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_fbPagePlugin extends WP_Widget {

	function __construct() {
		$widget_name = veu_get_prefix() . 'FB Page Plugin';

		parent::__construct(
			'vkExUnit_fbPagePlugin',
			$widget_name,
			array(
				'description'           => __( 'Displays a Facebook Page Plugin', 'vk-all-in-one-expansion-unit' ),
				// インスタンス設定を REST API に出力し、ブロックウィジェット編集画面でブロック内に自己完結で保持・編集できるようにする（参照ウィジェット扱いによる非表示を防ぐ）。
				// Expose the instance settings to the REST API so the block-based widgets editor can keep and edit them inline (prevents the widget from being hidden as a reference widget).
				'show_instance_in_rest' => true,
			)
		);
	}


	function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		echo '<div class="veu_fbPagePlugin">';
		if ( isset( $instance['label'] ) && $instance['label'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo wp_kses_post( $instance['label'] );
			echo wp_kses_post( $args['after_title'] );
		}

		$page_url  = ( isset( $instance['page_url'] ) && $instance['page_url'] ) ? $instance['page_url'] : '';
		$height    = ( isset( $instance['height'] ) && $instance['height'] ) ? $instance['height'] : 200;
		$showFaces = ( isset( $instance['showFaces'] ) && $instance['showFaces'] ) ? $instance['showFaces'] : 'false';
		$hideCover = ( isset( $instance['hideCover'] ) && $instance['hideCover'] ) ? $instance['hideCover'] : 'false';
		$showPosts = ( ! array_key_exists( 'showPosts', $instance ) || 'true' === $instance['showPosts'] ) ? 'true' : 'false';
		$tabs      = ( 'true' === $showPosts ) ? 'timeline' : '';
		?>

		<div class="fbPagePlugin_body">
			<div class="fb-page" data-href="<?php echo esc_url( $page_url ); ?>" data-width="500"  data-height="<?php echo esc_attr( $height ); ?>" data-hide-cover="<?php echo esc_attr( $hideCover ); ?>" data-show-facepile="<?php echo esc_attr( $showFaces ); ?>"<?php echo ( $tabs ) ? ' data-tabs="' . esc_attr( $tabs ) . '"' : ''; ?>>
				<div class="fb-xfbml-parse-ignore">
					<blockquote cite="<?php echo esc_url( $page_url ); ?>">
					<a href="<?php echo esc_url( $page_url ); ?>">Facebook page</a>
					</blockquote>
				</div>
			</div>
		</div>

		<?php
		echo '</div>';
		echo wp_kses_post( $args['after_widget'] );

		veu_set_facebook_script();
	} // widget($args, $instance)


	function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['label']    = wp_kses_post( stripslashes( $new_instance['label'] ) );
		$instance['page_url'] = esc_url( $new_instance['page_url'] );
		$instance['height']   = esc_html( $new_instance['height'] );
		// Checkboxes are omitted from POST data when unchecked, so guard with isset() to avoid the
		// PHP 8.x undefined array key warning. Fall back to '' (an empty, falsy value) so widget()
		// can distinguish explicitly unchecked values from legacy instances without the key.
		$instance['showFaces'] = isset( $new_instance['showFaces'] ) ? $new_instance['showFaces'] : '';
		$instance['hideCover'] = isset( $new_instance['hideCover'] ) ? $new_instance['hideCover'] : '';
		$instance['showPosts'] = isset( $new_instance['showPosts'] ) ? $new_instance['showPosts'] : '';

		return $instance;
	}


	function form( $instance ) {

		$defaults = array(
			'label'     => 'Facebook',
			'page_url'  => '',
			'height'    => 600,
			'showFaces' => 'false',
			'hideCover' => 'false',
			'showPosts' => 'true',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<?php // タイトル ?>
		<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
		<br/>

		<?php // URL ?>
		<label for="<?php echo $this->get_field_id( 'page_url' ); ?>"><?php echo 'Facebook Page URL'; ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'page_url' ); ?>" name="<?php echo $this->get_field_name( 'page_url' ); ?>" value="<?php echo $instance['page_url']; ?>" />
		<br/>

		<?php // Height ?>
		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" />
		<br/>

		<?php // showFaces ?>
		<label for="<?php echo $this->get_field_id( 'showFaces' ); ?>"><?php _e( "Show Friend's Faces", 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'showFaces' ); ?>" value="true" <?php echo ( $instance['showFaces'] == 'true' ) ? 'checked' : ''; ?> >
		<br/>

		<?php // hideCover ?>
		<label for="<?php echo $this->get_field_id( 'hideCover' ); ?>"><?php _e( 'Hide Cover Photo', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'hideCover' ); ?>" value="true" <?php echo ( $instance['hideCover'] == 'true' ) ? 'checked' : ''; ?> >
		<br/>

		<?php // showPosts ?>
		<label for="<?php echo $this->get_field_id( 'showPosts' ); ?>"><?php _e( 'Show Page Posts', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'showPosts' ); ?>" value="true" <?php echo ( $instance['showPosts'] == 'true' ) ? 'checked' : ''; ?> >
		<br/>
		<?php
	}
} // class WP_Widget_top_list_post
add_action(
	'widgets_init',
	function () {
		return register_widget( 'WP_Widget_vkExUnit_fbPagePlugin' );
	}
);
