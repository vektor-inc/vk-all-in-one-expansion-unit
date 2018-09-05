<?php

/*-------------------------------------------*/
/*  Side Post list widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_post_list extends WP_Widget {

	public $taxonomies = array( 'category' );

	function __construct() {
		$widget_name = veu_get_short_name() . ' ' . __( 'Recent Posts', 'vkExUnit' );

		parent::__construct(
			'vkExUnit_post_list',
			$widget_name,
			array( 'description' => __( 'Displays a list of your most recent posts', 'vkExUnit' ) )
		);
	}

	/*-------------------------------------------*/
	/*  一覧へのリンクhtmlを出力する関数
	/*-------------------------------------------*/
	static public function more_link_html( $instance ) {
		if ( ! empty( $instance['more_text'] ) && ! empty( $instance['more_url'] ) ) {
			$more_link_html  = '<div class="postList_more">';
			$more_link_html .= '<a href="' . esc_url( $instance['more_url'] ) . '">' . wp_kses_post( $instance['more_text'] ) . '</a>';
			$more_link_html .= '</div>';
		} else {
			$more_link_html = '';
		}
		return $more_link_html;
	}


	function widget( $args, $instance ) {
		$instance = static::default_options( $instance );

		if ( ! isset( $instance['format'] ) ) {
			$instance['format'] = 0; }

		echo $args['before_widget'];
		echo '<div class="veu_postList pt_' . $instance['format'] . '">';
		if ( ! empty( $instance['label'] ) ) {
			echo $args['before_title'];
			echo $instance['label'];
			echo $args['after_title'];
		}

		$count       = ( isset( $instance['count'] ) && $instance['count'] ) ? $instance['count'] : 10;
		$post_type   = ( isset( $instance['post_type'] ) && $instance['post_type'] ) ? $instance['post_type'] : 'post';
		$is_modified = ( isset( $instance['orderby'] ) && $instance['orderby'] == 'modified' );
		$orderby     = ( isset( $instance['orderby'] ) ) ? $instance['orderby'] : 'date';

		if ( $instance['format'] ) {
			$this->_taxonomy_init( $post_type ); }

		$p_args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $count,
			'orderby'        => $orderby,
			'paged'          => 1,
		);

		if ( isset( $instance['terms'] ) && $instance['terms'] ) {
			$taxonomies          = get_taxonomies( array() );
			$p_args['tax_query'] = array(
				'relation' => 'OR',
			);
			$terms_array         = explode( ',', $instance['terms'] );
			foreach ( $taxonomies as $taxonomy ) {
				$p_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $terms_array,
				);
			}
		}

		$post_loop = new WP_Query( $p_args );

		if ( $post_loop->have_posts() ) :
			if ( ! $instance['format'] ) {
				echo '<div class="postList postList_miniThumb">';
				while ( $post_loop->have_posts() ) :
					$post_loop->the_post();
					$this->display_pattern_0( $is_modified );
				endwhile;
				echo '</div>';
			} else {
				echo '<ul class="postList">';
				while ( $post_loop->have_posts() ) :
					$post_loop->the_post();
					$this->display_pattern_1( $is_modified );
				endwhile;
				echo '</ul>';
			}

		endif;

		echo  $this->more_link_html( $instance );

		echo '</div>';

		echo $args['after_widget'];

		wp_reset_postdata();
		wp_reset_query();

	} // widget($args, $instance)


	function display_pattern_0( $is_modified = false ) {
	?>
<!-- [ .postList は近日削除されます ] -->
<div class="postList postList_item" id="post-<?php the_ID(); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="postList_thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php
				$thumbnail_size = 'thumbnail';
				the_post_thumbnail( apply_filters( 'vk_post_list_widget_thumbnail', esc_attr( $thumbnail_size ) ) );
			?>
		</a>
		</div><!-- [ /.postList_thumbnail ] -->
	<?php endif; ?>
	<div class="postList_body">
		<?php
			do_action( 'vk_post_list_widget_media_body_prepend' );
			$media_body_output = '<div class="postList_title entry-title"><a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></div>';
		if ( $is_modified ) {
			$media_body_output .= '<div class="modified postList_date postList_meta_items">' . esc_html( get_the_modified_date() ) . '</div>';
		} else {
			$media_body_output .= '<div class="published postList_date postList_meta_items">' . esc_html( get_the_date() ) . '</div>';
		}
			echo apply_filters( 'vk_post_list_widget_media_body', $media_body_output );
			do_action( 'vk_post_list_widget_media_body_append' );
		?>
	</div><!-- [ /.postList_body ] -->
</div>
<?php
	}

	function display_pattern_1( $is_modified = false ) {
	?>
<li id="post-<?php the_ID(); ?>">

	<?php
		do_action( 'vk_post_list_widget_li_prepend' );
		/*
		microformats なので削除してはいけないクラス名
		.entry-title
		.published
		.modified
		*/
		$li_items_output = '';
	if ( $is_modified ) {
		$li_items_output .= '<span class="modified postList_date postList_meta_items">' . esc_html( get_the_modified_date() ) . '</span>';
	} else {
		$li_items_output = '<span class="published postList_date postList_meta_items">' . esc_html( get_the_date() ) . '</span>';
	}
		$li_items_output .= '<span class="postList_terms postList_meta_items">' . $this->taxonomy_list( get_the_id(), '', '', '' ) . '</span>';
		$li_items_output .= '<span class="postList_title entry-title"><a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></span>';
		echo apply_filters( 'vk_post_list_widget_li_items', $li_items_output );
		do_action( 'vk_post_list_widget_li_append' );
	?>
</li>
<?php
	}

	function _taxonomy_init( $post_type ) {
		if ( $post_type == 'post' ) {
			return; }
		$this->taxonomies = get_object_taxonomies( $post_type );
	}

	function taxonomy_list( $post_id = 0, $before = ' ', $sep = ',', $after = '' ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID(); }

		$taxo_catelist = array();

		foreach ( $this->taxonomies as $taxonomy ) {
			$terms = get_the_term_list( $post_id, $taxonomy, $before, $sep, $after );
			if ( $terms ) {
				$taxo_catelist[] = $terms; }
		}

		if ( count( $taxo_catelist ) ) {
			return join( $taxo_catelist, $sep ); }
		return '';
	}

	static function default_options( $instance = array() ) {
		$defaults = array(
			'count'     => 10,
			'label'     => __( 'Recent Posts', 'vkExUnit' ),
			'post_type' => 'post',
			'orderby'   => 'date',
			'terms'     => '',
			'format'    => '0',
			'more_url'  => '',
			'more_text' => '',
		);

		return wp_parse_args( (array) $instance, $defaults );
	}


	function form( $instance ) {
		/*
		 下記 default_options($instance) が無いと Charm テスト環境ではエラーが発生する
		 但し、これがある事で過去にnotice が出た経緯があるようなので、要調査
		 ※20行目付近にも同様の記述あり
		*/
		$instance = static::default_options( $instance );
		?>
		<br />
		<?php //タイトル ?>
		<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title:' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>" />
		<br /><br />

		<?php echo _e( 'Display Format', 'vkExUnit' ); ?>:<br/>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'format' ); ?>" value="0"
													<?php
													if ( ! $instance['format'] ) {
														echo 'checked'; }
