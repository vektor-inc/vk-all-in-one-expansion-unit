<?php

/*-------------------------------------------*/
/*  child page list widget
/*-------------------------------------------*/

class WP_Widget_vkExUnit_child_page extends WP_Widget {

	function __construct() {
		$widget_name = veu_get_short_name() . ' ' . __( 'Child Page List', 'vkExUnit' );

		parent::__construct(
			'vkExUnit_child_pages',
			$widget_name,
			array( 'description' => __( 'Display the child pages list from ancestor page.', 'vkExUnit' ) )
		);
	}


	function widget( $args, $instance ) {
		if ( ! is_page() ) {
			return; }
		$post_id = get_the_id();
		if ( ! $post_id ) {
			return; }
		$parent_id = 0;

		$post_three   = array();
		$post_three[] = $post_id;

		while ( end( $post_three ) ) {
			$the_query    = new WP_Query(
				array(
					'p'              => end( $post_three ),
					'post_type'      => 'page',
					'posts_per_page' => 1,
				)
			);
			$post_three[] = $the_query->posts[0]->post_parent;
			unset( $the_query );
		}
		$parent_id = $post_three[ count( $post_three ) - 2 ];
		$children  = wp_list_pages( 'title_li=&child_of=' . $parent_id . '&echo=0' );
		if ( ! $children ) {
			return; }
		// var_dump($args);
		echo $args['before_widget'];
		echo '<div class="sideWidget widget_childPageList widget_nav_menu">';
		echo $args['before_title'] . '<a href="' . get_permalink( $post_id ) . '">' . get_the_title( $parent_id ) . '</a></h1>' . $args['after_title'];
		echo '<ul>' . $children . '</ul>';
		echo '</div>';
		echo $args['after_widget'];
	}


	function form( $instance ) {
		?>
<div style="padding:0.6em 0;">
<?php _e( 'Display the child pages list from ancestor page.', 'vkExUnit' ); ?>
</div>
		<?php
	}


	function update( $instance, $old_instance ) {
		return $instance;
	}
}
add_action(
	'widgets_init', function() {
		return register_widget( 'WP_Widget_vkExUnit_child_page' );
	}
);
