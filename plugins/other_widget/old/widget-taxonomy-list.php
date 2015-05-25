<?php
/*-------------------------------------------*/
/*	Taxonomy list widget
/*-------------------------------------------*/
class WP_Widget_taxonomy_list extends WP_Widget {
    // ウィジェット定義
	function WP_Widget_taxonomy_list() {
		global $bizvektor_works_unit;
		$widget_ops = array(
			'classname' => 'WP_Widget_taxonomy_list',
			'description' => __( 'Displays a categories, tags or format list.', 'biz-vektor' ),
		);
		$lab = get_biz_vektor_name();
		if($lab == 'BizVektor'){
			$lab = 'BV';
		}
		$widget_name = $lab . '_' . __( 'categories/tags list', 'biz-vektor' );
		$this->WP_Widget('WP_Widget_taxonomy_list', $widget_name, $widget_ops);
	}

	function widget($args, $instance) {
		$arg = array(
			'echo'               => 1,
			'style'              => 'list',
			'show_count'         => false,
			'show_option_all'    => false,
			'hierarchical'       => true,
			'title_li'           => '',
			);

		$arg['taxonomy'] = $instance['tax_name'];

	?>
	<div class="localSection sideWidget">
	<div class="sectionBox">
		<h3 class="localHead"><?php echo $instance['label']; ?></h3>
		<ul class="localNavi">
			<?php wp_list_categories($arg); ?>
		</ul>
	</div>
	</div>
	<?php	
	}

	function form($instance){
		$defaults = array(
			'tax_name'     => 'category',
			'label'        => __( 'Category', 'biz-vektor' ),
			'hide'         => __( 'Category', 'biz-vektor' ),
			'title'		=> 'test',
			'_builtin'		=> false,
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		$taxs = get_taxonomies( array('public'=> true),'objects'); 
		?>
		<p>
		<label for="<?php echo $this->get_field_id('label'); ?>"><?php _e( 'Label to display', 'biz-vektor' ); ?></label>
		<input type="text"  id="<?php echo $this->get_field_id('label'); ?>-title" name="<?php echo $this->get_field_name('label'); ?>" value="<?php echo $instance['label']; ?>" ><br/>
		<input type="hidden" name="<?php echo $this->get_field_name('hide'); ?>" ><br/>
		
		<label for="<?php echo $this->get_field_id('tax_name'); ?>"><?php _e('Display page', 'biz-vektor') ?></label>
		<select name="<?php echo $this->get_field_name('tax_name'); ?>" >
		<?php foreach($taxs as $tax){ ?>
			<option value="<?php echo $tax->name; ?>" <?php if($instance['tax_name'] == $tax->name) echo 'selected="selected"'; ?> ><?php echo $tax->labels->name; ?></option>
		<?php } ?>
		</select>		</p>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			var post_labels = new Array();
			<?php
				foreach($taxs as $tax){
					if(isset($tax->labels->name)){
						echo 'post_labels["'.$tax->name.'"] = "'.$tax->labels->name.'";';
					}
				}
				echo 'post_labels["blog"] = "'. __( 'Blog', 'biz-vektor' ) . '";'."\n";
			?>
			var posttype = jQuery("[name=\"<?php echo $this->get_field_name('tax_name'); ?>\"]");
			var lablfeld = jQuery("[name=\"<?php echo $this->get_field_name('label'); ?>\"]");
			posttype.change(function(){
				lablfeld.val(post_labels[posttype.val()]+" <?php _e( 'Archives', 'biz-vektor' ) ?>");
			});
		});
		</script>
		<?php
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['tax_name'] = $new_instance['tax_name'];
		if(!$new_instance['label']){
			$new_instance['label'] = $new_instance['hide'];
		}
		$instance['label'] = esc_html($new_instance['label']);
		return $instance;
	}
} // class WP_Widget_top_list_info
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_taxonomy_list");'));