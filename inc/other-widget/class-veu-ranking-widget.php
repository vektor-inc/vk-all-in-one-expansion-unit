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

	}

	/**
	 * Widget Update
	 *
	 * @param array $new_instance New Instance.
	 * @param array $old_instance Old Instance.
	 */
	public function update( $new_instance, $old_instance ) {

	}

	/**
	 * Widget Display
	 *
	 * @param array $args Widget Args.
	 * @param array $instance Widget Instance.
	 */
	public function widget( $args, $instance ) {

	}
}
