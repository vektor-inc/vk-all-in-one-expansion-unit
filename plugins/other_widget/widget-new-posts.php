<?php

/*-------------------------------------------*/
/*	Side Post list widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_post_list extends WP_Widget {

	function WP_Widget_vkExUnit_post_list() {
		$widget_ops = array(
			'classname' => 'WP_Widget_vkExUnit_post_list',
			'description' => __( 'Displays a list of your most recent posts', 'vkExUnit' ),
		);
		$widget_name = vkExUnit_get_short_name(). '_' . __( 'Recent Posts', 'vkExUnit' );
		$this->WP_Widget('vkExUnit_post_list', $widget_name, $widget_ops);
	}

	function widget($args, $instance) {
		echo '<aside class="widget widget_newPosts">';
		echo '<h1 class="widget-title subSection-title">';
		if ( isset($instance['label']) && $instance['label'] ) {
			echo $instance['label'];
		} else {
			_e('Recent Posts', 'vkExUnit' );
		}
		echo '</h1>';

		$count 		= ( isset($instance['count']) && $instance['count'] ) ? $instance['count'] : 10;
		$post_type 	= ( isset($instance['post_type']) && $instance['post_type'] ) ? $instance['post_type'] : 'post';

		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => $count,
			'paged' => 1,
		);
		
		if(isset($instance['terms']) && $instance['terms']){
			$taxonomies = get_taxonomies(array());
	        $args['tax_query'] = array(
	        	'relation' => 'OR',
	        );
			foreach($taxonomies as $taxonomy){
	        $args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $instance['terms']
				);
			}
	    }
		$post_loop = new WP_Query( $args );


		if ($post_loop->have_posts()):
			while ( $post_loop->have_posts() ) : $post_loop->the_post(); ?>

			<div class="media" id="post-<?php the_ID(); ?>">
				
				<?php if ( has_post_thumbnail()) : ?>
					<div class="media-left postList_thumbnail">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
					</div>
				<?php endif; ?>
				
				<div class="media-body">
					<h4 class="media-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					<div><i class="fa fa-calendar"></i>&nbsp;<?php echo get_the_date(); ?></div>          
				</div>

			</div>

			<?php endwhile;
		endif;
		echo '</aside>';
		wp_reset_postdata();
		wp_reset_query();

	} // widget($args, $instance)

	function form ($instance) {
		
		$defaults = array(
			'count' 	=> 10,
			'label' 	=> __('Recent Posts', 'vkExUnit' ),
			'post_type' => 'post',
			'terms'     => ''
		);

		$instance = wp_parse_args((array) $instance, $defaults);
		
		?>
		
		<?php //タイトル ?>
		<label for="<?php echo $this->get_field_id('label');  ?>"><?php _e('Title:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('label'); ?>-title" name="<?php echo $this->get_field_name('label'); ?>" value="<?php echo $instance['label']; ?>" />
		<br/><br />

		<?php //表示件数 ?>
		<label for="<?php echo $this->get_field_id('count');  ?>"><?php _e('Display count','vkExUnit'); ?>:</label><br/>
		<input type="text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>" />
		<br /><br />

		<?php //投稿タイプ ?>
		<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Slug for the custom type you want to display', 'vkExUnit') ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" value="<?php echo esc_attr($instance['post_type']) ?>" />
		<br/><br/>

		<?php // Terms ?>
		<label for="<?php echo $this->get_field_id('terms'); ?>"><?php _e('taxonomy ID', 'vkExUnit') ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id('terms'); ?>" name="<?php echo $this->get_field_name('terms'); ?>" value="<?php echo esc_attr($instance['terms']) ?>" /><br />
		<?php _e('if you need filtering by term, add the term ID separate by ",".', 'vkExUnit'); 
		echo "<br/>";
		_e('if empty this area, I will do not filtering.', 'vkExUnit'); 
		echo "<br/><br/>";
	}

	function update ($new_instance, $old_instance) {
		
		$instance = $old_instance;
		
		$instance['count'] 		= $new_instance['count'];
		$instance['label'] 		= $new_instance['label'];
		$instance['post_type']	= !empty($new_instance['post_type']) ? strip_tags($new_instance['post_type']) : 'post';
		$instance['terms'] 		= preg_replace('/([^0-9,]+)/', '', $new_instance['terms']);

		return $instance;
	}

} // class WP_Widget_top_list_post
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_vkExUnit_post_list");'));