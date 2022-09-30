<?php
	$options = vkExUnit_get_ga_options();
	// $options_default = veu_get_sns_options_default();
/*-------------------------------------------*/
/*  Google Analytics
/*-------------------------------------------*/
?>
<div id="seoSetting" class="sectionBox">
	<h3><?php _e( 'Google Analytics Settings', 'vk-all-in-one-expansion-unit' ); ?></h3>
	<table class="form-table">
		<!-- Google Analytics -->
		<tr>
			<th><?php _e( 'Google Analytics Settings', 'vk-all-in-one-expansion-unit' ); ?></th>
			<td>
				<?php _e( 'Please fill in the Google Analytics ID from the Analytics embed code used in the site.', 'vk-all-in-one-expansion-unit' ); ?><br />
				<br />
				<label for="gaId-GA4"><?php _e( 'GA4', 'vk-all-in-one-expansion-unit' ); ?></label><br />
				<input type="text" name="vkExUnit_ga_options[gaId-GA4]" id="gaId-GA4" value="<?php echo esc_attr( $options['gaId-GA4'] ); ?>" style="width:90%;" /><br />
				<?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' );?>G-XXXXXXXXXX<br />
				<br />
				<label for="gaId-UA"><?php _e( 'UA', 'vk-all-in-one-expansion-unit' ); ?></label><br />
				<input type="text" name="vkExUnit_ga_options[gaId-UA]" id="gaId-UA" value="<?php echo esc_attr( $options['gaId-UA'] ); ?>" style="width:90%;" /><br />
				<?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' );?>UA-XXXXXXXX-XX<br />
				<br />
				<label>
					<input type="checkbox" name="vkExUnit_ga_options[disableLoggedin]" id="disableLoggedin" value="true" <?php echo ( $options['disableLoggedin'] ) ? 'checked' : ''; ?> /><?php _e( 'Disable tracking of logged in user', 'vk-all-in-one-expansion-unit' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</div>
