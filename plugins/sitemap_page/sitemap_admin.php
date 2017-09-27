<?php
$options = vkExUnit_get_sitemap_options();
// $options_default = veu_get_sns_options_default();
/*-------------------------------------------*/
/*  sitemap page
/*-------------------------------------------*/
?>
<div id="sitemapSetting" class="sectionBox">
<h3><?php _e( 'HTML Sitemap Settings', 'vkExUnit' ); ?></h3>
<table class="form-table">
<!-- sitemap -->
<tr>
<th><?php _e( 'Exclude page Settings', 'vkExUnit' ); ?></th>
<td>
<?php _e( 'Input you want to exclude page id.', 'vkExUnit' ); ?><br />
<p><input type="text" name="vkExUnit_sitemap_options[excludeId]" id="excludeId" value="<?php echo esc_attr( $options['excludeId'] ); ?>" style="width:50%;" /></p>
<?php _e( '* Please enter separated by ","(commas) if there is more than one page ID that you want to exclude.', 'vkExUnit' ); ?>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
