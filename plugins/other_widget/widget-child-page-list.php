<?php

/*-------------------------------------------*/
/*  child page list widget
/*-------------------------------------------*/

class WP_Widget_vkExUnit_child_page extends WP_Widget {

	function __construct() {
		$widget_name = vkExUnit_get_short_name(). '_' . __( "Child Page List", 'vkExUnit' );

		parent::__construct(
			'vkExUnit_child_pages',
			$widget_name,
			array( 'description' => __('Display child pages list of current page.', 'vkExUnit') )
		);
	}


	function widget($args, $instance) {
		if( !is_page() ) return;
		$post_id = get_the_id();
		if( !$post_id ) return;
		$children = wp_list_pages("title_li=&child_of=".$post_id."&echo=0");
		if( !$children ) return;
?>
<aside class="widget sideWidget widget_childPageList widget_nav_menu">
<h1 class="widget-title subSection-title"><a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a></h1>
<ul>
<?php echo $children; ?>
</ul>
</aside>
<?php
	}


	function form($instance) {
		?>
<div style="padding:0.6em 0;">
<?php _e( 'This is only parent page.', 'vkExUnit' ); ?>
</div>
		<?php
	}


	function update($instance, $old_instance) {
		return $instance;
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_vkExUnit_child_page");'));