<div class="wrap vkExUnit_admin_page">
<h2>
<span class="pageTitleTxt">VK All in One Expansion Unit <?php _e('Enable setting','vkExUnit');?></span>
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
		<th scope='col' id='cb' class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all','vkExUnit');?></label><input id="cb-select-all-1" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'><?php _e('Function','vkExUnit');?></th><th scope='col' id='description' class='manage-column column-description'><?php _e('Description','vkExUnit');?></th>
	</tr>
	</thead>

	<tbody id="the-list">
<?php


	global $vkExUnit_packages;
	//$vkExUnit_packages = $package_boxs;
	foreach($vkExUnit_packages as $package): ?>
		<tr<?php echo ( vkExUnit_package_is_enable($package['name']))? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_<?php echo $package['name']; ?>' >
				<?php _e('Automatic Eye Catch insert', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_<?php echo $package['name']; ?>]" id="checkbox_active_<?php echo $package['name']; ?>" value="true" <?php echo ( vkExUnit_package_is_enable($package['name']) ) ? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php echo $package['title'] ?></strong>

				<?php
				$count = '';
				$count = count($package['attr']);
				$i = 0;
				if($count):
						foreach($package['attr'] as $att):
							if( !$att['enable_only'] || isset($options['active_'.$package['name']]) && $options['active_'.$package['name']]):
				?>
				<?php echo ( $count > 1 && $i >= 1) ? ' | ':''; ?>
				<span>
				<a href="<?php echo ( $att['url'] )? $att['url'] : admin_url().'admin.php?page=vkExUnit_main_setting' ;?>">
				<?php echo $att['name']; ?>
				</a></span>

				<?php
						endif;
						$i++;
					endforeach;
				endif; //if($count): ?>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<?php
						if(is_array($package['description'])):
							foreach($package['description'] as $desk){ echo $desk; }
						else: ?>
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
		<th scope='col'  class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select all','vkExUnit');?></label><input id="cb-select-all-2" type="checkbox" /></th><th scope='col'  class='manage-column column-name'><?php _e('Function', 'vkExUnit');?></th><th scope='col'  class='manage-column column-description'><?php _e('Description','vkExUnit');?></th>
	</tr>
	</tfoot>

</table>

<button onclick="javascript:jQuery('#vkEx_extention').toggle(); return false;"><?php _e('Extension Setting', 'vkExUnit'); ?></button>
<table class="form-table" id="vkEx_extention" style="display:none;">
<tr>
<th><?php _e('Plugin setting options','vkExUnit'); ?></th>
<td><label><input type="checkbox" name="vkExUnit_common_options[delete_options_at_deactivate]" value="true" <?php echo (isset($options['delete_options_at_deactivate']) && $options['delete_options_at_deactivate'])? 'checked':''; ?> />
<?php _e('Delete myOptions when deactivate me.', 'vkExUnit'); ?></label></td>
</tr>
</table>
<?php submit_button(); ?>
</form>
</div><!-- [ /.adminMain ] -->
<div class="adminSub">
<div class="exUnit_infoBox"><?php vkExUnit_news_body(); ?></div>
<div class="exUnit_adminBnr"><?php vkExUnit_admin_banner(); ?></div>
</div><!-- [ /.adminSub ] -->
</div>