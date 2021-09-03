<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

// namespace Vektor\ExUnit\Package\Cta;


/*-------------------------------------------*/
/*  Contact widget
/*-------------------------------------------*/
class Widget_CTA extends \WP_Widget {


	function __construct() {
		global $vk_call_to_action_textdomain;
		$widget_name = veu_get_prefix() . __( 'CTA', $vk_call_to_action_textdomain );

		parent::__construct(
			'vkExUnit_cta',
			$widget_name,
			array(
				'description' => __( 'Select CTA and display it.', $vk_call_to_action_textdomain ),
			)
		);
	}


	function widget( $args, $instance ) {

		// 各記事で非表示指定されてたら表示しない
		global $post;
		$post_config = get_post_meta( $post->ID, 'vkexunit_cta_each_option', true );
		if ( 'disable' === $post_config ) {
			return;
		}

		if ( isset( $instance['id'] ) && $instance['id'] ) {
			echo $args['before_widget'];
			if ( $instance['id'] == 'random' ) {
				$instance['id'] = Vk_Call_To_Action::cta_id_random();
			}
			echo Vk_Call_To_Action::render_cta_content( $instance['id'] );
			echo $args['after_widget'];
		}
		return;
	}


	function update( $new_instance, $old_instance ) {
		$cta_wid = array();
		if ( $new_instance['id'] == 'random' ) {
			$cta_wid['id'] = 'random';
		} else {
			$cta_wid['id'] = ( Vk_Call_To_Action::POST_TYPE == get_post_type( $new_instance['id'] ) ) ? $new_instance['id'] : null;
		}
		return $cta_wid;
	}


	function form( $instance ) {
		global $vk_call_to_action_textdomain;
		$defaults = array(
			'id' => null,
		);
		$instance = \wp_parse_args( (array) $instance, $defaults );
		$value    = $instance['id'];
		$ctas     = Vk_Call_To_Action::get_ctas( true, '- ' );
?>
<div style="padding:1em 0;">
	<?php _e( 'Please select CTA to display.', $vk_call_to_action_textdomain ); ?>
</div>
<div style="padding-bottom: 0.5em;">
<?php
  // ランダムを先頭に追加
array_unshift(
	$ctas, array(
		'key'   => 'random',
		'label' => __( 'Random', $vk_call_to_action_textdomain ),
	)
);
?>
<input type="hidden" name="_vkExUnit_cta_switch" value="cta_number" />
<select name="<?php echo $this->get_field_name( 'id' ); ?>" style="width: 100%" >
<option value="">[ <?php _e( 'Please select', $vk_call_to_action_textdomain ); ?> ]</option>
<?php foreach ( $ctas as $cta ) : ?>
	<option value="<?php echo $cta['key']; ?>" <?php echo( $value == $cta['key'] ) ? 'selected' : ''; ?> ><?php echo $cta['label']; ?></option>
<?php endforeach; ?>
</select>
</div>
<div style="padding-bottom: 1em;">
<a href="<?php echo admin_url( 'edit.php?post_type=cta' ); ?>" class="button button-default" target="_blank">
	<?php _e( 'Show CTA index page', $vk_call_to_action_textdomain ); ?>
</a>
<a href="<?php echo admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings' ); ?>" class="button button-default" target="_blank">
	<?php _e( 'CTA setting', $vk_call_to_action_textdomain ); ?>
</a>
</div>
<?php
		return $instance;
	}
}
