<h2><?php echo __('SNS Settings');?></h2>
<?php
	$options = vkExUnit_get_sns_options();
/*-------------------------------------------*/
/*	SNS
/*-------------------------------------------*/
?>
<div id="snsSetting" class="sectionBox">

<!-- OGP hidden -->
<table class="form-table">
<tr>
<th><?php _e('Do not output the OG', 'vkExUnit'); ?></th>
<td>
<p><?php _e('If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vkExUnit'); ?></p>
<?php
$ogTagDisplay = (isset($options['ogTagDisplay'])) ? $options['ogTagDisplay'] : 'og_on';
$vkExUnit_ogTags = array(
	'og_on' 	=> __('Output OG tags(default)', 'vkExUnit'),
	'og_off' 	=> __('Do not output OG tags', 'vkExUnit')
	);
foreach( $vkExUnit_ogTags as $vkExUnit_ogTagValue => $vkExUnit_ogTagLavel) {
	if ( $vkExUnit_ogTagValue == $ogTagDisplay ) { ?>
	<label><input type="radio" name="vkExUnit_sns_options[ogTagDisplay]" value="<?php echo $vkExUnit_ogTagValue ?>" checked> <?php echo $vkExUnit_ogTagLavel ?></label><br />
	<?php } else { ?>
	<label><input type="radio" name="vkExUnit_sns_options[ogTagDisplay]" value="<?php echo $vkExUnit_ogTagValue ?>"> <?php echo $vkExUnit_ogTagLavel ?></label><br />
	<?php }
} ?>
</td>
</tr>
<tr>
<th><?php _e('facebook application ID', 'vkExUnit'); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbAppId]" id="fbAppId" value="<?php echo esc_attr( $options['fbAppId'] ); ?>" />
<span>[ <a href="https://developers.facebook.com/apps" target="_blank">&raquo; <?php _e('I will check and get the application ID', 'vkExUnit'); ?></a> ]</span><br />
<?php _e('* If an application ID is not specified, neither a Like button nor the comment field displays and operates correctly.', 'vkExUnit'); ?><br />
<?php _e('Please search for terms as [get Facebook application ID] If you do not know much about how to get application ID for Facebook.', 'vkExUnit'); ?>
</td>
</tr>
<tr>
<th><?php _e('facebook page URL', 'vkExUnit'); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbPageUrl]" id="fbPageUrl" value="<?php echo esc_url( $options['fbPageUrl'] ); ?>" /></td>
</tr>
<!-- OGP -->
<tr>
<th><?php _e('OG default image', 'vkExUnit'); ?></th>
<td><?php _e('If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.', 'vkExUnit'); ?><br />
<?php _e('If a featured image is specified for the page, it takes precedence.', 'vkExUnit'); ?><br />
<input type="text" name="vkExUnit_sns_options[ogImage]" id="ogImage" value="<?php echo esc_attr( $options['ogImage'] ); ?>" /> 
<button id="media_ogImage" class="media_btn button"><?php _e('Select an image', 'vkExUnit'); ?></button><br />
<span><?php _e('ex) ', 'vkExUnit') ;?>http://www.vektor-inc.co.jp/images/ogImage.png</span><br />
<?php _e('* Picture sizes are 300x300 pixels or more and picture ratio 16:9 is recommended.', 'vkExUnit'); ?>
</td>
</tr>
<tr>
<th><?php _e('twitter ID', 'vkExUnit'); ?></th>
<td><input type="text" name="vkExUnit_sns_options[twitterId]" id="twitterId" value="<?php echo esc_attr( $options['twitterId'] ); ?>" /></td>
</tr>
</table>

<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存"  /></p>
</div>