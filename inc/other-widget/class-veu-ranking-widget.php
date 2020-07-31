<?php
/**
 * Ranking Widget
 *
 * @package VK All in One Expansion Unit
 */

/**
 * Ranking Widget
 */
class VEU_Ranking_Widget extends WP_Widget {

	/**
	 * Constractor
	 */
	public function __construct() {
		parent::__construct(
			'VEU_Ranking_Widget',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
		require_once dirname( __FILE__ ) . '/../vk-components/vk-components-config.php';
	}

	/**
	 * Widget Name
	 */
	public static function veu_widget_name() {
		return veu_get_prefix() . __( 'Ranking', 'vk-all-in-one-expansion-unit' );
	}

	/**
	 * Widget Description
	 */
	public static function veu_widget_description() {
		return __( 'Display ranking of articles that you wan to see', 'vk-all-in-one-expansion-unit' );
	}

	/**
	 * Widget Form
	 *
	 * @param array $instance Widget Instance.
	 */
	public function form( $instance ) {

		echo '<div class="admin-custom-section">';
		echo '<h3 class="admin-custom-h2">' . esc_html__( 'Ranking Setting', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		// Title.
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		echo '<label>';
		echo '<p>' . esc_html__( 'Title:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( $title ) . '" />';
		echo '</label>';

		// Display Value of Ranking.
		$ranking_max = ! empty( $instance['ranking_max'] ) ? $instance['ranking_max'] : 10;
		echo '<label>';
		echo '<p>' . esc_html__( 'Display Ranking Value:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="number" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'ranking_max' ) ) . '" value="' . esc_attr( $ranking_max ) . '" />';
		echo '</label>';
		echo '</div>';

		// 表示形式とカラム.
		echo '<div class="admin-custom-section">';
		echo '<h2 class="admin-custom-h2">' . esc_html__( 'Display type and columns', 'vk-all-in-one-expansion-unit' ) . '</h2>';

		// 表示タイプ.
		echo '<h3 class="admin-custom-h3">' . esc_html__( 'Display type', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		$patterns = VK_Component_Posts::get_patterns();

		echo '<select id="' . esc_attr( $this->get_field_name( 'layout' ) ) . '" name="' . esc_attr( $this->get_field_name( 'layout' ) ) . '" class="admin-custom-input">';

		foreach ( $patterns as $key => $value ) {
			if ( $instance['layout'] === $key ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $selected ) . '>' . esc_html( $value['label'] ) . '</option>';
		}
		echo '</select>';

		// カラム.
		echo '<h3 class="admin-custom-h3">' . esc_html__( 'Columns', 'vk-all-in-one-expansion-unit' ) . '</h3>';
		echo '<p>' . esc_html__( 'Please input column count to under the range of 1 to 4.', 'vk-all-in-one-expansion-unit' ) . '</p>';

		$sizes = array(
			'xs' => array( 'label' => __( 'Extra small', 'vk-all-in-one-expansion-unit' ) ),
			'sm' => array( 'label' => __( 'Small', 'vk-all-in-one-expansion-unit' ) ),
			'md' => array( 'label' => __( 'Medium', 'vk-all-in-one-expansion-unit' ) ),
			'lg' => array( 'label' => __( 'Large', 'vk-all-in-one-expansion-unit' ) ),
			'xl' => array( 'label' => __( 'Extra large', 'vk-all-in-one-expansion-unit' ) ),
		);

		foreach ( $sizes as $key => $value ) {
			$field          = 'col_' . $key;
			$instance_field = isset( $instance[ $field ] ) ? $instance[ $field ] : 1;
			echo '<label>';
			// translators: label of $size.
			echo '<p>' . sprintf( esc_html__( 'Column ( Screen size : %s )', 'vk-all-in-one-expansion-unit' ), esc_html( $value['label'] ) ) . '</p>';
			echo '<input type="number" max="4" min="1" name="' . esc_attr( $this->get_field_name( $field ) ) . '" value="' . esc_attr( $instance_field ) . '" class="admin-custom-input" />';
			echo '</label>';
		}

		echo '</div>';

		// 表示アイテム.
		echo '<div class="admin-custom-section">';
		echo '<h2 class="admin-custom-h2">' . esc_html__( 'Display item', 'vk-all-in-one-expansion-unit' ) . '</h2>';

		$items = array(
			'display_image'              => __( 'Image', 'vk-all-in-one-expansion-unit' ),
			'display_image_overlay_term' => __( 'Term name', 'vk-all-in-one-expansion-unit' ),
			'display_excerpt'            => __( 'Excerpt', 'vk-all-in-one-expansion-unit' ),
			'display_date'               => __( 'Date', 'vk-all-in-one-expansion-unit' ),
			'display_new'                => __( 'New mark', 'vk-all-in-one-expansion-unit' ),
			'display_btn'                => __( 'Button', 'vk-all-in-one-expansion-unit' ),
		);

		foreach ( $items as $key => $value ) {
			$checked = ( isset( $instance[ $key ] ) && $instance[ $key ] ) ? ' checked' : '';
			echo '<label><p>';
			echo '<input type="checkbox" value="true" name="' . esc_attr( $this->get_field_name( '' . $key ) ) . '"' . esc_attr( $checked ) . ' />';
			echo '<span>' . esc_html( $value ) . '</span>';
			echo '</label></p>';
		}

		echo '<h3 class="admin-custom-h3">' . esc_html__( 'New mark option', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		// NEWアイコン表示期間.
		$new_date = ( isset( $instance['new_date'] ) ) ? $instance['new_date'] : 7;

		echo '<label>';
		echo '<p>' . esc_html__( 'Number of days to display the new post mark', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="number" name="' . esc_attr( $this->get_field_name( 'new_date' ) ) . '" value="' . esc_attr( $new_date ) . '" class="admin-custom-input" />';
		echo '</label>';

		// New text.
		$new_text = ( isset( $instance['new_text'] ) ) ? $instance['new_text'] : '';
		echo '<label>';
		echo '<p>' . esc_html__( 'New mark text', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 'new_text' ) ) . '" value="' . esc_attr( $new_text ) . '" class="admin-custom-input" />';
		echo '</label>';

		// Button Option.
		echo '<h3 class="admin-custom-h3">' . esc_html__( 'Button option', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		// Button Text.
		$btn_text = ( isset( $instance['btn_text'] ) ) ? $instance['btn_text'] : '';
		echo '<label>';
		echo '<p>' . esc_html__( 'Button text', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 'btn_text' ) ) . '" value="' . esc_attr( $btn_text ) . '" class="admin-custom-input" />';
		echo '</label>';

		// Button Align.
		echo '<label>';
		echo '<p>' . esc_html__( 'Button align', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<select name="' . esc_attr( $this->get_field_name( 'btn_align' ) ) . '" class="admin-custom-input">';
		$btn_aligns = array(
			'text-left'   => array(
				'label' => __( 'Left', 'vk-all-in-one-expansion-unit' ),
			),
			'text-center' => array(
				'label' => __( 'Center', 'vk-all-in-one-expansion-unit' ),
			),
			'text-right'  => array(
				'label' => __( 'Right', 'vk-all-in-one-expansion-unit' ),
			),
		);
		foreach ( $btn_aligns as $key => $value ) {
			if ( $instance['btn_align'] === $key ) {
				$selected = ' selected="selected"';
			} else {
				$selected;
			}

			echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $selected ) . '>' . esc_html( $value['label'] ) . '</option>';
		}
		echo '</select>';
		echo '</label>';
		echo '</div>';

		echo '<h2 class="admin-custom-h2">' . esc_html__( 'Ranking URLs', 'vk-all-in-one-expansion-unit' ) . '</h2>';
		for ( $i = 1; $i <= $ranking_max; $i++ ) {
			$url[ $i ] = isset( $instance[ 'url' . $i ] ) ? $instance[ 'url' . $i ] : '';
			echo '<label>';
			echo '<p>' . esc_html__( 'Page URL', 'vk-all-in-one-expansion-unit' ) . '[' . esc_html( $i ) . ']:</p>';
			echo '<input type="text" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'url' . $i ) ) . '" value="' . esc_url( $url[ $i ] ) . '" />';
			echo '</label>';
		}
	}

	/**
	 * Widget Update
	 *
	 * @param array $new_instance New Instance.
	 * @param array $old_instance Old Instance.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title']                      = $new_instance['title'];
		$instance['ranking_max']                = $new_instance['ranking_max'];
		$instance['layout']                     = $new_instance['layout'];
		$instance['col_xs']                     = $new_instance['col_xs'];
		$instance['col_sm']                     = $new_instance['col_sm'];
		$instance['col_md']                     = $new_instance['col_md'];
		$instance['col_lg']                     = $new_instance['col_lg'];
		$instance['col_xl']                     = $new_instance['col_xl'];
		$instance['new_date']                   = $new_instance['new_date'];
		$instance['display_image']              = $new_instance['display_image'];
		$instance['display_image_overlay_term'] = $new_instance['display_image_overlay_term'];
		$instance['display_excerpt']            = $new_instance['display_excerpt'];
		$instance['display_date']               = $new_instance['display_date'];
		$instance['display_new']                = $new_instance['display_new'];
		$instance['display_btn']                = $new_instance['display_btn'];
		$instance['btn_text']                   = $new_instance['btn_text'];
		$instance['btn_align']                  = $new_instance['btn_align'];
		$instance['new_text']                   = $new_instance['new_text'];

		for ( $i = 1; $i <= $instance['ranking_max']; $i++ ) {
			$instance[ 'url' . $i ] = $new_instance[ 'url' . $i ];
		}

		return $instance;
	}

	/**
	 * Default Option.
	 */
	public function options_default() {
		$options_default = array(
			'title'                      => __( 'Rnaking article list', 'vk-all-in-one-expansion-unit' ),
			'ranking_max'                => 10,
			'layout'                     => 'media',
			'col_xs'                     => 1,
			'col_sm'                     => 1,
			'col_md'                     => 1,
			'col_lg'                     => 1,
			'col_xl'                     => 1,
			'display_image'              => true,
			'display_image_overlay_term' => true,
			'display_excerpt'            => false,
			'display_date'               => true,
			'display_new'                => true,
			'new_date'                   => 7,
			'new_text'                   => 'New!!',
			'btn_text'                   => __( 'Read more', 'lightning-pro' ),
			'btn_align'                  => 'text-right',
		);
		return $options_default;
	}

	/**
	 * Widget Display
	 *
	 * @param array $args Widget Args.
	 * @param array $instance Widget Instance.
	 */
	public function widget( $args, $instance ) {

		$defaults = self::options_default();
		$instance = wp_parse_args( $instance, $defaults );

		echo wp_kses_post( $args['before_widget'] );
		echo '<div class="veu_ranking_widget">';

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo wp_kses_post( $instance['title'] );
			echo wp_kses_post( $args['after_title'] );
		}
		echo '<div class="veu_ranking_widget_body">';

		$post__in = array();
		for ( $i = 1; $i <= $instance['ranking_max']; $i++ ) {
			if ( ! empty( $instance[ 'url' . $i ] ) ) {
				$post_url   = esc_url( $instance[ 'url' . $i ] );
				$post_id    = url_to_postid( $post_url );
				$post__in[] = $post_id;
			}
		}

		$query_args = array(
			'post_type'           => 'any',
			'post__in'            => $post__in,
			'ignore_sticky_posts' => true,
		);

		$ranking_query = new WP_Query( $query_args );

		$options = $instance;
		VK_Component_Posts::the_loop( $ranking_query, $options );

		wp_reset_postdata();

		echo wp_kses_post( $args['after_widget'] );

		echo '</div>'; // .veu_ranking_widget_body.
		echo '</div>'; // .veu_ranking_widget.
	}
}
