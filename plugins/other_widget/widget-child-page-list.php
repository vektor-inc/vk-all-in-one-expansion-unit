<?php

/*-------------------------------------------*/
/*  child page list widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_child_page extends WP_Widget {

    function WP_Widget_vkExUnit_child_page() {
        $widget_opts = array(
            'classname'   => 'WP_Widget_vkExUnit_child_page',
            'description' => __('Display child pages list of current page.', 'vkExUnit')
        );
        $widget_name = vkExUnit_get_short_name(). '_' . __( "Child Page List", 'vkExUnit' );
        $this->WP_Widget('WP_Widget_vkExUnit_child_page', $widget_name, $widget_opts);
    }


    function widget($args, $instance) {
        if( !is_page() ) return;
        $post_id = get_the_id();
        if( !$post_id ) return;
        $children = wp_list_pages("title_li=&child_of=".$post_id."&echo=0");
        if( !$children ) return;
?>
<div class="localSection sideWidget pageListSection">
<h3 class="localHead"><a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a></h3>
<ul class="localNavi">
<?php echo $children; ?>
</ul>
</div>
<?php
    }


    function form($instance) {
    }


    function update($new_instance, $old_instance) {
    }
}
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_vkExUnit_child_page");'));