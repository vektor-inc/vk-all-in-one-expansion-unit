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
		global $ranking_max;

		$ranking_max = ! empty( $instance['ranking_max'] ) ? $instance['ranking_max'] : 10;

		echo '<div class="admin-custom-section">';
		echo '<h3 class="admin-custom-h2">' . esc_html__( 'Ranking Setting', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		// Title.
		$title = isset($instance['title'] ) ? $instance['title'] : '';
		echo '<label>';
		echo '<p>' . esc_html__( 'Title:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( $title ) . '" />';
		echo '</label>';

		// Display Value of Ranking.
		echo '<label>';
		echo '<p>' . esc_html__( 'Display Ranking Value:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="number" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'ranking_max' ) ) . '" value="' . esc_attr( $ranking_max ) . '" />';
		echo '</label>';

		// Display Value of Ranking.
		$layout = isset($instance['layout'] ) ? $instance['layout'] : '';
		echo '<label>';
		echo '<p>' . esc_html__( 'Display Layout:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="number" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'layout' ) ) . '" value="' . esc_attr( $instance['layout'] ) . '" />';
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
			$field = 'col_' . $key;
			echo '<label>';
			// translators: label of $size.
			echo '<p>' . sprintf( esc_html__( 'Column ( Screen size : %s )', 'vk-all-in-one-expansion-unit' ), esc_html( $value['label'] ) ) . '</p>';
			echo '<input type="number" max="4" min="1" id="' . esc_attr( $this->get_field_id( $field ) ) . '" name="' . esc_attr( $this->get_field_name( $field ) ) . '" value="' . esc_attr( $instance[ $field ] ) . '" class="admin-custom-input" />';
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
			echo '<label>';
			echo '<input type="checkbox" value="true" name="' . esc_attr( $this->get_field_name( '' . $key ) ) . '"' . esc_attr( $checked ) . ' />';
			echo '<p>' . esc_html( $value ) . '</p>';
			echo '</label>';
		}

		echo '<h3 class="admin-custom-h3">' . esc_html__( 'New mark option', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		// NEWアイコン表示期間.
		$new_date = ( isset( $instance['new_date'] ) ) ? $instance['new_date'] : 7;

		echo '<label>';
		echo '<p>' . esc_html__( 'Number of days to display the new post mark', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="number" name="' . esc_attr( $this->get_field_name( 'new_date' ) ) . '" value="' . esc_attr( $instance['new_date'] ) . '" class="admin-custom-input" />';
		echo '</label>';

		// New text.
		echo '<label>';
		echo '<p>' . esc_html__( 'New mark text', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 'new_text' ) ) . '" value="' . esc_attr( instance['new_text'] ) . '" class="admin-custom-input" />';
		echo '</label>';

		// Button Option.
		echo '<h3 class="admin-custom-h3">' . esc_html__( 'Button option', 'vk-all-in-one-expansion-unit' ) . '</h3>';

		echo '<label>';
		echo '<p>' . esc_html__( 'Button text', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" name="' . esc_attr( $this->get_field_name( 'btn_text' ) ) . '" value="' . esc_attr( $instance['btn_text'] ) . '" class="admin-custom-input" />';
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

		echo '<h3 class="admin-custom-h3">' . esc_html__( 'Ranking URLs', 'vk-all-in-one-expansion-unit' ) . '</h3>';
		for ( $i = 1; $i <= $ranking_max; $i++ ) {
			echo '<label>';
			echo '<p>' . esc_html__( 'Page URL', 'vk-all-in-one-expansion-unit' ) . '[' . esc_html( $i ) . ']:</p>';
			echo '<input type="text" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'url' . $i ) ) . '" value="' . esc_attr( $instance[ 'url' . $i ] ) . '" />';
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
		global $ranking_max;

		$instance['title']       = $new_instance['title'];
		$instance['ranking_max'] = $new_instance['ranking_max'];

		for ( $i = 1; $i <= $ranking_max; $i++ ) {
			$instance[ 'url' . $i ] = esc_url( $new_instance[ 'url' . $i ] );
		}

		return $instance;
	}

	/**
	 * Widget Display
	 *
	 * @param array $args Widget Args.
	 * @param array $instance Widget Instance.
	 */
	public function widget( $args, $instance ) {

		global $ranking_max;

		echo wp_kses_post( $args['before_widget'] );
		echo '<div class="veu_ranking">';

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo wp_kses_post( $instance['title'] );
			echo wp_kses_post( $args['after_title'] );
		}

		$post__in = array();
		for ( $i = 1; $i <= $ranking_max; $i++ ) {
			if ( ! empty( $instance[ 'url' . $i ] ) ) {
				$post_url   = esc_url( $instance[ 'url' . $i ] );
				$post_id    = url_to_postid( $post_url );
				$post__in[] = $post_id;
			}
		}
	}
}
