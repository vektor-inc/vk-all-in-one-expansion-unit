<?php

/*
  Taxonomy list widget
/*-------------------------------------------*/
class WP_Widget_VK_taxonomy_list extends WP_Widget {
	function __construct() {
		parent::__construct(
			'WP_Widget_VK_taxonomy_list',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		return veu_get_prefix() . __( 'Categories/Custom taxonomies list', 'vk-all-in-one-expansion-unit' );
	}

	public static function veu_widget_description() {
		return __( 'Displays a categories and custom taxonomies list.', 'vk-all-in-one-expansion-unit' );
	}

	function widget( $args, $instance ) {
		$instance = static::get_defaults( $instance );
		if ( ! isset( $instance['tax_name'] ) ) {
			$instance['tax_name'] = 'category';
		}
		if ( ! isset( $instance['label'] ) ) {
			$instance['label'] = __( 'Category', 'vk-all-in-one-expansion-unit' );
		}
		?>
		<?php echo $args['before_widget']; ?>
		<div class="sideWidget widget_taxonomies widget_nav_menu">
			<?php echo $args['before_title'] . $instance['label'] . $args['after_title']; ?>
			<ul class="localNavi">
				<?php
				$tax_args = array(
					'echo'            => 1,
					'style'           => 'list',
					'show_count'      => false,
					'show_option_all' => false,
					'hide_empty'      => $instance['hide_empty'],
					'hierarchical'    => true,
					'title_li'        => '',
					'taxonomy'        => $instance['tax_name'],
				);
				$tax_args = apply_filters( 'veu_widget_taxlist_args', $tax_args ); // 9.13.0.0
				wp_list_categories( $tax_args );
				?>
			</ul>
		</div>
		<?php echo $args['after_widget']; ?>
		<?php
	}


	public static function get_defaults( $instance = array() ) {
		$defaults = array(
			'tax_name'   => 'category',
			'label'      => __( 'Category', 'vk-all-in-one-expansion-unit' ),
			'hide'       => __( 'Category', 'vk-all-in-one-expansion-unit' ),
			'title'      => 'Category',
			'hide_empty' => false,
			'_builtin'   => false,
		);
		return wp_parse_args( (array) $instance, $defaults );
	}


	function form( $instance ) {
		$instance = static::get_defaults( $instance );
		$taxs     = get_taxonomies( array( 'public' => true ), 'objects' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Label to display', 'vk-all-in-one-expansion-unit' ); ?></label>
		<input type="text"  id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" ><br/>
		<input type="hidden" name="<?php echo $this->get_field_name( 'hide' ); ?>" ><br/>

		<label for="<?php echo $this->get_field_id( 'tax_name' ); ?>"><?php _e( 'Display page', 'vk-all-in-one-expansion-unit' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'tax_name' ); ?>" >

		<?php foreach ( $taxs as $tax ) { ?>
			<option value="<?php echo $tax->name; ?>"
										<?php
										if ( $instance['tax_name'] == $tax->name ) {
											echo 'selected="selected"'; }
										?>
 ><?php echo $tax->labels->name; ?></option>
		<?php } ?>
		</select><br/><br/>

		<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" value="true"
												<?php
												if ( $instance['hide_empty'] ) {
													echo 'checked';}
												?>
 />
		<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php _e( 'Do not display terms without posts', 'vk-all-in-one-expansion-unit' ); ?></label>
		</p>

		<script type="text/javascript">
		jQuery(document).ready(function($){
			var post_labels = new Array();
			<?php
			foreach ( $taxs as $tax ) {
				if ( isset( $tax->labels->name ) ) {
					echo 'post_labels["' . $tax->name . '"] = "' . $tax->labels->name . '";';
				}
			}
				echo 'post_labels["blog"] = "' . __( 'Blog', 'vk-all-in-one-expansion-unit' ) . '";' . "\n";
			?>
			var posttype = jQuery("[name=\"<?php echo $this->get_field_name( 'tax_name' ); ?>\"]");
			var lablfeld = jQuery("[name=\"<?php echo $this->get_field_name( 'label' ); ?>\"]");
			posttype.change(function(){
				lablfeld.val(post_labels[posttype.val()]+" <?php _e( 'Archives', 'vk-all-in-one-expansion-unit' ); ?>");
			});
		});
		</script>
		<?php
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['tax_name'] = $new_instance['tax_name'];

		if ( ! $new_instance['label'] ) {
			$new_instance['label'] = $new_instance['hide'];
		}
		$instance['label'] = esc_html( $new_instance['label'] );

		$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) && $new_instance['hide_empty'] == 'true' );

		return $instance;
	}
}
