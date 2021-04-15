<?php
	$options = get_option( 'vk_google_tag_manager_related_options' );
	$options_value = ! empty( $options['gtm_id'] ) ? $options['gtm_id'] : '';
/*-------------------------------------------*/
/*  Google Analytics
/*-------------------------------------------*/
?>
<div id="gtmSetting" class="sectionBox">
<h3><?php _e( 'Google Tag Manager Setting', 'vk-all-in-one-expansion-unit' ); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
	<th><?php _e( 'Google tag manager ID', 'vk-all-in-one-expansion-unit' ); ?></th>
	<td>
		<p><?php _e( 'Please enter the Google Tag Manager ID to use on this site.', 'vk-all-in-one-expansion-unit' ); ?></p>
		<p>GTM-<input type="text" name="vk_google_tag_manager_related_options[gtm_id]" id="gtm_id" value="<?php echo esc_attr( $options_value ); ?>" style="width:90%;" /></p>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>