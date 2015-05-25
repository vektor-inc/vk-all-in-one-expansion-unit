<form method="post" action="options.php">
<?php
	settings_fields( 'biz_vektor_sns_options_fields' );
	$options = biz_vektor_get_sns_options();
	// $options_default = biz_vektor_get_sns_options_default();
/*-------------------------------------------*/
/*	SNS
/*-------------------------------------------*/
?>
<div id="snsSetting" class="sectionBox">
<?php // get_template_part('inc/theme-options-nav'); ?>
<h3><?php _e('Social media', 'biz-vektor'); ?></h3>
<?php _e('If you are unsure, you can leave for later.', 'biz-vektor'); ?>
<table class="form-table">
<!--
<tr>
<th>facebook</th>
<td><?php _e('If you wish to link to a personal account or a Facebook page  banner will be displayed if you enter the URL.', 'biz-vektor'); ?><br />
<input type="text" name="biz_vektor_sns_options[facebook]" id="facebook" value="<?php echo esc_attr( $options['facebook'] ); ?>" />
<span><?php _e('ex) ', 'biz-vektor') ;?>https://www.facebook.com/hidekazu.ishikawa</span>
</td>
</tr>
-->
<!-- facebook application ID -->
<tr>
<th><?php _e('facebook application ID', 'biz-vektor'); ?></th>
<td><input type="text" name="biz_vektor_sns_options[fbAppId]" id="fbAppId" value="<?php echo esc_attr( $options['fbAppId'] ); ?>" />
<span>[ <a href="https://developers.facebook.com/apps" target="_blank">&raquo; <?php _e('I will check and get the application ID', 'biz-vektor'); ?></a> ]</span><br />
<?php _e('* If an application ID is not specified, neither a Like button nor the comment field displays and operates correctly.', 'biz-vektor'); ?><br />
<?php _e('Please search for terms as [get Facebook application ID] If you do not know much about how to get application ID for Facebook.', 'biz-vektor'); ?>
</td>
</tr>
<!-- facebook user ID -->
<tr>
<th><?php _e('Facebook user ID (optional)', 'biz-vektor'); ?></th>
<td><?php _e('Please enter the Facebook user ID of the administrator.', 'biz-vektor'); ?><br />
<input type="text" name="biz_vektor_sns_options[fbAdminId]" id="fbAdminId" value="<?php echo esc_attr( $options['fbAdminId'] ); ?>" /><br />
<?php _e('* It is not the application ID of the Facebook page.', 'biz-vektor'); ?><br />
<?php _e('You can see the personal Facebook ID when you access the following url http://graph.facebook.com/(own url name(example: hidekazu.ishikawa)).', 'biz-vektor'); ?><br />
<?php _e('Please search for terms as [find facebook user ID] if you are still not sure.', 'biz-vektor'); ?>
</td>
</tr>
<!-- twitter 
<tr>
<th><?php _e('twitter account', 'biz-vektor'); ?></th>
<td><?php _e('If you would like to link to a Twitter account, banner will be displayed if you enter the account name.', 'biz-vektor'); ?><br />
@<input type="text" name="biz_vektor_sns_options[twitter]" id="twitter" value="<?php echo esc_attr( $options['twitter'] ); ?>" /><br />
<?php $twitter_widget = '<a href="'.get_admin_url().'widgets.php" target="_blank">'.__('widget', 'biz-vektor').'</a>';
printf(__('* If you prefer to use Twitter widgets etc, this can be left blank, paste the source code into a [text] %s here.', 'biz-vektor'),$twitter_widget);
?>
</td>
</tr>
-->
<!-- OGP -->
<tr>
<th><?php _e('OGP default image', 'biz-vektor'); ?></th>
<td><?php _e('If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.', 'biz-vektor'); ?><br />
<?php _e('If a featured image is specified for the page, it takes precedence.', 'biz-vektor'); ?><br />
<input type="text" name="biz_vektor_sns_options[ogpImage]" id="ogpImage" value="<?php echo esc_attr( $options['ogpImage'] ); ?>" /> 
<button id="media_ogpImage" class="media_btn"><?php _e('Select an image', 'biz-vektor'); ?></button><br />
<span><?php _e('ex) ', 'biz-vektor') ;?>http://www.vektor-inc.co.jp/images/ogpImage.png</span><br />
<?php _e('* Picture sizes are 300x300 pixels or more and picture ratio 16:9 is recommended.', 'biz-vektor'); ?>
</td>
</tr>
<!-- Social buttons -->
<tr>
<th><?php _e('Social buttons', 'biz-vektor'); ?></th>
<td><?php _e('Please check the type of page that displays the social button.', 'biz-vektor'); ?>
<ul>
<li><input type="checkbox" name="biz_vektor_sns_options[snsBtnsFront]" id="snsBtnsFront" value="false" <?php if ($options['snsBtnsFront']) {?> checked<?php } ?>> 
	<?php _ex('Home page', 'sns display', 'biz-vektor'); ?></li>
