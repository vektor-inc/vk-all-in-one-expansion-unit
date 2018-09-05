<?php

/*-------------------------------------------*/
/*  Archive list widget
/*-------------------------------------------*/
class WP_Widget_VK_archive_list extends WP_Widget {

	function __construct() {
		$widget_name = veu_get_short_name() . ' ' . __( 'archive list', 'vkExUnit' );

		parent::__construct(
			'WP_Widget_VK_archive_list',
			$widget_name,
			array( 'description' => __( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.', 'vkExUnit' ) )
		);
	}


	function widget( $args, $instance ) {
		$arg = array(
			'echo' => 1,
		);

		if ( isset( $instance['display_type'] ) && $instance['display_type'] == 'y' ) {
			$arg['type']      = 'yearly';
			$arg['post_type'] = ( isset( $instance['post_type'] ) ) ? $instance['post_type'] : 'post';
			if ( strtoupper( get_locale() ) == 'JA' ) {
				$arg['after'] = '年';
			}
		} else {
			$arg['type']      = 'monthly';
			$arg['post_type'] = ( isset( $instance['post_type'] ) ) ? $instance['post_type'] : 'post';
		}
	?>
	<?php echo $args['before_widget']; ?>
	<div class="sideWidget widget_archive">
	<?php if ( ( isset( $instance['label'] ) ) && $instance['label'] ) { ?>
	<?php echo $args['before_title'] . $instance['label'] . $args['after_title']; ?>
	<?php } ?>
<ul class="localNavi">
	<?php wp_get_archives( $arg ); ?>
</ul>
</div>
	<?php echo $args['after_widget']; ?>
	<?php
	}


	function form( $instance ) {
		$defaults = array(
			'post_type'    => 'post',
			'display_type' => 'm',
			'label'        => __( 'Monthly archives', 'vkExUnit' ),
			'hide'         => __( 'Monthly archives', 'vkExUnit' ),
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		$pages    = get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			), 'names'
		);
		$pages[]  = 'post';
		?>
		<p>

		<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title', 'vkExUnit' ); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>" ><br/>
		<input type="hidden" name="<?php echo $this->get_field_name( 'hide' ); ?>" ><br/>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'vkExUnit' ); ?>:</label>
		<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" >
		<?php foreach ( $pages as $page ) { ?>
		<option value="<?php echo $page; ?>"
									<?php
									if ( $instance['post_type'] == $page ) {
										echo 'selected="selected"'; }
?>
 ><?php echo $page; ?></option>
		<?php } ?>
		</select>
		<br/>
		<label for="<?php echo $this->get_field_id( 'display_type' ); ?>">表示タイプ</label>
		<select name="<?php echo $this->get_field_name( 'display_type' ); ?>" >
			<option value="m"
			<?php
			if ( $instance['display_type'] != 'y' ) {
				echo 'selected="selected"'; }
?>
 >
			<?php _e( 'Monthly', 'vkExUnit' ); ?></option>
			<option value="y"
			<?php
			if ( $instance['display_type'] == 'y' ) {
				echo 'selected="selected"'; }
?>
 >
			<?php _e( 'Yearly', 'vkExUnit' ); ?></option>
		</select>
		</p>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			var post_labels = new Array();
			<?php
			foreach ( $pages as $page ) {
				$page_labl = get_post_type_object( $page );
				if ( isset( $page_labl->labels->name ) ) {
					echo 'post_labels["' . $page . '"] = "' . $page_labl->labels->name . '";';
				}
			}
				echo 'post_labels["blog"] = "Blog";' . "\n";
			?>
			var posttype = jQuery("[name=\"<?php echo $this->get_field_name( 'post_type' ); ?>\"]");
			var lablfeld = jQuery("[name=\"<?php echo $this->get_field_name( 'label' ); ?>\"]");
			posttype.change(function(){
				lablfeld.val(post_labels[posttype.val()]+'<?php _e( 'archive', 'vkExUnit' ); ?>');
			});
		});
		</script>
		<?php
	}


	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['post_type']    = $new_instance['post_type'];
		$instance['display_type'] = $new_instance['display_type'];
		if ( ! $new_instance['label'] ) {
			$new_instance['label'] = $new_instance['hide'];
		}
		$instance['label'] = $new_instance['label'];
		return $instance;
	}
}

add_action( 'widgets_init', 'vkExUnit_widget_register_archive_list' );
function vkExUnit_widget_register_archive_list() {
	return register_widget( 'WP_Widget_VK_archive_list' );
}
