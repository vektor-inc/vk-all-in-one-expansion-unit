<?php

class WP_Widget_vkExUnit_ChildPageList extends WP_Widget {
	function __construct() {
		parent::__construct(
			'vkExUnit_childPageList',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		return veu_get_prefix() . __( 'child pages list', 'vk-all-in-one-expansion-unit' );
	}

	public static function veu_widget_description() {
		return __( 'Displays list of child page for the current page on such as sidebar of page.', 'vk-all-in-one-expansion-unit' );
	}

	function widget( $args, $instance ) {

		global $post;
		if ( is_page() ) {
			if ( $post->ancestors ) {
				foreach ( $post->ancestors as $post_anc_id ) {
					$post_id = $post_anc_id;
				}
			} else {
				$post_id = $post->ID;
			}
			if ( $post_id ) {
				$children = wp_list_pages( 'title_li=&child_of=' . $post_id . '&echo=0' );
				if ( $children ) {
					echo $args['before_widget'];
					echo '<div class="veu_childPages widget_link_list">';
					echo $args['before_title'];
					echo '<a href="' . get_the_permalink( $post_id ) . '">';
					echo get_the_title( $post_id );
					echo '</a>';
					echo $args['after_title'];
					?>
					<ul class="localNavi">
					<?php echo $children; ?>
					</ul>
					</div>
					<?php echo $args['after_widget']; ?>
					<?php
				}
			}
		} // is_page

	}

	function form( $instance ) {
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

}
