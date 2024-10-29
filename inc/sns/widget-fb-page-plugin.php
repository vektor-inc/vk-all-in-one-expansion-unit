<?php
/*-------------------------------------------*/
/*  fbPagePlugin widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_fbPagePlugin extends WP_Widget {

	function __construct() {
		$widget_name = veu_get_prefix() . 'FB Page Plugin';

		parent::__construct(
			'vkExUnit_fbPagePlugin',
			$widget_name,
			array( 'description' => __( 'Displays a Facebook Page Plugin', 'vk-all-in-one-expansion-unit' ) )
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
		$showPosts = ( isset( $instance['showPosts'] ) && $instance['showPosts'] ) ? $instance['showPosts'] : 'true';
		?>

		<div class="fbPagePlugin_body">
			<div class="fb-page" data-href="<?php echo esc_url( $page_url ); ?>" data-width="500"  data-height="<?php echo esc_attr( $height ); ?>" data-hide-cover="<?php echo esc_attr( $hideCover ); ?>" data-show-facepile="<?php echo esc_attr( $showFaces ); ?>" data-show-posts="<?php echo esc_attr( $showPosts ); ?>">
				<div class="fb-xfbml-parse-ignore">
					<blockquote cite="<?php echo $page_url; ?>">
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
		$instance              = $old_instance;
		$instance['label']     = wp_kses_post( stripslashes($new_instance['label'] ) );
		$instance['page_url']  = esc_url( $new_instance['page_url'] );
		$instance['height']    = esc_html( $new_instance['height'] );
		$instance['showFaces'] = $new_instance['showFaces'];
		$instance['hideCover'] = $new_instance['hideCover'];
		$instance['showPosts'] = $new_instance['showPosts'];

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

		<?php //タイトル ?>
		<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
		<br/>

		<?php //URL ?>
		<label for="<?php echo $this->get_field_id( 'page_url' ); ?>"><?php echo 'Facebook Page URL'; ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'page_url' ); ?>" name="<?php echo $this->get_field_name( 'page_url' ); ?>" value="<?php echo $instance['page_url']; ?>" />
		<br/>

		<?php //Height ?>
		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" />
		<br/>

		<?php //showFaces ?>
		<label for="<?php echo $this->get_field_id( 'showFaces' ); ?>"><?php _e( "Show Friend's Faces", 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'showFaces' ); ?>" value="true" <?php echo ( $instance['showFaces'] == 'true' ) ? 'checked' : ''; ?> >
		<br/>

		<?php //hideCover ?>
		<label for="<?php echo $this->get_field_id( 'hideCover' ); ?>"><?php _e( 'Hide Cover Photo', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'hideCover' ); ?>" value="true" <?php echo ( $instance['hideCover'] == 'true' ) ? 'checked' : ''; ?> >
		<br/>

		<?php //showPosts ?>
		<label for="<?php echo $this->get_field_id( 'showPosts' ); ?>"><?php _e( 'Show Page Posts', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'showPosts' ); ?>" value="true" <?php echo ( $instance['showPosts'] == 'true' ) ? 'checked' : ''; ?> >
		<br/>
		<?php
	}
} // class WP_Widget_top_list_post
add_action(
	'widgets_init', function() {
		return register_widget( 'WP_Widget_vkExUnit_fbPagePlugin' );
	}
);
