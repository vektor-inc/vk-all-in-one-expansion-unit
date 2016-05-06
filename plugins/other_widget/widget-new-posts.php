<?php

/*-------------------------------------------*/
/*  Side Post list widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_post_list extends WP_Widget {

	public $taxonomies = array( 'category' );

	function __construct() {
		$widget_name = vkExUnit_get_short_name(). '_' . __( 'Recent Posts', 'vkExUnit' );

		parent::__construct(
			'vkExUnit_post_list',
			$widget_name,
			array( 'description' => __( 'Displays a list of your most recent posts', 'vkExUnit' ) )
		);
	}

	function widget( $args, $instance ) {
		if ( ! isset( $instance['format'] ) ) { $instance['format'] = 0; }

		echo $args['before_widget'];
		echo '<div class="veu_newPosts pt_'.$instance['format'].'">';
		echo $args['before_title'];
		if ( isset( $instance['label'] ) && $instance['label'] ) {
			echo $instance['label'];
		} else {
			_e( 'Recent Posts', 'vkExUnit' );
		}
		echo $args['after_title'];

		$count      = ( isset( $instance['count'] ) && $instance['count'] ) ? $instance['count'] : 10;
		$post_type  = ( isset( $instance['post_type'] ) && $instance['post_type'] ) ? $instance['post_type'] : 'post';

		if ( $instance['format'] ) { $this->_taxonomy_init( $post_type ); }

		$p_args = array(
			'post_type' => $post_type,
			'posts_per_page' => $count,
			'paged' => 1,
		);

		if ( isset( $instance['terms'] ) && $instance['terms'] ) {
			$taxonomies = get_taxonomies( array() );
			$p_args['tax_query'] = array(
				'relation' => 'OR',
			);
			foreach ( $taxonomies as $taxonomy ) {
				$p_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $instance['terms'],
				);
			}
		}
		$post_loop = new WP_Query( $p_args );

		if ( $post_loop->have_posts() ) :
			if ( ! $instance['format'] ) {
				while ( $post_loop->have_posts() ) : $post_loop->the_post();
					$this->display_pattern_0();
				endwhile;
			} else {
				echo '<ul class="postList">';
				while ( $post_loop->have_posts() ) : $post_loop->the_post();
					$this->display_pattern_1();
				endwhile;
				echo '</ul>';
			}

		endif;
		echo '</div>';
		echo $args['after_widget'];

		wp_reset_postdata();
		wp_reset_query();

	} // widget($args, $instance)


	function display_pattern_0() {
	?>
<div class="media" id="post-<?php the_ID(); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
        <div class="media-left postList_thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php 
				$thumbnail_size = 'thumbnail';
				the_post_thumbnail( apply_filters( 'vk_post_list_widget_thumbnail', esc_attr( $thumbnail_size ) ) );
			?>
		</a>
        </div>
	<?php endif; ?>
    <div class="media-body">
		<?php
			do_action( 'vk_post_list_widget_media_body_prepend' );
			$media_body_output  = '<h4 class="media-heading"><a href="'.esc_url( get_the_permalink() ).'">'.esc_html( get_the_title() ).'</a></h4>';
			$media_body_output .= '<div class="published entry-meta_items">'.esc_html( get_the_date() ).'</div>';
			echo apply_filters( 'vk_post_list_widget_media_body', $media_body_output );
			do_action( 'vk_post_list_widget_media_body_append' );
		?>
    </div>
</div><?php
	}

	function display_pattern_1() {
	?>
<li id="post-<?php the_ID(); ?>">
	
	<?php
		do_action( 'vk_post_list_widget_li_prepend' );
		$li_items_output  = '<span class="published entry-meta_items">'.esc_html( get_the_date() ).'</span>';
		$li_items_output .= '<span class="taxonomies">'.$this->taxonomy_list( get_the_id(), ' ', '', '' ).'</span>';
		$li_items_output .=	'<span class="entry-title"><a href="'.esc_url( get_the_permalink() ).'">'.esc_html( get_the_title() ).'</a></span>';
		echo apply_filters( 'vk_post_list_widget_li_items', $li_items_output );
		do_action( 'vk_post_list_widget_li_append' );
	?>
</li><?php
	}

	function _taxonomy_init( $post_type ) {
		if ( $post_type == 'post' ) { return; }
		$this->taxonomies = get_object_taxonomies( $post_type );
	}

	function taxonomy_list( $post_id = 0, $before = ' ', $sep = ',', $after = '' ) {
		if ( ! $post_id ) { $post_id = get_the_ID(); }

		$taxo_catelist = array();

		foreach ( $this->taxonomies as $taxonomy ) {
			$terms = get_the_term_list( $post_id, $taxonomy, $before, $sep , $after );
			if ( $terms ) { $taxo_catelist[] = $terms; }
		}

		if ( count( $taxo_catelist ) ) { return join( $taxo_catelist, $sep ); }
		return '';
	}



	function form( $instance ) {
		$defaults = array(
			'count'     => 10,
			'label'     => __( 'Recent Posts', 'vkExUnit' ),
			'post_type' => 'post',
			'terms'     => '',
			'format'    => '0',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		//タイトル ?>
        <br/>
		<?php echo _e( 'Display Format', 'vkExUnit' ); ?>:<br/>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'format' );  ?>" value="0" <?php if ( ! $instance['format'] ) { echo 'checked'; } ?> /><?php echo __( 'Thumbnail', 'vkExUnit' ) .'/'. __( 'Title', 'vkExUnit' ) .'/'. __( 'Date', 'vkExUnit' ); ?></label><br/>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'format' );  ?>" value="1" <?php if ( $instance['format'] == 1 ) { echo 'checked'; } ?>/><?php echo __( 'Date', 'vkExUnit' ) .'/'. __( 'Category', 'vkExUnit' ) .'/'. __( 'Title', 'vkExUnit' ); ?></label>
        <br/><br/>
		<label for="<?php echo $this->get_field_id( 'label' );  ?>"><?php _e( 'Title:' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
        <br/><br />

		<?php //表示件数 ?>
		<label for="<?php echo $this->get_field_id( 'count' );  ?>"><?php _e( 'Display count','vkExUnit' ); ?>:</label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" />
        <br /><br />

		<?php //投稿タイプ ?>
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Slug for the custom type you want to display', 'vkExUnit' ) ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" value="<?php echo esc_attr( $instance['post_type'] ) ?>" />
        <br/><br/>

		<?php // Terms ?>
		<label for="<?php echo $this->get_field_id( 'terms' ); ?>"><?php _e( 'taxonomy ID', 'vkExUnit' ) ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'terms' ); ?>" name="<?php echo $this->get_field_name( 'terms' ); ?>" value="<?php echo esc_attr( $instance['terms'] ) ?>" /><br />
		<?php _e( 'if you need filtering by term, add the term ID separate by ",".', 'vkExUnit' );
		echo '<br/>';
		_e( 'if empty this area, I will do not filtering.', 'vkExUnit' );
		echo '<br/><br/>';
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['format']     = $new_instance['format'];
		$instance['count']      = $new_instance['count'];
		$instance['label']      = $new_instance['label'];
		$instance['post_type']  = ! empty( $new_instance['post_type'] ) ? strip_tags( $new_instance['post_type'] ) : 'post';
		$instance['terms']      = preg_replace( '/([^0-9,]+)/', '', $new_instance['terms'] );
		return $instance;
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_vkExUnit_post_list");' ) );
