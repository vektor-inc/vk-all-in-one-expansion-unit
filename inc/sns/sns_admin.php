<h3><?php echo __( 'SNS Settings' ); ?></h3>
<?php
	$options = veu_get_sns_options();

/*
  SNS
/*-------------------------------------------*/
?>
<div id="snsSetting" class="sectionBox">

<!-- OGP hidden -->
<table class="form-table">
<tr>
<th><?php _e( 'Post title custom for SNS', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><label>
<input type="checkbox" name="vkExUnit_sns_options[snsTitle_use_only_postTitle]" id="snsTitle_use_only_postTitle" value="true" <?php echo ( $options['snsTitle_use_only_postTitle'] ) ? 'checked' : ''; ?> /><?php _e( 'For SNS title be composed by post title only.', 'vk-all-in-one-expansion-unit' ); ?></label>
</td>
</tr>
<tr>
<th><?php _e( 'facebook application ID', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbAppId]" id="fbAppId" value="<?php echo esc_attr( $options['fbAppId'] ); ?>" /><br />
<span>[ <a href="https://developers.facebook.com/apps" target="_blank">&raquo; <?php _e( 'I will check and get the application ID', 'vk-all-in-one-expansion-unit' ); ?></a> ]</span><br />
<?php _e( '* If an application ID is not specified, neither a Like button nor the comment field displays and operates correctly.', 'vk-all-in-one-expansion-unit' ); ?><br />
<?php _e( 'Please search for terms as [get Facebook application ID] If you do not know much about how to get application ID for Facebook.', 'vk-all-in-one-expansion-unit' ); ?>
</td>
</tr>
<tr>
<th><?php _e( 'facebook page URL', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbPageUrl]" id="fbPageUrl" value="<?php echo esc_url( $options['fbPageUrl'] ); ?>" /></td>
</tr>
</tr>
<tr>
<th><?php _e( 'facebook Access Token', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbAccessToken]" id="fbAccessToken" value="<?php echo esc_attr( $options['fbAccessToken'] ); ?>" /><br />
<span>[ <a href="https://developers.facebook.com/docs/facebook-login/access-tokens?locale=<?php _e( 'en_US', 'vk-all-in-one-expansion-unit' ); ?>" > &raquo; <?php _e( 'Access Tokens', 'vk-all-in-one-expansion-unit' ); ?></a>]</span><br/>
<?php _e( 'If you need show share counts in SNS buttons. You need get Access Token in facebook developers.', 'vk-all-in-one-expansion-unit' ); ?>
</td>
</tr>
<!-- OGP -->
<tr>
<th><?php _e( 'OG default image', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>
<p>
<?php _e( 'If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.', 'vk-all-in-one-expansion-unit' ); ?><br />
<?php _e( 'If a featured image is specified for the page, it takes precedence.', 'vk-all-in-one-expansion-unit' ); ?><br />
</p>
<input type="text" name="vkExUnit_sns_options[ogImage]" id="ogImage" value="<?php echo esc_attr( $options['ogImage'] ); ?>" />
<button id="media_src_ogImage" class="media_btn button"><?php _e( 'Select an image', 'vk-all-in-one-expansion-unit' ); ?></button><br />
<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?>https://www.vektor-inc.co.jp/images/ogImage.png</span><br />
<?php _e( '* Picture sizes are 1280x720 pixels or more and picture ratio 16:9 is recommended.', 'vk-all-in-one-expansion-unit' ); ?>
</td>
</tr>
<tr>
<th><?php _e( 'X ID', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>@<input type="text" name="vkExUnit_sns_options[twitterId]" id="twitterId" value="<?php echo esc_attr( $options['twitterId'] ); ?>" /></td>
</tr>

<tr>
<th><?php _e( 'OG tags', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><label>
<input type="checkbox" name="vkExUnit_sns_options[enableOGTags]" id="enableOGTags" value="true" <?php echo ( $options['enableOGTags'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the OG tags', 'vk-all-in-one-expansion-unit' ); ?></label>
<p><?php _e( 'If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vk-all-in-one-expansion-unit' ); ?></p>
</td>
</tr>

<tr>
<th><?php _e( 'X Card tags', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><label>
<input type="checkbox" name="vkExUnit_sns_options[enableTwitterCardTags]" id="enableTwitterCardTags" value="true" <?php echo ( $options['enableTwitterCardTags'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the X Card tags', 'vk-all-in-one-expansion-unit' ); ?></label>
</td>
</tr>

<?php
/**********************************************************
 * SNS Buttons
 */
?>

<tr>
<th><label for="enableSnsBtns"><?php _e( 'Social bookmark buttons', 'vk-all-in-one-expansion-unit' ); ?></label></th>
<td><label><input type="checkbox" name="vkExUnit_sns_options[enableSnsBtns]" id="enableSnsBtns" value="true" <?php echo ( $options['enableSnsBtns'] ) ? 'checked' : ''; ?> /><?php _e( 'Automatic insertion', 'vk-all-in-one-expansion-unit' ); ?></label>
<p><?php _e( 'Automatically insert social bookmarks (share buttons and tweet buttons) into the body content field or specified action hooks.', 'vk-all-in-one-expansion-unit' ); ?></p>
<dl>
<dt><?php _e( 'Exclude Post Types', 'vk-all-in-one-expansion-unit' ); ?></dt>
<dd>
<?php
$args = array(
	'name'    => 'vkExUnit_sns_options[snsBtn_exclude_post_types]',
	'checked' => $options['snsBtn_exclude_post_types'],
);
vk_the_post_type_check_list( $args );
?>
</dd>
</dl>

<dl>
<dt><?php _e( 'Exclude Post ID', 'vk-all-in-one-expansion-unit' ); ?></dt>
<dd>
<input type="text" id="snsBtn_ignorePosts" name="vkExUnit_sns_options[snsBtn_ignorePosts]" value="
<?php
if ( isset( $options['snsBtn_ignorePosts'] ) ) {
	echo esc_attr( $options['snsBtn_ignorePosts'] );}
?>
" />
<br/>
<?php
_e( 'if you need filtering by post_ID, add the ignore post_ID separate by ",".', 'vk-all-in-one-expansion-unit' );
echo '<br/>';
_e( 'if empty this area, I will do not filtering.', 'vk-all-in-one-expansion-unit' );
echo '<br/>';
_e( 'example', 'vk-all-in-one-expansion-unit' );
?>
  (12,31,553)
</dd>
</dl>
</td>
</tr>

<tr>
<th><label><?php _e( 'Share button for display', 'vk-all-in-one-expansion-unit' ); ?></label></th>
<td><label></label>
<ul class="no-style">
<li><label><input type="checkbox" name="vkExUnit_sns_options[useFacebook]" value="true"
<?php
if ( $options['useFacebook'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Facebook', 'vk-all-in-one-expansion-unit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useTwitter]" value="true"
<?php
if ( $options['useTwitter'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'X', 'vk-all-in-one-expansion-unit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useHatena]" value="true"
<?php
if ( $options['useHatena'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Hatena', 'vk-all-in-one-expansion-unit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[usePocket]" value="true"
<?php
if ( $options['usePocket'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Pocket', 'vk-all-in-one-expansion-unit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useLine]" value="true"
<?php
if ( $options['useLine'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'LINE (mobile only)', 'vk-all-in-one-expansion-unit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useCopy]" value="true"
<?php
if ( $options['useCopy'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Copy', 'vk-all-in-one-expansion-unit' ); ?></label></li>
</ul>
</td>
</tr>

<tr>
<th><label><?php _e( 'Share button display Position', 'vk-all-in-one-expansion-unit' ); ?></label></th>
<td><label></label>
<ul class="no-style">
<li><label><input type="checkbox" name="vkExUnit_sns_options[snsBtn_position][before]" value="true"
<?php
if ( ! empty( $options['snsBtn_position']['before'] ) ) {
	echo 'checked';}
?>
 /> <?php _e( 'Before content', 'vk-all-in-one-expansion-unit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[snsBtn_position][after]" value="true"
<?php
if ( ! empty( $options['snsBtn_position']['after'] ) ) {
	echo 'checked';}
?>
 /> <?php _e( 'After content', 'vk-all-in-one-expansion-unit' ); ?></label></li>
</ul>
</td>
</tr>
<tr>
<th><?php _e( 'Entry Count', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>
	<label><input type="radio" name="vkExUnit_sns_options[entry_count]" value="disable" 
	<?php
	if ( $options['entry_count'] == 'disable' ) {
		echo 'checked';}
	?>
	 /><?php _e( 'Disable', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
	<label><input type="radio" name="vkExUnit_sns_options[entry_count]" value="get" 
	<?php
	if ( $options['entry_count'] == 'get' ) {
		echo 'checked';}
	?>
	 /><?php _e( 'GET (Default)', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
	<label><input type="radio" name="vkExUnit_sns_options[entry_count]" value="post" 
	<?php
	if ( $options['entry_count'] == 'post' ) {
		echo 'checked';}
	?>
	 /><?php _e( 'POST', 'vk-all-in-one-expansion-unit' ); ?></label>
	<p><?php _e( '* manage entry count Api. change to \'POST\' if fail entry count. (POST mode is can\'t use cache)', 'vk-all-in-one-expansion-unit' ); ?></p>
</td>
</tr>

<tr>
	<th><label ><?php _e( 'Output action hook (optional)', 'vk-all-in-one-expansion-unit' ); ?></label></th>
<td>
<p>
<?php _e( 'By default, it is output at the bottom of the content.', 'vk-all-in-one-expansion-unit' ); ?><br>
<?php _e( 'If you want to change the location of share buttons, please enter the action hook name.', 'vk-all-in-one-expansion-unit' ); ?><br>
<?php _e( 'If you want to multiple display that, input action hook name separated by line breaks.', 'vk-all-in-one-expansion-unit' ); ?><br>
<?php _e( 'Ex) lightning_comment_before', 'vk-all-in-one-expansion-unit' ); ?>
</p>	
<textarea name="vkExUnit_sns_options[hook_point]" id="hook_point" style="width:100%;" rows="2"><?php echo esc_html( $options['hook_point'] ); ?></textarea>
</td>
</tr>

<tr>
<th><label for="enableFollowMe"><?php _e( 'Follow me box', 'vk-all-in-one-expansion-unit' ); ?></label></th>
<td><label><input type="checkbox" name="vkExUnit_sns_options[enableFollowMe]" id="enableFollowMe" value="true" <?php echo ( $options['enableFollowMe'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the Follow me box', 'vk-all-in-one-expansion-unit' ); ?></label>
<dl>
<dt><?php _e( 'Follow me box title', 'vk-all-in-one-expansion-unit' ); ?></dt>
<dd><input type="text" name="vkExUnit_sns_options[followMe_title]" id="followMe_title" value="<?php echo esc_attr( $options['followMe_title'] ); ?>" /></dd>
</dl>
</td>
</tr>

</table>

<?php submit_button(); ?>
</div>
