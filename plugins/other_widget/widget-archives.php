<?php

/*-------------------------------------------*/
/*	Archive list widget
/*-------------------------------------------*/
class WP_Widget_VK_archive_list extends WP_Widget {
    // ウィジェット定義
	function WP_Widget_VK_archive_list() {
		global $bizvektor_works_unit;
		$widget_ops = array(
			'classname'   => 'WP_Widget_VK_archive_list',
			'description' => __( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.' , 'vkExUnit' ),
		);
		$widget_name = 'VK_' . __( 'archive list', 'vkExUnit' );
		$this->WP_Widget('WP_Widget_VK_archive_list', $widget_name, $widget_ops);
	}

	function widget($args, $instance) {
		$arg = array(
			'echo' => 1,
			);

		if($instance['display_type'] == 'y'){
			$arg['type']      = "yearly";
			$arg['post_type'] = $instance['post_type'];
			$arg['after']     = '年';
		}
		else{
			$arg['type']      = "monthly";
			$arg['post_type'] = $instance['post_type'];
		}

	?>
	<div class="localSection sideWidget">
	<div class="sectionBox">
		<h3 class="localHead"><?php echo $instance['label']; ?></h3>
		<ul class="localNavi">
			<?php wp_get_archives($arg); ?>
		</ul>
	</div>
	</div>
	<?php
	}

	function form($instance){
		$defaults = array(
			'post_type'    => 'post',
			'display_type' => 'm',
			'label'        => __( 'Monthly archives', 'vkExUnit' ),
			'hide'         => __( 'Monthly archives', 'vkExUnit' ),
		);

		$instance = wp_parse_args((array) $instance, $defaults);
		$pages = get_post_types( array('public'=> true, '_builtin' => false),'names');
		$pages[] = 'post';
		?>
		<p>

		<label for="<?php echo $this->get_field_id('label'); ?>"><?php _e('Title','vkExUnit');?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('label'); ?>-title" name="<?php echo $this->get_field_name('label'); ?>" value="<?php echo $instance['label']; ?>" ><br/>
		<input type="hidden" name="<?php echo $this->get_field_name('hide'); ?>" ><br/>

		<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e( 'Post type', 'vkExUnit' ) ?>:</label>
		<select name="<?php echo $this->get_field_name('post_type'); ?>" >
		<?php foreach($pages as $page){ ?>
		<option value="<?php echo $page; ?>" <?php if($instance['post_type'] == $page) echo 'selected="selected"'; ?> ><?php echo $page; ?></option>
		<?php } ?>
		</select>
		<br/>
		<label for="<?php echo $this->get_field_id('display_type'); ?>">表示タイプ</label>
		<select name="<?php echo $this->get_field_name('display_type'); ?>" >
			<option value="m" <?php if($instance['display_type'] != "y") echo 'selected="selected"'; ?> >月別</option>
			<option value="y" <?php if($instance['display_type'] == "y") echo 'selected="selected"'; ?> >年別</option>
		</select>
		</p>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			var post_labels = new Array();
			<?php
				foreach($pages as $page){
					$page_labl = get_post_type_object($page);
					if(isset($page_labl->labels->name)){
						echo 'post_labels["'.$page.'"] = "'.$page_labl->labels->name.'";';
					}
				}
				echo 'post_labels["blog"] = "ブログ";'."\n";
			?>
			var posttype = jQuery("[name=\"<?php echo $this->get_field_name('post_type'); ?>\"]");
			var lablfeld = jQuery("[name=\"<?php echo $this->get_field_name('label'); ?>\"]");
			posttype.change(function(){
				lablfeld.val(post_labels[posttype.val()]+'アーカイブ');
			});
		});
		</script>
		<?php
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		
		$instance['post_type']    = $new_instance['post_type'];
		$instance['display_type'] = $new_instance['display_type'];
		
		if(!$new_instance['label']){
			$new_instance['label'] = $new_instance['hide'];
		}
		$instance['label'] = $new_instance['label'];
		
		return $instance;
	}
} // class WP_Widget_top_list_info
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_VK_archive_list");'));