?>
 /><?php echo __( 'Thumbnail', 'vkExUnit' ) . '/' . __( 'Title', 'vkExUnit' ) . '/' . __( 'Date', 'vkExUnit' ); ?></label><br/>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'format' ); ?>" value="1"
													<?php
													if ( $instance['format'] == 1 ) {
														echo 'checked'; }
?>
/><?php echo __( 'Date', 'vkExUnit' ) . '/' . __( 'Category', 'vkExUnit' ) . '/' . __( 'Title', 'vkExUnit' ); ?></label>
		<br/><br/>

		<?php echo _e( 'Order by', 'vkExUnit' ); ?>:<br/>
		<label style="padding-bottom: 0.5em"><input type="radio" name="<?php echo $this->get_field_name( 'orderby' ); ?>" value="date"
																					<?php
																					if ( $instance['orderby'] != 'modified' ) {
																						echo 'checked'; }
?>
 /><?php _e( 'Publish date', 'vkExUnit' ); ?></label><br/>
		<label><input type="radio" name="<?php echo $this->get_field_name( 'orderby' ); ?>" value="modified"
													<?php
													if ( $instance['orderby'] == 'modified' ) {
														echo 'checked'; }
?>
/><?php _e( 'Modified date', 'vkExUnit' ); ?></label>
		<br/><br/>

		<?php //表示件数 ?>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Display count', 'vkExUnit' ); ?>:</label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>" />
		<br /><br />

		<?php //投稿タイプ ?>
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Slug for the custom type you want to display', 'vkExUnit' ); ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" value="<?php echo esc_attr( $instance['post_type'] ); ?>" />
		<br/><br/>

		<?php // Terms ?>
		<label for="<?php echo $this->get_field_id( 'terms' ); ?>"><?php _e( 'taxonomy ID', 'vkExUnit' ); ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'terms' ); ?>" name="<?php echo $this->get_field_name( 'terms' ); ?>" value="<?php echo esc_attr( $instance['terms'] ); ?>" /><br />
		<?php
		_e( 'if you need filtering by term, add the term ID separate by ",".', 'vkExUnit' );
		echo '<br/>';
		_e( 'if empty this area, I will do not filtering.', 'vkExUnit' );
		?>
		<br/><br/>

		<?php // Read more ?>
		<label for="<?php echo $this->get_field_id( 'more_url' ); ?>"><?php _e( 'Destination URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo esc_attr( $instance['more_url'] ); ?>" />
		<br /><br />
		<label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'Notation text:', 'vkExUnit' ); ?></label><br/>
		<input type="text" placeholder="最新記事一覧 ≫" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
				<br /><br />

	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['format']    = $new_instance['format'];
		$instance['count']     = $new_instance['count'];
		$instance['label']     = $new_instance['label'];
		$instance['orderby']   = in_array( $new_instance['orderby'], array( 'date', 'modified' ) ) ? $new_instance['orderby'] : 'date';
		$instance['post_type'] = ! empty( $new_instance['post_type'] ) ? strip_tags( $new_instance['post_type'] ) : 'post';
		$instance['terms']     = preg_replace( '/([^0-9,]+)/', '', $new_instance['terms'] );
		$instance['more_url']  = $new_instance['more_url'];
		$instance['more_text'] = $new_instance['more_text'];
		return $instance;
	}
}

add_action( 'widgets_init', 'vkExUnit_widget_register_post_list' );
function vkExUnit_widget_register_post_list() {
	return register_widget( 'WP_Widget_vkExUnit_post_list' );
}
