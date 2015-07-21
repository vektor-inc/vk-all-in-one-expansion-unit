<?php
	$options = vkExUnit_get_sitemap_options();
	// $options_default = vkExUnit_get_sns_options_default();
/*-------------------------------------------*/
/*	sitemap page
/*-------------------------------------------*/
?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e('Sitemap page Settings', 'vkExUnit'); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
<th><?php _e('Sitemap page Settings', 'vkExUnit'); ?></th>

<td><?php _e('You want to add post type.', 'vkExUnit'); ?><br />
<p><input type="text" name="vkExUnit_ga_options[addPostType]" id="postType" value="<?php echo esc_attr( $options['addPostType'] ); ?>" style="width:50%;" /></p>
<br />
<?php _e('Input you want to exclude post id.', 'vkExUnit'); ?><br />
<p><input type="text" name="vkExUnit_ga_options[excludeId]" id="excludeId" value="<?php echo esc_attr( $options['excludeId'] ); ?>" style="width:50%;" /></p>

</td>
</tr>
</table>
<?php submit_button(); ?>
</div>