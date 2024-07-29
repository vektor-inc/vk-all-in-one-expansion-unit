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
		if ( ! isset( $instance['form_design'] ) ) {
			$instance['form_design'] = 'list';
		}
		
		?>
		<?php echo $args['before_widget']; ?>
		<div class="sideWidget widget_taxonomies widget_nav_menu">
			<?php echo $args['before_title'] . $instance['label'] . $args['after_title']; ?>
			<ul class="localNavi">

				<?php
				if ( 'list' === $instance['form_design'] ) {
					//
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
				} elseif ( 'select' === $instance['form_design'] ) {
					//
					$tax_args = array(
						'echo'              => 1,
						'class'             => 'veu_widget_taxonmomy',
						'show_option_none'  => __( 'Any', 'vk-filter-search-pro' ),
						'option_none_value' => '',
						'show_count'        => false,
						'hide_empty'        => $instance['hide_empty'],
						'hierarchical'      => true,
						'taxonomy'          => $instance['tax_name'],
					);
					$tax_args = apply_filters( 'veu_widget_taxlist_args', $tax_args ); //

					wp_dropdown_categories( $tax_args );
				}
				?>
			</ul>
		</div>
		<?php echo $args['after_widget']; ?>
		<?php
	}


	public static function get_defaults( $instance = array() ) {
		$defaults = array(
			'tax_name'    => 'category',
			'label'       => __( 'Category', 'vk-all-in-one-expansion-unit' ),
			'hide'        => __( 'Category', 'vk-all-in-one-expansion-unit' ),
			'title'       => __( 'Category', 'vk-all-in-one-expansion-unit' ),
			'form_design' => 'list',
			'hide_empty'  => false,
			'_builtin'    => false,
		);
		return wp_parse_args( (array) $instance, $defaults );
	}


	function form( $instance ) {
		$instance = static::get_defaults( $instance );
		$taxs     = get_taxonomies( array( 'public' => true ), 'objects' );
		?>

		<!-- タイトル -->
		<div style="margin-top:15px;">
			<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Label to display', 'vk-all-in-one-expansion-unit' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>"  class="admin-custom-input">
		</div>

		<input type="hidden" name="<?php echo $this->get_field_name( 'hide' ); ?>" >

		<!-- [ Select Taxonomy ] -->
		<div>
			<label for="<?php echo $this->get_field_id( 'tax_name' ); ?>"><?php _e( 'Category / Taxonomy', 'vk-all-in-one-expansion-unit' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'tax_name' ); ?>" class="admin-custom-input">
				<?php foreach ( $taxs as $tax ) { ?>
					<option value="<?php echo $tax->name; ?>"<?php if ( $instance['tax_name'] === $tax->name ) { echo ' selected="selected"'; } ?>>
						<?php echo $tax->labels->name; ?>
					</option>
				<?php } ?>
			</select>
		</div>

		<!-- [ Form format ] -->
		<div>
			<label for="<?php echo $this->get_field_id( 'form_design' ); ?>"><?php _e( 'Display design', 'vk-all-in-one-expansion-unit' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'form_design' ); ?>" class="admin-custom-input">
				<option value="list" <?php selected( $instance['form_design'], 'list' ); ?>><?php _e( 'Lists', 'vk-all-in-one-expansion-unit' ); ?></option>
				<option value="select" <?php selected( $instance['form_design'], 'select' ); ?>><?php _e( 'Drop down', 'vk-all-in-one-expansion-unit' ); ?></option>
			</select>
		</div>

		<input style="margin-top:3px" type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" value="true"<?php if ( $instance['hide_empty'] ) { echo ' checked';} ?>
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

		$instance['form_design'] = $new_instance['form_design'];

		return $instance;
	}
}

add_filter(
	'vkExUnit_master_js_options',
	function( $options ) {
		$options['homeUrl'] = home_url( '/' );
		return $options;
	},
	10,
	1
);