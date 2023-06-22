<?php
/*
  Side Post list widget
/*-------------------------------------------*/

// vendor/vektor-inc/vk-term-color/src
use VektorInc\VK_Term_Color\VkTermColor;

class WP_Widget_vkExUnit_post_list extends WP_Widget {
	function __construct() {
		parent::__construct(
			'vkExUnit_post_list',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		return veu_get_prefix() . __( 'Recent Posts', 'vk-all-in-one-expansion-unit' );
	}

	public static function veu_widget_description() {
		return __( 'Displays a list of your most recent posts', 'vk-all-in-one-expansion-unit' );
	}

	/*
	  一覧へのリンクhtmlを出力する関数
	/*-------------------------------------------*/
	public static function more_link_html( $instance ) {
		if ( ! empty( $instance['more_text'] ) && ! empty( $instance['more_url'] ) ) {
			$more_link_html  = '<div class="postList_more">';
			$more_link_html .= '<a href="' . esc_url( $instance['more_url'] ) . '">' . wp_kses_post( $instance['more_text'] ) . '</a>';
			$more_link_html .= '</div>';
		} else {
			$more_link_html = '';
		}
		return $more_link_html;
	}

	public static function get_widget_title( $instance ) {
		$title = '';
		if ( isset( $instance['title'] ) && $instance['title'] ) {
			$title = $instance['title'];
		} elseif ( isset( $instance['label'] ) && $instance['label'] ) {
			// title が未記入で label は入力されている場合
			$title = $instance['label'];
		}
		return $title;
	}

	/*
	  Widget
	/*-------------------------------------------*/
	function widget( $args, $instance ) {
		$instance = static::get_options( $instance );
		$title    = $this->get_widget_title( $instance );

		if ( ! isset( $instance['format'] ) ) {
			$instance['format'] = 0;
		}

		echo $args['before_widget'];
		echo '<div class="veu_postList pt_' . $instance['format'] . '">';

		if ( ! empty( $title ) ) {
			echo $args['before_title'];
			echo $title;
			echo $args['after_title'];
		}

		$count = ( isset( $instance['count'] ) && $instance['count'] ) ? $instance['count'] : 10;

		$post_type = ( isset( $instance['post_type'] ) && $instance['post_type'] ) ? $instance['post_type'] : 'post';
		if ( is_array( $post_type ) ) {
			$post_type = vk_the_post_type_check_list_saved_array_convert( $post_type );
		}
		$is_modified = ( isset( $instance['orderby'] ) && $instance['orderby'] == 'modified' );
		$orderby     = ( isset( $instance['orderby'] ) ) ? $instance['orderby'] : 'date';

		$query_args = array(
			'order'          => 'DESC',
			'post_type'      => $post_type,
			'posts_per_page' => $count,
			'orderby'        => $orderby,
			'paged'          => 1,
		);

		if ( isset( $instance['terms'] ) && $instance['terms'] ) {
			$taxonomies              = get_taxonomies( array() );
			$query_args['tax_query'] = array(
				'relation' => 'OR',
			);
			$terms_array             = explode( ',', $instance['terms'] );
			foreach ( $taxonomies as $taxonomy ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $terms_array,
				);
			}
		}

		if ( ! empty( $args['id'] ) ) {
			$widget_area_id = $args['id'];
		} else {
			$widget_area_id = '';
		}

		/**
		 * Add veu_widget_new_posts_query filter
		 *
		 * @since 9.90.0.0
		 * https://github.com/vektor-inc/vk-all-in-one-expansion-unit/pull/974
		 */
		$query_args = apply_filters( 'veu_widget_new_posts_query', $query_args, $widget_area_id );
		$post_loop  = new WP_Query( $query_args );

		if ( $post_loop->have_posts() ) :
			if ( ! $instance['format'] ) {
				echo '<div class="postList postList_miniThumb">';
				while ( $post_loop->have_posts() ) :
					$post_loop->the_post();
					$this->display_pattern_0( $is_modified, $instance );
				endwhile;
				echo '</div>';
			} else {
				echo '<ul class="postList">';
				while ( $post_loop->have_posts() ) :
					$post_loop->the_post();
					$this->display_pattern_1( $is_modified, $instance );
				endwhile;
				echo '</ul>';
			}

		endif;

		echo $this->more_link_html( $instance );

		echo '</div>';

		echo $args['after_widget'];

		wp_reset_postdata();
		wp_reset_query();

	} // widget($args, $instance)


