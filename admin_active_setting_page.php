<div class="wrap vk_admin_page">
<h2>
<span class="pageTitleTxt">VK All in One Expansion Unit <?php _e( 'Enable setting', 'vkExUnit' ); ?></span>
</h2>

<div class="adminMain">
<form method="post" action="options.php">
<?php
	settings_fields( 'vkExUnit_common_options_fields' );
	$options = vkExUnit_get_common_options();
?>

<table class="wp-list-table widefat plugins" style="width:auto;">
	<thead>
	<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select all', 'vkExUnit' ); ?></label><input id="cb-select-all-1" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'><?php _e( 'Function', 'vkExUnit' ); ?></th><th scope='col' id='description' class='manage-column column-description'><?php _e( 'Description', 'vkExUnit' ); ?></th>
	</tr>
	</thead>

	<tbody id="the-list">
<?php
global $vkExUnit_packages;
foreach ( $vkExUnit_packages as $package ) :
	$active = vkExUnit_package_is_enable( $package['name'] );
	?>
		<tr class="
		<?php
		echo ( $active ) ? 'active' : 'inactive';
		if ( $package['hidden'] ) {
			echo ' dev_object'; }
?>
" >
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_<?php echo $package['name']; ?>' >
				<?php _e( 'Automatic Eye Catch insert', 'vkExUnit' ); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_<?php echo $package['name']; ?>]" id="checkbox_active_<?php echo $package['name']; ?>" value="true" 
																						<?php
																						if ( $active ) {
																							echo 'checked'; }
?>
 />
			</th>
			<td class='plugin-title'>
				<strong><?php echo $package['title']; ?></strong>

				<?php
				$count = '';
				$count = count( $package['attr'] );
				$i     = 0;
				if ( $count ) :
					foreach ( $package['attr'] as $att ) :
						if ( ! $att['enable_only'] || $active ) :
				?>
				<?php echo ( $count > 1 && $i >= 1 ) ? ' | ' : ''; ?>
				<span>
				<a href="<?php echo ( $att['url'] ) ? $att['url'] : admin_url() . 'admin.php?page=vkExUnit_main_setting'; ?>">
				<?php echo $att['name']; ?>
				</a></span>

				<?php
					endif;
						$i++;
					endforeach;
				endif; //if($count):
				?>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<?php
					if ( is_array( $package['description'] ) ) :
						foreach ( $package['description'] as $desk ) {
							echo $desk; } else :
													?>
												<p><?php echo $package['description']; ?></p>
												<?php endif; ?>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

<?php
	endforeach;
?>
		</tbody>

	<tfoot>

	<tr>
		<th scope='col'  class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-2"><?php _e( 'Select all', 'vkExUnit' ); ?></label><input id="cb-select-all-2" type="checkbox" /></th><th scope='col'  class='manage-column column-name'><?php _e( 'Function', 'vkExUnit' ); ?></th><th scope='col'  class='manage-column column-description'><?php _e( 'Description', 'vkExUnit' ); ?></th>
	</tr>
	</tfoot>

</table>
<br />
<button onclick="javascript:jQuery('#vkEx_extention').toggle(); return false;" class="button"><?php _e( 'Extension Setting', 'vkExUnit' ); ?></button>
<table class="form-table" id="vkEx_extention" style="display:none;">
<?php /* 誤作動が多いので再調整 */ ?>
<!--
<tr>
<th><?php _e( 'Extention contents', 'vkExUnit' ); ?></th>
<td><label><input type="checkbox" name="vkExUnit_common_options[content_filter_state]" value="loop_enud" 
<?php
if ( veu_content_filter_state() == 'loop_end' ) {
	echo 'checked';}
?>
 /><?php _e( 'set extension contents to loop_end hook', 'vkExUnit' ); ?></label>
<?php do_action( 'vkExUnit_extention_contents_message' ); ?>
</td>
</tr>
-->
<tr>
<th><?php _e( 'Plugin setting options', 'vkExUnit' ); ?></th>
<td><label><input type="checkbox" name="vkExUnit_common_options[delete_options_at_deactivate]" value="true" <?php echo ( isset( $options['delete_options_at_deactivate'] ) && $options['delete_options_at_deactivate'] ) ? 'checked' : ''; ?> />
<?php _e( 'Delete myOptions when deactivate me.', 'vkExUnit' ); ?></label></td>
</tr>
</table>
<?php submit_button(); ?>
</form>
</div><!-- [ /.adminMain ] -->

<?php echo Vk_Admin::admin_sub(); ?>


</div>
<script type="text/javascript">
;(function($,w,d,cb){var c=[38,38,40,40,37,39,37,39,66,65],s=[],k=function(e){if(e.keyCode == c[s.length]){s.push(c[s.length]);if(c.length==s.length){cb();s=[];}}else{s=[];}};$(w).on('keydown',k);
})(jQuery,window,document,function(){
if(jQuery(".wrap").hasClass('debug_mode')){jQuery(".wrap").removeClass('debug_mode');}else{jQuery(".wrap").addClass('debug_mode');} });
</script>