<li><input type="checkbox" name="biz_vektor_sns_options[snsBtnsPage]" id="snsBtnsPage" value="false" <?php if ($options['snsBtnsPage']) {?> checked<?php } ?>> 
	<?php _ex('Page', 'sns display', 'biz-vektor'); ?></li>
<li><input type="checkbox" name="biz_vektor_sns_options[snsBtnsPost]" id="snsBtnsPost" value="false" <?php if ($options['snsBtnsPost']) {?> checked<?php } ?>> 
	<?php echo esc_html(biz_vektor_sns_options('postLabelName')); ?> <?php _ex('Post', 'sns display', 'biz-vektor'); ?></li>
</ul>
<p><?php _e('Within the type of page that is checked, if there is a particular page you do not wish to display, enter the Page ID. If multiple pages, please separate by commas.', 'biz-vektor'); ?><br />
<input type="text" name="biz_vektor_sns_options[snsBtnsHidden]" id="ogpImage" value="<?php echo esc_attr( $options['snsBtnsHidden'] ); ?>" /><br />
<?php _e('ex) ', 'biz-vektor') ;?>1,3,7</p>
</td>
</tr>
<!-- facebook comment -->
<tr>
<th><?php _e('facebook comments box', 'biz-vektor'); ?></th>
<td><?php _e('Please check the type of the page to display Facebook comments.', 'biz-vektor'); ?>
<ul>
<li><input type="checkbox" name="biz_vektor_sns_options[fbCommentsFront]" id="fbCommentsFront" value="false" <?php if ($options['fbCommentsFront']) {?> checked<?php } ?>> 
	<?php _ex('Home page', 'sns display', 'biz-vektor'); ?></li>
<li><input type="checkbox" name="biz_vektor_sns_options[fbCommentsPage]" id="fbCommentsPage" value="false" <?php if ($options['fbCommentsPage']) {?> checked<?php } ?>> 
	<?php _ex('Page', 'sns display', 'biz-vektor'); ?></li>
<li><input type="checkbox" name="biz_vektor_sns_options[fbCommentsPost]" id="fbCommentsPost" value="false" <?php if ($options['fbCommentsPost']) {?> checked<?php } ?>> 
	<?php echo esc_html(biz_vektor_sns_options('postLabelName')); ?> <?php _ex('Post', 'sns display', 'biz-vektor'); ?></li>
<li><input type="checkbox" name="biz_vektor_sns_options[fbCommentsInfo]" id="fbCommentsInfo" value="false" <?php if ($options['fbCommentsInfo']) {?> checked<?php } ?>> 
	<?php echo esc_html(biz_vektor_sns_options('infoLabelName')); ?> <?php _ex('Post', 'sns display', 'biz-vektor'); ?></li>
