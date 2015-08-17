<?php

/*-------------------------------------------*/
/*	page widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_widget_page extends WP_Widget {
	
	function WP_Widget_vkExUnit_widget_page() {
		$widget_ops = array(
			'classname' => 'WP_Widget_vkExUnit_widget_page',
			'description' => __( 'Displays a page contents to widget.', 'vkExUnit' ),
		);
		$widget_name = vkExUnit_get_short_name() . '_' . __( 'page content to widget', 'vkExUnit' );
		$this->WP_Widget('pudge', $widget_name, $widget_ops);
	}
	
	function widget($args, $instance){
		$this->display_page($instance['page_id'],$instance['set_title']);
	}

	function form($instance){
		$defaults = array(
			'page_id' => 2,
			'set_title' => true
		);

		$instance = wp_parse_args((array) $instance, $defaults); ?>
		<p>
		<?php 	$pages = get_pages();	?>
		<label for="<?php echo $this->get_field_id('page_id'); ?>"><?php _e('Display page', 'vkExUnit') ?></label>
		<select name="<?php echo $this->get_field_name('page_id'); ?>" >
		<?php foreach($pages as $page){ ?>
		<option value="<?php echo $page->ID; ?>" <?php if($instance['page_id'] == $page->ID) echo 'selected="selected"'; ?> ><?php echo $page->post_title; ?></option>
		<?php } ?>
		</select>
		<br/>
		<input type="checkbox" name="<?php echo $this->get_field_name('set_title'); ?>" value="true" <?php echo ($instance['set_title'])? 'checked': '' ; ?> >
		<label for="<?php echo $this->get_field_id('set_title'); ?>"> <?php _e( 'display title', 'vkExUnit' ); ?></label>
		</p>
		<?php
	}
	
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['page_id'] = $new_instance['page_id'];
		$instance['set_title'] = ($new_instance['set_title'] == 'true')? true : false;
		return $instance;
	}
	
	function display_page($pageid,$titleflag=false) {
		
		$page = get_page($pageid);
		echo PHP_EOL.'<div id="widget-page-'.$pageid.'" class="widget widget_pageContent">'.PHP_EOL;
		if($titleflag){ echo '<h1 class="widget-title">'.$page->post_title.'</h1>'.PHP_EOL; }
		remove_filter( 'the_content', 'vkExUnit_add_snsBtns' );	
		echo apply_filters('the_content', $page->post_content );
		add_filter( 'the_content', 'vkExUnit_add_snsBtns');
		
		if ( is_user_logged_in() == TRUE ) {
			global $user_level;
			get_currentuserinfo();
			if (10 <= $user_level) { ?>
	<div class="adminEdit">
		<a href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo $pageid ;?>&action=edit" class="btn btn-default btn-sm"><?php _e('Edit', 'vkExUnit');?></a>
	</div>
<?php } }
		echo '</div>'.PHP_EOL;
	}
}
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_vkExUnit_widget_page");'));