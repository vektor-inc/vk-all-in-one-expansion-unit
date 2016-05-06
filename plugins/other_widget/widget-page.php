<?php

/*-------------------------------------------*/
/*  page widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_widget_page extends WP_Widget {

	function __construct() {
		$widget_name = vkExUnit_get_short_name() . '_' . __( 'page content to widget', 'vkExUnit' );

		parent::__construct(
			'pudge',
			$widget_name,
			array( 'description' => __( 'Displays a page contents to widget.', 'vkExUnit' ) )
		);
	}

	function widget( $args, $instance ) {
		global $is_pagewidget;
		$is_pagewidget = true;
		$this->display_page( $instance['page_id'],$instance['set_title'],$args );
		$is_pagewidget = false;
	}

	function form( $instance ) {
		$defaults = array(
			'page_id' => 2,
			'set_title' => true,
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <p>
		<?php 	$pages = get_pages();	?>
		<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Display page', 'vkExUnit' ) ?></label>
		<select name="<?php echo $this->get_field_name( 'page_id' ); ?>" >
		<?php foreach ( $pages as $page ) {  ?>
		<option value="<?php echo $page->ID; ?>" <?php if ( $instance['page_id'] == $page->ID ) { echo 'selected="selected"'; } ?> ><?php echo $page->post_title; ?></option>
		<?php } ?>
        </select>
        <br/>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'set_title' ); ?>" value="true" <?php echo ($instance['set_title'])? 'checked': '' ; ?> >
		<label for="<?php echo $this->get_field_id( 'set_title' ); ?>"> <?php _e( 'display title', 'vkExUnit' ); ?></label>
        </p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['page_id'] = $new_instance['page_id'];
		$instance['set_title'] = ($new_instance['set_title'] == 'true')? true : false;
		return $instance;
	}

	function display_page( $pageid, $titleflag = false, $args ) {
		echo $args['before_widget'];
		$page = get_page( $pageid );
		echo PHP_EOL.'<div id="widget-page-'.$pageid.'" class="widget_pageContent">' . PHP_EOL;
		if ( $titleflag ) {  echo $args['before_title'] . $page->post_title . $args['after_title'] . PHP_EOL; }
		echo apply_filters( 'the_content', $page->post_content );

		if ( is_user_logged_in() == true ) {
			if (  current_user_can( 'edit_pages' ) ) { ?>
    <div class="veu_adminEdit">
		<a href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo $pageid ;?>&action=edit" class="btn btn-default btn-sm"><?php _e( 'Edit', 'vkExUnit' );?></a>
    </div>
<?php }
		}
		echo '</div>'.PHP_EOL;
		echo $args['after_widget'];
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_vkExUnit_widget_page");' ) );
