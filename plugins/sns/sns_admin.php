<h3><?php echo __( 'SNS Settings' ); ?></h3>
<?php
	$options = veu_get_sns_options();
/*-------------------------------------------*/
/*  SNS
/*-------------------------------------------*/
?>
<div id="snsSetting" class="sectionBox">

<!-- OGP hidden -->
<table class="form-table">
<tr>
<th><?php _e( 'facebook application ID', 'vkExUnit' ); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbAppId]" id="fbAppId" value="<?php echo esc_attr( $options['fbAppId'] ); ?>" /><br />
<span>[ <a href="https://developers.facebook.com/apps" target="_blank">&raquo; <?php _e( 'I will check and get the application ID', 'vkExUnit' ); ?></a> ]</span><br />
<?php _e( '* If an application ID is not specified, neither a Like button nor the comment field displays and operates correctly.', 'vkExUnit' ); ?><br />
<?php _e( 'Please search for terms as [get Facebook application ID] If you do not know much about how to get application ID for Facebook.', 'vkExUnit' ); ?>
</td>
</tr>
<tr>
<th><?php _e( 'facebook page URL', 'vkExUnit' ); ?></th>
<td><input type="text" name="vkExUnit_sns_options[fbPageUrl]" id="fbPageUrl" value="<?php echo esc_url( $options['fbPageUrl'] ); ?>" /></td>
</tr>
<!-- OGP -->
<tr>
<th><?php _e( 'OG default image', 'vkExUnit' ); ?></th>
<td><?php _e( 'If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.', 'vkExUnit' ); ?><br />
<?php _e( 'If a featured image is specified for the page, it takes precedence.', 'vkExUnit' ); ?><br />
<input type="text" name="vkExUnit_sns_options[ogImage]" id="ogImage" value="<?php echo esc_attr( $options['ogImage'] ); ?>" />
<button id="media_src_ogImage" class="media_btn button"><?php _e( 'Select an image', 'vkExUnit' ); ?></button><br />
<span><?php _e( 'ex) ', 'vkExUnit' ); ?>https://www.vektor-inc.co.jp/images/ogImage.png</span><br />
<?php _e( '* Picture sizes are 1280x720 pixels or more and picture ratio 16:9 is recommended.', 'vkExUnit' ); ?>
</td>
</tr>
<tr>
<th><?php _e( 'twitter ID', 'vkExUnit' ); ?></th>
<td>@<input type="text" name="vkExUnit_sns_options[twitterId]" id="twitterId" value="<?php echo esc_attr( $options['twitterId'] ); ?>" /></td>
</tr>

<tr>
<th><?php _e( 'OG tags', 'vkExUnit' ); ?></th>
<td><label>
<input type="checkbox" name="vkExUnit_sns_options[enableOGTags]" id="enableOGTags" value="true" <?php echo ( $options['enableOGTags'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the OG tags', 'vkExUnit' ); ?></label>
<p><?php _e( 'If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vkExUnit' ); ?></p>
</td>
</tr>

<tr>
<th><?php _e( 'Twitter Card tags', 'vkExUnit' ); ?></th>
<td><label>
<input type="checkbox" name="vkExUnit_sns_options[enableTwitterCardTags]" id="enableTwitterCardTags" value="true" <?php echo ( $options['enableTwitterCardTags'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the Twitter Card tags', 'vkExUnit' ); ?></label>
</td>
</tr>

<tr>
<th><label for="enableSnsBtns"><?php _e( 'Social bookmark buttons', 'vkExUnit' ); ?></label></th>
<td><label><input type="checkbox" name="vkExUnit_sns_options[enableSnsBtns]" id="enableSnsBtns" value="true" <?php echo ( $options['enableSnsBtns'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the social bookmark buttons', 'vkExUnit' ); ?></label>

<dl>
<dt><?php _e( 'Exclude Post Types', 'vkExUnit' ); ?></dt>
<dd>
<?php
$args       = array(
	'public' => true,
);
$post_types = get_post_types( $args, 'object' );
echo '<ul>';
foreach ( $post_types as $key => $value ) {
	if ( $key != 'attachment' ) {
		$checked = ( isset( $options['snsBtn_exclude_post_types'][ $key ] ) && $options['snsBtn_exclude_post_types'][ $key ] == 'true' ) ? ' checked' : '';
		echo '<li><label>';
		echo '<input type="checkbox" name="vkExUnit_sns_options[snsBtn_exclude_post_types][' . $key . ']" id="snsBtn_exclude_post_types" value="true"' . $checked . ' />' . esc_html( $value->label );
		echo '</label></li>';
	}
}
echo '</ul>';
?>
</dd>
</dl>

<dl>
<dt><?php _e( 'Exclude Post ID', 'vkExUnit' ); ?></dt>
<dd>
<input type="text" id="snsBtn_ignorePosts" name="vkExUnit_sns_options[snsBtn_ignorePosts]" value="
<?php
if ( isset( $options['snsBtn_ignorePosts'] ) ) {
	echo $options['snsBtn_ignorePosts'];}
?>
" />
<br/>
<?php
_e( 'if you need filtering by post_ID, add the ignore post_ID separate by ",".', 'vkExUnit' );
echo '<br/>';
_e( 'if empty this area, I will do not filtering.', 'vkExUnit' );
echo '<br/>';
_e( 'example', 'vkExUnit' );
?>
  (12,31,553)
</dd>
</dl>
</td>
</tr>

<tr>
<th><label for="enableFollowMe"><?php _e( 'Follow me box', 'vkExUnit' ); ?></label></th>
<td><label><input type="checkbox" name="vkExUnit_sns_options[enableFollowMe]" id="enableFollowMe" value="true" <?php echo ( $options['enableFollowMe'] ) ? 'checked' : ''; ?> /><?php _e( 'Print the Follow me box', 'vkExUnit' ); ?></label>
<dl>
<dt><?php _e( 'Follow me box title', 'vkExUnit' ); ?></dt>
<dd><input type="text" name="vkExUnit_sns_options[followMe_title]" id="followMe_title" value="<?php echo esc_attr( $options['followMe_title'] ); ?>" /></dd>
</dl>
</td>
</tr>

<tr>
<th><label><?php _e( 'Share button for display', 'vkExUnit' ); ?></label></th>
<td><label></label>
<ul>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useFacebook]" value="true"
<?php
if ( $options['useFacebook'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Facebook', 'vkExUnit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useTwitter]" value="true"
<?php
if ( $options['useTwitter'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Twitter', 'vkExUnit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useHatena]" value="true"
<?php
if ( $options['useHatena'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Hatena', 'vkExUnit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[usePocket]" value="true"
<?php
if ( $options['usePocket'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'Pocket', 'vkExUnit' ); ?></label></li>
<li><label><input type="checkbox" name="vkExUnit_sns_options[useLine]" value="true"
<?php
if ( $options['useLine'] ) {
	echo 'checked';}
?>
 /> <?php _e( 'LINE (mobile only)', 'vkExUnit' ); ?></label></li>
</ul>
</td>
</tr>


</table>

<?php submit_button(); ?>
</div>
