<h2>VK All in One Expansion Unit Settings.</h2>

<form method="post" action="options.php">
<?php
	settings_fields( 'vkExUnit_common_options_fields' );
	$options = vkExUnit_get_common_options();
?>

<table class="wp-list-table widefat plugins" style="width:auto;">
	<thead>
	<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select all','vkExUnit');?></label><input id="cb-select-all-1" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'><?php _e('Function','vkExUnit');?></th><th scope='col' id='description' class='manage-column column-description'><?php _e('Description','vkExUnit');?></th>	</tr>
	</thead>

	<tbody id="the-list">

		<!-- [ active_bootstrap ] -->
		<tr<?php echo (isset($options['active_bootstrap']) && $options['active_bootstrap'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_bootstrap' >
				<?php _e('Choose Print Bootstrap css', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_bootstrap]" id="checkbox_active_bootstrap" value="true" <?php echo (isset($options['active_bootstrap']) && $options['active_bootstrap'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Print Bootstrap css and js', 'vkExUnit'); ?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('If your using theme has already including Bootstrap, you deactivate this item.','vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>
		
		<!-- [ active_fontawesome ] -->
		<tr<?php echo (isset($options['active_fontawesome']) && $options['active_fontawesome'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_fontawesome' >
				<?php _e('Choose Print link fontawesome.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_fontawesome]" id="checkbox_active_fontawesome" value="true" <?php echo (isset($options['active_fontawesome']) && $options['active_fontawesome'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Print link fontawesome.', 'vkExUnit'); ?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Print fontawesome link tag to html head.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>
		
		<!-- [ active_metaDescription ] -->
		<tr<?php echo (isset($options['active_metaDescription']) && $options['active_metaDescription'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_metaDescription' >
				<?php _e('Choose Print meta description.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_metaDescription]" id="checkbox_active_metaDescription" value="true" <?php echo (isset($options['active_metaDescription']) && $options['active_metaDescription'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Print meta description', 'vkExUnit'); ?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Print meta description to html head.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

		<!-- [ active_sns ] -->
		<tr<?php echo (isset($options['active_sns']) && $options['active_sns']) ? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_sns' >
				<?php _e('Choose Social media cooperation.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_sns]" id="checkbox_active_sns" value="true" <?php echo (isset($options['active_sns']) && $options['active_sns'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong>Social media cooperation</strong>
				<div class="row-actions visible">

				<?php if (isset($options['active_sns']) && $options['active_sns']) : ?>

					<span class="0">
					<a href="<?php echo admin_url().'admin.php?page=vkExUnit_main_setting';?>">
					<?php _e('Setting','vkExUnit');?>
					</a></span>

				<?php endif; ?>

				</div>
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

		<!-- [ active_ga ] -->
		<tr<?php echo (isset($options['active_ga']) && $options['active_ga'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_ga' >
				<?php _e('Choose Print Google Analytics tracking code.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_ga]" id="checkbox_active_ga" value="true" <?php echo (isset($options['active_ga']) && $options['active_ga'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong>Google Analytics</strong>

				<?php if (isset($options['active_ga']) && $options['active_ga']) : ?>
					
					<span class="0">
					<a href="<?php echo admin_url().'admin.php?page=vkExUnit_main_setting';?>">
					<?php _e('Setting','vkExUnit');?>
					</a></span>

				<?php endif; ?>

			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Print Google Analytics tracking code.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

		<!-- [ active_relatedPosts ] -->
		<tr<?php echo (isset($options['active_relatedPosts']) && $options['active_relatedPosts'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_relatedPosts' >
				<?php _e('Choose Related posts.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_relatedPosts]" id="checkbox_active_relatedPosts" value="true" <?php echo (isset($options['active_relatedPosts']) && $options['active_relatedPosts'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Related posts', 'vkExUnit');?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Print Related posts lists to post content bottom.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

		<!-- [ active_otherWidgets ] -->
		<tr<?php echo (isset($options['active_otherWidgets']) && $options['active_otherWidgets'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_otherWidgets' >
				<?php _e('Choose other widgets.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_otherWidgets]" id="checkbox_active_otherWidgets" value="true" <?php echo (isset($options['active_otherWidgets']) && $options['active_otherWidgets'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Other Widgets', 'vkExUnit');?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('You can use various widgets.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

		<!-- [ CSS cosutomize ] -->
		<tr<?php echo (isset($options['active_css_customize']) && $options['active_css_customize'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_css_customize' >
				<?php _e('Choose other widgets.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_css_customize]" id="checkbox_active_css_customize" value="true" <?php echo (isset($options['active_css_customize']) && $options['active_css_customize'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('CSS customize', 'vkExUnit');?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('You can set Customize CSS.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>
		</tbody>

	<tfoot>
	<tr>
		<th scope='col'  class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select all','vkExUnit');?></label><input id="cb-select-all-2" type="checkbox" /></th><th scope='col'  class='manage-column column-name'><?php _e('Function', 'vkExUnit');?></th><th scope='col'  class='manage-column column-description'><?php _e('Description','vkExUnit');?></th>	</tr>
	</tfoot>

</table>
<?php submit_button(); ?>
</form>