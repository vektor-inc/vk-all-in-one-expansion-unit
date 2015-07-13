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
		
		<!-- [ active_icon ] -->
		<tr<?php echo (isset($options['active_icon']) && $options['active_icon'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_icon' >
				<?php _e('Choose Print meta description.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_icon]" id="checkbox_active_icon" value="true" <?php echo (isset($options['active_icon']) && $options['active_icon'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Favicon setting', 'vkExUnit'); ?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('About favicon.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>
		
		<!-- [ active_wpTitle ] -->
		<tr<?php echo (isset($options['active_wpTitle']) && $options['active_wpTitle'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_wpTitle' >
				<?php _e('Choose Rewrite the title tag', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_wpTitle]" id="checkbox_active_wpTitle" value="true" <?php echo (isset($options['active_wpTitle']) && $options['active_wpTitle'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Rewrite the title tag', 'vkExUnit'); ?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Print is rewritten by its own rules to html head.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-wpTitle ] -->
			</td>
		</tr>

		<!-- [ active_metaKeyword ] -->
		<tr<?php echo (isset($options['active_metaKeyword']) && $options['active_metaKeyword'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_metaKeyword' >
				<?php _e('Choose Print meta Keyword.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_metaKeyword]" id="checkbox_active_metaKeyword" value="true" <?php echo (isset($options['active_metaKeyword']) && $options['active_metaKeyword'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Print meta Keyword', 'vkExUnit'); ?></strong>

				<?php if (isset($options['active_metaKeyword']) && $options['active_metaKeyword']) : ?>
				<div class="row-actions visible">
					<span class="0">
					<a href="<?php echo admin_url().'admin.php?page=vkExUnit_main_setting#vkExUnit_common_keywords';?>">
					<?php _e('Setting','vkExUnit');?>
					</a></span>
				</div>
				<?php endif; ?>
				
			</td>
			<td class='column-Keyword desc'>
				<div class='plugin-Keyword'>
					<p><?php _e('Print meta Keyword to html head.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-Keyword ] -->
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
				<strong><?php _e('Social media cooperation.', 'vkExUnit'); ?></strong>
				
				<?php if (isset($options['active_sns']) && $options['active_sns']) : ?>
				<div class="row-actions visible">
					<span class="0">
					<a href="<?php echo admin_url().'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options';?>">
					<?php _e('Setting','vkExUnit');?>
					</a></span>
				</div>
				<?php endif; ?>
				
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<ul>
					<li><?php _e('Print og tags to html head.','vkExUnit');?></li>
					<li><?php _e('Print twitter card tags to html head.','vkExUnit');?></li>
					<li><?php _e('Print social bookmark buttons.','vkExUnit');?></li>
					<li><?php _e('Facebook Page Plugin widget.','vkExUnit');?></li>
					<li><?php _e('Print Follow me box to content bottom.','vkExUnit');?></li>
					</ul>
					<p><?php 
					$settingPage = '<a href="'.admin_url().'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options">'.__('Main setting page').'</a>';
						printf( __( '* You can stop the function separately from the %s.', 'vkExUnit' ), $settingPage );?>
					</p>
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
					<a href="<?php echo admin_url().'admin.php?page=vkExUnit_main_setting#vkExUnit_ga_options';?>">
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
		
		<!-- [ active_childPageIndex ] -->
		<tr<?php echo (isset($options['active_childPageIndex']) && $options['active_childPageIndex'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_relatedPosts' >
				<?php _e('Choose display a child page index to posts.', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_childPageIndex]" id="checkbox_active_childPageIndex" value="true" <?php echo (isset($options['active_childPageIndex']) && $options['active_childPageIndex'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Child page index', 'vkExUnit');?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Display a child page index to posts.', 'vkExUnit'); ?></p>
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
				<strong><?php _e('Widgets', 'vkExUnit');?></strong>
				<span>
				<a href="<?php echo admin_url().'widgets.php';?>">
				<?php _e('Setting','vkExUnit');?>
				</a></span>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('You can use various widgets.', 'vkExUnit'); ?></p>
					<ul>
					<li><?php _e('VK_Recent Posts - display the link text and the date of the latest article title.','vkExUnit');?></li>
					<li><?php _e('VK_Page content to widget - display the contents of the page to the widgets.','vkExUnit');?></li>
					<li><?php _e('VK_Profile - display the profile entered in the widget.','vkExUnit');?></li>
					<li><?php _e('VK_FB Page Plugin - display the Facebook Page Plugin.','vkExUnit');?></li>
					<li><?php _e('VK_3PR area - display the 3PR area.','vkExUnit');?></li>
					<li>VK_<?php _e( 'categories/tags list', 'vkExUnit' ); ?> - <?php _e( 'Displays a categories, tags or format list.', 'vkExUnit' ); ?></li>
					<li>VK_<?php _e( 'archive list', 'vkExUnit' ); ?> - <?php _e( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.' , 'vkExUnit' ); ?></li>
					</ul>
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
				<?php if (isset($options['active_css_customize']) && $options['active_css_customize']) : ?>
					
					<span>
					<a href="<?php echo admin_url().'admin.php?page=vkExUnit_css_customize';?>">
					<?php _e('Setting','vkExUnit');?>
					</a></span>

				<?php endif; ?>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('You can set Customize CSS.', 'vkExUnit'); ?></p>
				</div><!-- [ /.plugin-description ] -->
			</td>
		</tr>

		<!-- [ AUTO Eye Catch ] -->
		<tr<?php echo (isset($options['active_auto_eyecatch']) && $options['active_auto_eyecatch'])? ' class="active"': ' class="inactive"'; ?>>
			<th scope='row' class='check-column'>
				<label class='screen-reader-text' for='checkbox_active_auto_eyecatch' >
				<?php _e('Automatic Eye Catch insert', 'vkExUnit'); ?>
				</label>
				<input type="checkbox" name="vkExUnit_common_options[active_auto_eyecatch]" id="checkbox_active_auto_eyecatch" value="true" <?php echo (isset($options['active_auto_eyecatch']) && $options['active_auto_eyecatch'])? 'checked': ''; ?> />
			</th>
			<td class='plugin-title'>
				<strong><?php _e('Automatic Eye Catch insert', 'vkExUnit'); ?></strong>
			</td>
			<td class='column-description desc'>
				<div class='plugin-description'>
					<p><?php _e('Display Eye Catch image at before content.', 'vkExUnit'); ?></p>
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