	function display_pattern_0( $is_modified, $instance ) {
		?>

<div class="postList_item" id="post-<?php the_ID(); ?>">
		<?php if ( has_post_thumbnail() || $instance['media_id'] ) : ?>
		<div class="postList_thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php
			if ( has_post_thumbnail() ) {
				$thumbnail_size = 'thumbnail';
				the_post_thumbnail( apply_filters( 'vk_post_list_widget_thumbnail', esc_attr( $thumbnail_size ) ) );
			} else {
				$attr = array(
					'class' => 'attachment-thumbnail size-thumbnail wp-post-image',
					'alt'   => trim( strip_tags( get_post_meta( $instance['media_id'], '_wp_attachment_image_alt', true ) ) ),
				);
				echo wp_get_attachment_image( $instance['media_id'], 'thumbnail', '', $attr );
			}
			?>
		</a>
		</div><!-- [ /.postList_thumbnail ] -->
	<?php endif; ?>
	<div class="postList_body">
		<?php
			do_action( 'vk_post_list_widget_media_body_prepend' );

			$allowed_html = array(
				'span'   => array( 'class' => array() ),
				'b'      => array(),
				'strong' => array(),
			);

			$media_body_output = '<div class="postList_title entry-title"><a href="' . esc_url( get_the_permalink() ) . '">' . wp_kses( get_the_title(), $allowed_html ) . '</a></div>';
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

	/**
	 * [display_pattern_1 description]
	 *
	 * @param  boolean $is_modified [description]
	 * @param  [type]  $instance    [description]
	 * @param  [type]  $taxonomies  [description]
	 * @return [type]               [description]
	 */
	public static function display_pattern_1( $is_modified, $instance ) {
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

		// クエリでターム指定がない場合に存在しているカスタム分類を取得する
		// ※ 各記事で get_the_terms() する時に $taxonomy が必要なため
		// ※ get_the_taxonomies() は 該当 term が ２つの場合「〇〇と〇〇」というように『と』が入ってしまう

		// まずはカスタム分類を取得
		$taxonomies_object = get_taxonomies(
			array(
				'public'  => true,
				'show_ui' => true,
			),
			'objects'
		);
		// 階層のあるものだけ $taxonomies に格納
		foreach ( $taxonomies_object as $key => $value ) {
			if ( $value->hierarchical ) {
				$taxonomies[] = $key;
			}
		}

		// taxonomy

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( get_the_ID(), $taxonomy );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_color = '';
					$link       = get_term_link( $term->term_id );
					if ( class_exists( 'VektorInc\VK_Term_Color\VKTermColor' ) ) {
						$term_color = VkTermColor::get_term_color( $term->term_id );
						$term_color = ( $term_color ) ? ' style="background-color:' . $term_color . ';border:none;color:white;"' : '';
					}
					$li_items_output .= '<span class="postList_terms postList_meta_items">';
					$li_items_output .= '<a href="' . $link . '"' . $term_color . '>' . $term->name . '</a>';
					$li_items_output .= '</span>';
				}
			}
		}

		$allowed_html = array(
			'span'   => array( 'class' => array() ),
			'b'      => array(),
			'strong' => array(),
		);

		$li_items_output .= '<span class="postList_title entry-title"><a href="' . esc_url( get_the_permalink() ) . '">' . wp_kses( get_the_title(), $allowed_html ) . '</a></span>';
		echo apply_filters( 'vk_post_list_widget_li_items', $li_items_output );
		do_action( 'vk_post_list_widget_li_append' );
		?>
