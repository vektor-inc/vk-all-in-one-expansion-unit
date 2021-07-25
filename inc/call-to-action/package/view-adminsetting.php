<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

/*
Main setting Page
*/
global $vk_call_to_action_textdomain;
?>

<h3><?php _e( 'Call To Action', $vk_call_to_action_textdomain ); ?></h3>

<div id="on_setting" class="sectionBox">
<p>
<?php _e( 'Display the CTA at the end of the post content.', $vk_call_to_action_textdomain ); ?><br/>
<?php _e( 'The CTA stands for "Call to action" and this is the area that prompts the user behavior.', $vk_call_to_action_textdomain ); ?><br/>
<?php _e( 'As an example, text message and a link button for induction to the free sample download page.', $vk_call_to_action_textdomain ); ?></p>

<h4><?php _e( 'How to use', $vk_call_to_action_textdomain ); ?></h4>

<ol>
<li><?php _e( 'You register the contents on "CTA" that you want to display to bottom of the content.', $vk_call_to_action_textdomain ); ?></li>
<li><?php _e( 'Choose the CTA to be displayed from the following.', $vk_call_to_action_textdomain ); ?></li>
<li><?php _e( 'If you want to switch the CTA to be displayed on each post, please set in the details page of such posting.', $vk_call_to_action_textdomain ); ?></li>
</ol>

<a href="<?php echo admin_url( 'edit.php?post_type=cta' ); ?>" class="button button-default" target="_blank"><?php _e( 'Show CTA index page', $vk_call_to_action_textdomain ); ?></a>

<table class="form-table">
<?php foreach ( $options as $type => $value ) : ?>
<tr><th><label ><?php echo get_post_type_object( $type )->label; ?></label></th>
<td><select name="vkExUnit_cta_settings[<?php echo $type; ?>]" id="vkExUnit_cta_settings">
	<?php foreach ( $ctas as $cta ) : ?>
	<option value="<?php echo $cta['key']; ?>" <?php echo( $value == $cta['key'] ) ? 'selected' : ''; ?> ><?php echo $cta['label']; ?></option>
<?php endforeach; ?>
</select>
　<a href="<?php echo admin_url( 'edit.php?post_type=' . $type ); ?>" class="button button-default" target="_blank"><?php _e( 'Show index page', $vk_call_to_action_textdomain ); ?></a>
</td></tr>
<?php endforeach; ?>
</table>

<hr>
<?php
$options       = get_option( 'vkExUnit_cta_settings' );
$options_value = '';
if ( isset( $options['hook_point'] ) ) {
	$options_value = $options['hook_point'];
}
?>
<table class="form-table">
<tr><th><label ><?php _e( 'Output action hook (optional)', $vk_call_to_action_textdomain ); ?></label></th>
<td>
<p>
<?php _e( 'By default, it is output at the bottom of the content.', $vk_call_to_action_textdomain ); ?><br>
<?php _e( 'If you want to change the location of any action hook, enter the action hook name.', $vk_call_to_action_textdomain ); ?><br>
<?php _e( 'Ex) lightning_site_footer_before', $vk_call_to_action_textdomain ); ?>
</p>	
<input type="text" name="vkExUnit_cta_settings[hook_point]" id="hook_point" value="<?php echo esc_attr( $options_value ); ?>" style="width:100%;" />
</td></tr>
</table>

<?php submit_button(); ?>
</div>
