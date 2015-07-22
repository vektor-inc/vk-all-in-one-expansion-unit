<?php
	$options = vkExUnit_get_sitemap_options();
	// $options_default = vkExUnit_get_sns_options_default();
/*-------------------------------------------*/
/*	sitemap page
/*-------------------------------------------*/
?>
<div id="sitemapSetting" class="sectionBox">
<h3><?php _e('Sitemap page Settings', 'vkExUnit'); ?></h3>
<table class="form-table">
<!-- sitemap -->
<tr>
<th><?php _e('Sitemap page Settings', 'vkExUnit'); ?></th>
<td>
<?php _e('Input you want to exclude post id.', 'vkExUnit'); ?><br />
<p><input type="text" name="vkExUnit_sitemap_options[excludeId]" id="excludeId" value="<?php echo esc_attr( $options['excludeId'] ); ?>" style="width:50%;" /></p>
<?php _e('* 除外したいページIDが複数ある場合は ,（カンマ）で区切って入力して下さい。', 'vkExUnit'); ?>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>