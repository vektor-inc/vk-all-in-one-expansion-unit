<?php
/*-------------------------------------------*/
/*	ChildPageList widget
/*-------------------------------------------*/
class WP_Widget_ChildPageList extends WP_Widget {
	function WP_Widget_childPageList() {
		$widget_ops = array(
			'classname' => 'WP_Widget_childPageList',
			'description' => __( 'Displays list of child page for the current page.', 'biz-vektor' ),
		);
		$widget_name = vkExUnit_get_short_name() . '_' . __( 'child pages list', 'biz-vektor' );
		$this->WP_Widget('childPageList', $widget_name, $widget_ops);
	}
	function widget($args, $instance) {
		extract( $args );
		if(biz_vektor_childPageList()){
			echo $before_widget;
			biz_vektor_childPageList();
			echo $after_widget;
		}
	}
	function form($instance){
	}
	function update($new_instance,$old_instance){
	}
} // class WP_Widget_childPageList
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_childPageList");'));