</ul>
<p><?php _e('Within the type of page that is checked, if there is a particular page you do not wish to display, enter the Page ID. If multiple pages, please separate by commas.', 'biz-vektor'); ?><br />
<input type="text" name="biz_vektor_sns_options[snsBtnsHidden]" id="ogpImage" value="<?php echo esc_attr( $options['snsBtnsHidden'] ); ?>" /><br />
<?php _e('ex) ', 'biz-vektor') ;?>1,3,7</p>
</td>
</tr>
<!-- facebook LikeBox -->
<tr>
<th>facebook LikeBox</th>
<td><?php _e('If you wish to use Facebook LikeBox, please check the location.', 'biz-vektor'); ?><br />
<?php _e('* Please be sure to set Facebook application ID.', 'biz-vektor'); ?>
<ul>
<li><input type="checkbox" name="biz_vektor_sns_options[fbLikeBoxFront]" id="fbLikeBoxFront" value="checked" <?php if ($options['fbLikeBoxFront']) {?> checked<?php } ?>> 
	<?php _ex('Home page', 'sns display', 'biz-vektor'); ?></li>
<li><input type="checkbox" name="biz_vektor_sns_options[fbLikeBoxSide]" id="fbLikeBoxSide" value="checked" <?php if ($options['fbLikeBoxSide']) {?> checked<?php } ?>> 
	<?php _ex('Side bar', 'sns display', 'biz-vektor'); ?></li>
</ul>
<dl>
<dt><?php _e('URL of the Facebook page.', 'biz-vektor'); ?></dt>
<dd><input type="text" name="biz_vektor_sns_options[fbLikeBoxURL]" id="fbLikeBoxURL" value="<?php echo esc_attr( $options['fbLikeBoxURL'] ); ?>" /><br />
<span><?php _e('ex) ', 'biz-vektor') ;?>https://www.facebook.com/bizvektor</span></dd>
<dt><?php _e('Display stream', 'biz-vektor'); ?></dt>
<dd><input type="checkbox" name="biz_vektor_sns_options[fbLikeBoxStream]" id="fbLikeBoxStream" value="checked" <?php if ($options['fbLikeBoxStream']) {?> checked<?php } ?>> <?php _e('Display', 'biz-vektor'); ?></dd>
<dt><?php _e('Display faces', 'biz-vektor'); ?></dt>
<dd><input type="checkbox" name="biz_vektor_sns_options[fbLikeBoxFace]" id="fbLikeBoxFace" value="checked" <?php if ($options['fbLikeBoxFace']) {?> checked<?php } ?>> <?php _e('Display', 'biz-vektor'); ?></dd>
<dt><?php _e('Height of LikeBox', 'biz-vektor'); ?></dt>
<dd><input type="text" name="biz_vektor_sns_options[fbLikeBoxHeight]" id="fbLikeBoxHeight" value="<?php echo esc_attr( $options['fbLikeBoxHeight'] ); ?>" />
px</dd>
</dl>
</td>
</tr>
<!-- OGP hidden -->
<tr>
<th><?php _e('Do not output the OGP', 'biz-vektor'); ?></th>
<td>
<p><?php _e('If other plug-ins are used for the OGP, do not output the OGP using BizVektor.', 'biz-vektor'); ?></p>
<?php
$biz_vektor_ogpTags = array(
	'ogp_on' 	=> __('I want to output the OGP tags using BizVektor', 'biz-vektor'),
	'ogp_off' 	=> __('Do not output OGP tags using BizVektor', 'biz-vektor')
	);
foreach( $biz_vektor_ogpTags as $biz_vektor_ogpTagValue => $biz_vektor_ogpTagLavel) {
	if ( $biz_vektor_ogpTagValue == $options['ogpTagDisplay'] ) { ?>
	<label><input type="radio" name="biz_vektor_sns_options[ogpTagDisplay]" value="<?php echo $biz_vektor_ogpTagValue ?>" checked> <?php echo $biz_vektor_ogpTagLavel ?></label><br />
	<?php } else { ?>
	<label><input type="radio" name="biz_vektor_sns_options[ogpTagDisplay]" value="<?php echo $biz_vektor_ogpTagValue ?>"> <?php echo $biz_vektor_ogpTagLavel ?></label><br />
	<?php }
} ?>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
<div class="optionNav bottomNav">
<ul><li><a href="#wpwrap"><?php _e('Page top', 'biz-vektor'); ?></a></li></ul>
</div>
</form>