</li>
		<?php
	}

	// function _taxonomy_init( $post_type ) {
	// if ( $post_type == 'post' ) {
	// return;
	// }
	// $this->taxonomies = get_object_taxonomies( $post_type );
	// }



	static function get_options( $instance = array() ) {
		$defaults = array(
			'count'     => 10,
			// 'label'     => __( 'Recent Posts', 'vk-all-in-one-expansion-unit' ),
			'title'     => __( 'Recent Posts', 'vk-all-in-one-expansion-unit' ),
			'media_url' => '',
			'media_id'  => '',
			'media_alt' => '',
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
		 下記 get_options($instance) が無いと Charm テスト環境ではエラーが発生する
		 但し、これがある事で過去にnotice が出た経緯があるようなので、要調査
		 ※20行目付近にも同様の記述あり
		*/
		$instance = static::get_options( $instance );
		?>

		<?php // タイトル ?>
		<h3 class="admin-custom-h3">
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		</h3>
		<?php
		if ( isset( $instance['title'] ) && $instance['title'] ) {
			$title = $instance['title'];
		} else {
			$title = $instance['label'];
		}
		?>
		<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>-title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" class="admin_widget_input" />

		<?php // Display Format ?>
		<h3 class="admin-custom-h3">
		<?php echo _e( 'Display Format', 'vk-all-in-one-expansion-unit' ); ?>
		</h3>
		<ul>
		<?php
		$checked = '';
		if ( ! $instance['format'] ) {
			$checked = ' checked';
		}
		?>
		<li><label><input type="radio" name="<?php echo $this->get_field_name( 'format' ); ?>" value="0"<?php echo $checked; ?>/><?php echo __( 'Thumbnail', 'vk-all-in-one-expansion-unit' ) . '/' . __( 'Title', 'vk-all-in-one-expansion-unit' ) . '/' . __( 'Date', 'vk-all-in-one-expansion-unit' ); ?></label></li>
		<?php
		$checked = '';
		if ( $instance['format'] == 1 ) {
			$checked = ' checked';
		}
		?>
		<li><label><input type="radio" name="<?php echo $this->get_field_name( 'format' ); ?>" value="1"<?php echo $checked; ?>/><?php echo __( 'Date', 'vk-all-in-one-expansion-unit' ) . '/' . __( 'Category', 'vk-all-in-one-expansion-unit' ) . '/' . __( 'Title', 'vk-all-in-one-expansion-unit' ); ?></label>
		</li>
		</ul>

		<?php
		/*
		media uploader
		/*-------------------------------------------*/
		$args = array(
			'media_url' => 'media_url',
			'media_id'  => 'media_id',
			'media_alt' => 'media_alt',
		);
		?>
		<h3 class="admin-custom-h3">
		<label for="<?php echo $this->get_field_id( $args['media_url'] ); ?>"><?php _e( 'Default thumbnail image:', 'vk-all-in-one-expansion-unit' ); ?></label>
		</h3>
<div class="media_image_section">
<div class="_display admin-custom-thumb-outer" style="height:auto">
		<?php
		if ( ! empty( $instance[ $args['media_url'] ] ) ) :
			?>
	<img src="<?php echo esc_url( $instance[ $args['media_url'] ] ); ?>" class="admin-custom-thumb" />
		<?php endif; ?>
</div>
<button class="button button-default widget_media_btn_select" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_add(this);return false;"><?php _e( 'Select image', 'vk-all-in-one-expansion-unit' ); ?></button>
<button class="button button-default widget_media_btn_reset" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_del(this);return false;"><?php _e( 'Clear image', 'vk-all-in-one-expansion-unit' ); ?></button>
<div class="_form" style="line-height: 2em">
<input type="hidden" class="_id" name="<?php echo $this->get_field_name( $args['media_id'] ); ?>" value="<?php echo esc_attr( $instance[ $args['media_id'] ] ); ?>" />
<input type="hidden" class="_url" name="<?php echo $this->get_field_name( $args['media_url'] ); ?>" value="<?php echo esc_attr( $instance[ $args['media_url'] ] ); ?>" />
<input type="hidden" class="_alt" name="<?php echo $this->get_field_name( $args['media_alt'] ); ?>" value="<?php echo esc_attr( $instance[ $args['media_alt'] ] ); ?>" />
</div>
</div><!-- [ /.media_image_section ] -->



		<h3 class="admin-custom-h3">
		<?php echo _e( 'Order by', 'vk-all-in-one-expansion-unit' ); ?>
		</h3>
		<ul>
		<?php
		$checked = '';
		if ( $instance['orderby'] != 'modified' ) {
			$checked = ' checked';
		}
		?>
		<li><label style="padding-bottom: 0.5em"><input type="radio" name="<?php echo $this->get_field_name( 'orderby' ); ?>" value="date"<?php echo $checked; ?> /><?php _e( 'Publish date', 'vk-all-in-one-expansion-unit' ); ?></label></li>
		<?php
		$checked = '';
		if ( $instance['orderby'] == 'modified' ) {
			$checked = ' checked';
		}
		?>
		<li><label><input type="radio" name="<?php echo $this->get_field_name( 'orderby' ); ?>" value="modified"<?php echo $checked; ?> /><?php _e( 'Modified date', 'vk-all-in-one-expansion-unit' ); ?></label></li>
</ul>

		<?php // 表示件数 ?>
		<h3 class="admin-custom-h3">
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Display count', 'vk-all-in-one-expansion-unit' ); ?>:</label>
		</h3>
		<input type="text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>" />

		<?php // 投稿タイプ ?>
		<h3 class="admin-custom-h3">
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Slug for the custom type you want to display', 'vk-all-in-one-expansion-unit' ); ?></label>
		</h3>

		<?php
		$args = array(
			'name'    => $this->get_field_name( 'post_type' ),
			'checked' => $instance['post_type'],
		);
		vk_the_post_type_check_list( $args );
		?>

		<?php // Terms ?>
		<h3 class="admin-custom-h3">
		<label for="<?php echo $this->get_field_id( 'terms' ); ?>"><?php _e( 'taxonomy ID', 'vk-all-in-one-expansion-unit' ); ?>:</label>
		</h3>
		<input type="text" id="<?php echo $this->get_field_id( 'terms' ); ?>" name="<?php echo $this->get_field_name( 'terms' ); ?>" value="<?php echo esc_attr( $instance['terms'] ); ?>" class="admin-custom-input" />
		
		<p>
		<?php
		_e( 'if you need filtering by term, add the term ID separate by ",".', 'vk-all-in-one-expansion-unit' );
		echo '<br/>';
		_e( 'if empty this area, I will do not filtering.', 'vk-all-in-one-expansion-unit' );
		?>
		</p>


		<?php // Read more ?>
		<h3 class="admin-custom-h3">
				<?php _e( 'Button option', 'vk-all-in-one-expansion-unit' ); ?>
		</h3>
		<label for="<?php echo $this->get_field_id( 'more_url' ); ?>"><?php _e( 'Destination URL:', 'vk-all-in-one-expansion-unit' ); ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo esc_attr( $instance['more_url'] ); ?>" class="admin-custom-input" />
		<br />
		<label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'Notation text:', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<input type="text" placeholder="最新記事一覧 ≫" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" class="admin-custom-input" />
		<br /><br />

		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['format']    = $new_instance['format'];
		$instance['count']     = $new_instance['count'];
		$instance['title']     = wp_kses_post( $new_instance['title'] );
		$instance['media_url'] = esc_url( $new_instance['media_url'] );
		$instance['media_id']  = esc_attr( $new_instance['media_id'] );
		$instance['media_alt'] = esc_attr( $new_instance['media_alt'] );
		$instance['orderby']   = in_array( $new_instance['orderby'], array( 'date', 'modified' ) ) ? $new_instance['orderby'] : 'date';
		$instance['post_type'] = $new_instance['post_type'];
		$instance['terms']     = preg_replace( '/([^0-9,]+)/', '', $new_instance['terms'] );
		$instance['more_url']  = $new_instance['more_url'];
		$instance['more_text'] = $new_instance['more_text'];
		return $instance;
	}
}
