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

		$ranking_max = ! empty( $instance['ranking_max'] ) ? $instance['ranking_max']: 10;

		echo '<h3 class="admin-custom-h3">' . __( 'Ranking Setting', 'vk-all-in-one-expansion-unit' ) . '</h3>';
		echo '<label>';
		echo '<p>' . esc_html__( 'Title:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( $instance['title'] ) . '" />';
		echo '</label>';

		echo '<label>';
		echo '<p>' . esc_html__( 'Display Ranking Value:', 'vk-all-in-one-expansion-unit' ) . '</p>';
		echo '<input type="text" class="admin-custom-input" name="' . esc_attr( $this->get_field_name( 'ranking_max' ) ) . '" value="' . esc_attr( $instance['ranking_max'] ) . '" />';
		echo '</label>';

		echo '<h3 class="admin-custom-h3">' . __( 'Ranking URLs', 'vk-all-in-one-expansion-unit' ) . '</h3>';
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

		$instance['title'] = $new_instance['title'];
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

		echo $args['before_widget'];
		echo PHP_EOL . '<div class="veu_ranking">' . PHP_EOL;

		if ( isset( $instance['title'] ) && $instance['title'] ) {
			echo $args['before_title'];
			echo $instance['title'];
			echo $args['after_title'];
		}

		echo '<div class="ranking">';

		for ( $i = 1; $i <= $ranking_max; $i++ ) {
			if ( ! empty( $instance[ 'url' . $i ] ) ) {
				$post_url = esc_url( $instance[ 'url' . $i ] );
				$post_id  = url_to_postid( $post_url );

			}
		}
	}
}
