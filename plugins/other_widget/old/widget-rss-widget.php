<?php
/*-------------------------------------------*/
/*  RSS widget
/*-------------------------------------------*/
class wp_widget_bizvektor_rss extends WP_Widget {
	function wp_widget_bizvektor_rss() {
		$widget_ops = array(
			'classname' => 'wp_widget_bizvektor_rss',
			'description' => __( 'Displays entries list from a RSS feed link.', 'biz-vektor' ),
		);
		$widget_name = vkExUnit_get_short_name().'_' . __( 'RSS entries for top', 'biz-vektor' );
		$this->WP_Widget( 'rsswidget', $widget_name, $widget_ops );
	}
	function widget( $args, $instance ) {
		$options = biz_vektor_get_theme_options();
		if ( preg_match( '/^http.*$/',$instance['url'] ) ) {
			echo '<div id="rss_widget">';
			biz_vektor_blogList( $instance );
			echo '</div>';
		}
	}
	function form( $instance ) {
		$defaults = array(
			'url' => '',
			'label' => __( 'Blog entries', 'biz-vektor' ),
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
		<Label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Heading title', 'biz-vektor' ) ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
        <br/>
		<Label for="<?php echo $this->get_field_id( 'url' ); ?>">URL</label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" />
		<?php
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['url'] = $new_instance['url'];
		$instance['label'] = $new_instance['label'];
		return $instance;
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("wp_widget_bizvektor_rss");' ) );
