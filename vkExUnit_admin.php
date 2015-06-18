<h2>VK All in one Expansion Unit Settings.</h2>
<ul>
<li><a href="<?php echo admin_url();?>">SNS連携の設定</a></li>
<li>head タグ内に meta description を出力する</li>
<li>head タグ内に twitterカードのタグを出力する</li>
</ul>


<form method="post" action="options.php">
<?php submit_button(); ?>
<?php
	settings_fields( 'vkExUnit_common_options_fields' );
	$options = vkExUnit_get_common_options();
	print '<pre style="text-align:left">';print_r($options);print '</pre>';
?>

<table class="wp-list-table widefat plugins">
	<thead>
	<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'  style=""><label class="screen-reader-text" for="cb-select-all-1">すべて選択</label><input id="cb-select-all-1" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'  style="">プラグイン</th><th scope='col' id='description' class='manage-column column-description'  style="">説明</th>	</tr>
	</thead>

	<tbody id="the-list">
		<tr<?php echo (isset($options['active_metaDescription']) && $options['active_metaDescription'] == 'true')? ' class="active"': 'class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_metaDescription' >
				<?php _e('Choose Print meta description.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_metaDescription]" id="checkbox_active_metaDescription" value="true" <?php echo (isset($options['active_metaDescription']) && $options['active_metaDescription'] == 'true')? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong>Print meta description</strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p>Print meta description to html head.</p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>
		<tr<?php echo (isset($options['active_sns']) && $options['active_sns'] == 'true') ? ' class="active"': 'class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_sns' >
				<?php _e('Choose Social media cooperation.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_sns]" id="checkbox_active_sns" value="true" <?php echo (isset($options['active_sns']) && $options['active_sns'] == 'true')? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong>Social media cooperation</strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<ul>
					<li><?php _e('Print og tags to html head.','vkExUnit');?></li>
					<li><?php _e('Print twitter card tags to html head.','vkExUnit');?></li>
					<li><?php _e('Print social bookmarks.','vkExUnit');?></li>
					<li><?php _e('Facebook Page Plugin.','vkExUnit');?></li>
					</ul>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

		</tbody>

	<tfoot>
	<tr>
		<th scope='col'  class='manage-column column-cb check-column'  style=""><label class="screen-reader-text" for="cb-select-all-2">すべて選択</label><input id="cb-select-all-2" type="checkbox" /></th><th scope='col'  class='manage-column column-name'  style="">プラグイン</th><th scope='col'  class='manage-column column-description'  style="">説明</th>	</tr>
	</tfoot>

</table>
<?php submit_button(); ?>
</form>