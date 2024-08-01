<?php
use VektorInc\VK_Admin\VkAdmin;
?>
<div class="wrap vk_admin_page">
<h2>
<span class="pageTitleTxt"><?php echo veu_get_name(); ?> <?php _e( 'Enable setting', 'vk-all-in-one-expansion-unit' ); ?></span>
</h2>

<div class="adminLayout">
<div class="adminMain">

<form method="post" action="options.php">
<?php
	settings_fields( 'vkExUnit_common_options_fields' );
	$options = veu_get_common_options();
?>

<table class="wp-list-table widefat plugins" style="width:auto;">
	<thead>
	<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'><label class="screen-reader-text" for="th-select-all-h"><?php _e( 'Select all', 'vk-all-in-one-expansion-unit' ); ?></label><input id="th-select-all-h" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'><?php _e( 'Function', 'vk-all-in-one-expansion-unit' ); ?></th><th scope='col' id='description' class='manage-column column-description'><?php _e( 'Description', 'vk-all-in-one-expansion-unit' ); ?></th>
	</tr>
	</thead>

	<tbody id="the-list">
<?php
global $vkExUnit_packages;
foreach ( $vkExUnit_packages as $package ) :
	$active = veu_package_is_enable( $package['name'] );

	if ( ! empty( $package['section_title'] ) ){
		echo '<tr><th colspan="3" class="section_title">' . $package['section_title'] . '</th></tr>';
	} else {

	?>
		<tr class="
		<?php
		echo ( $active ) ? 'active' : 'inactive';
		if ( $package['hidden'] ) {
			echo ' dev_object'; }
?>
" >
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_<?php echo esc_attr( $package['name'] ); ?>' ><?php echo esc_html( $package['title'] ); ?></label>
				<input type="checkbox" name="vkExUnit_common_options[active_<?php echo esc_attr( $package['name'] ); ?>]" id="checkbox_active_<?php echo esc_attr( $package['name'] ); ?>" value="true" <?php if(!$package['hidden']){echo 'class="vew-module-checkbox"';}; ?>
																						<?php
																						if ( $active ) {
																							echo 'checked'; }
?>
 />
			</th>
			<td class='plugin-title'>
				<label for='checkbox_active_<?php echo esc_attr( $package['name'] ); ?>'><strong><?php echo esc_html( $package['title'] ); ?></strong></label>

				<?php
				$count = '';
				$count = count( $package['attr'] );
				$i     = 0;
				if ( $count ) :
					foreach ( $package['attr'] as $att ) :
						if ( 
							// パッケージが有効化されている
							$active ||
							// 有効ではないが enable only が false のとき
							empty( $att['enable_only'] ) ) :
				?>
				<?php echo ( $count > 1 && $i >= 1 ) ? ' | ' : ''; ?>
				<span>
				<a href="<?php echo ( $att['url'] ) ? esc_html( $att['url'] ) : admin_url() . 'admin.php?page=vkExUnit_main_setting'; ?>">
				<?php echo esc_html( $att['name'] ); ?>
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
							echo wp_kses_post( $desk ); } else :
													?>
												<p><?php echo wp_kses_post( $package['description'] ); ?></p>
												<?php endif; ?>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>
		<?php } ?>

<?php
	endforeach;
?>
		</tbody>

	<tfoot>

	<tr>
		<th scope='col'  class='manage-column column-cb check-column'><label class="screen-reader-text" for="th-select-all-f"><?php _e( 'Select all', 'vk-all-in-one-expansion-unit' ); ?></label><input id="th-select-all-f" type="checkbox" /></th><th scope='col'  class='manage-column column-name'><?php _e( 'Function', 'vk-all-in-one-expansion-unit' ); ?></th><th scope='col'  class='manage-column column-description'><?php _e( 'Description', 'vk-all-in-one-expansion-unit' ); ?></th>
	</tr>
	</tfoot>

</table>
<br />

<?php
	do_action( 'vew_admin_setting_block', $options);
?>

<button onclick="javascript:jQuery('#vkEx_extention').toggle(); return false;" class="button"><?php _e( 'Extension Setting', 'vk-all-in-one-expansion-unit' ); ?></button>
<table class="form-table" id="vkEx_extention" style="display:none;">
<?php /* 誤作動が多いので再調整 */ ?>
<!--
<tr>
<th><?php _e( 'Extention contents', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><label><input type="checkbox" name="vkExUnit_common_options[content_filter_state]" value="loop_enud"
<?php
if ( veu_content_filter_state() == 'loop_end' ) {
	echo 'checked';}
?>
 /><?php _e( 'set extension contents to loop_end hook', 'vk-all-in-one-expansion-unit' ); ?></label>
<?php do_action( 'vkExUnit_extention_contents_message' ); ?>
</td>
</tr>
-->
<tr>
<th><?php _e( 'Post edit page metabox settings', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><label><input type="checkbox" name="vkExUnit_common_options[post_metabox_individual]" value="true" <?php echo ( isset( $options['post_metabox_individual'] ) && $options['post_metabox_individual'] ) ? 'checked' : ''; ?> />
<?php _e( 'Do not combine ExUnit\'s meta box', 'vk-all-in-one-expansion-unit' ); ?></label></td>
</tr>
<tr>
<th><?php _e( 'Plugin setting options', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><label><input type="checkbox" name="vkExUnit_common_options[delete_options_at_deactivate]" value="true" <?php echo ( isset( $options['delete_options_at_deactivate'] ) && $options['delete_options_at_deactivate'] ) ? 'checked' : ''; ?> />
<?php _e( 'Delete myOptions when deactivate me.', 'vk-all-in-one-expansion-unit' ); ?></label></td>
</tr>
</table>
<?php submit_button(); ?>
</form>
</div><!-- [ /.adminMain ] -->

<?php echo VkAdmin::admin_sub(); ?>
</div>

<script type="text/javascript">
((d)=>{
let fc=(c,f)=>{Array.prototype.forEach.call(d.getElementsByClassName(c),f)};
((cb)=>{let c=[38,38,40,40,37,39,37,39,66,65],p=0;d.addEventListener('keydown',(e)=>{if(e.keyCode!=c[p]){p=0;return}if(++p>=c.length){p=0;try{cb()}catch(e){};return;}});})(()=>{Array.prototype.forEach.call(d.getElementsByClassName('wrap'),(i)=>{if(i.classList.contains('debug_mode')){i.classList.remove('debug_mode')}else{i.classList.add('debug_mode')}})});
})(document);
</script>

</div><!-- [ /.wrap ] -->
