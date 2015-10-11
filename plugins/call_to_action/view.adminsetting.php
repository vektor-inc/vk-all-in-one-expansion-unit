<?php
/*
Main setting Page
*/
?>
<h3><?php _e( 'Call To Action', 'vkExUnit' ); ?></h3>

<div id="on_setting" class="sectionBox">
<p>
<?php _e( 'Display the CTA at the end of the post content.', 'vkExUnit' ); ?><br/>
<?php _e( 'The CTA stands for "Call to action" and this is the area that prompts the user behavior.', 'vkExUnit' ); ?><br/>
<?php _e( 'As an example, text message and a link button for induction to the free sample download page.', 'vkExUnit' ); ?></p>

<h4><?php _e( 'How to use','vkExUnit' );?></h4>

<ol>
<li><?php _e( 'You register the contents on "CTA" that you want to display to bottom of the content.', 'vkExUnit' ); ?></li>
<li><?php _e( 'Choose the CTA to be displayed from the following.', 'vkExUnit' ); ?></li>
<li><?php _e( 'If you want to switch the CTA to be displayed on each post, please set in the details page of such posting.', 'vkExUnit' ); ?></li>
</ol>

<a href="<?php echo admin_url( 'edit.php?post_type=cta' ) ?>" class="button button-default" target="_blank"><?php _e( 'Show CTA index page', 'vkExUnit' ); ?></a>

<table class="form-table">
<?php while ( list($type, $value) = each( $options ) ) :  ?>
<tr><th><label ><?php echo get_post_type_object( $type )->label; ?></label></th>
<td><select name="vkExUnit_cta_settings[<?php echo $type; ?>]" id="vkExUnit_cta_settings">
<?php foreach ( $ctas as $cta ) :  ?>
    <option value="<?php echo $cta['key'] ?>" <?php echo($value == $cta['key'])? 'selected':''; ?> ><?php echo $cta['label'] ?></option>
<?php endforeach; ?>
</select>
　<a href="<?php echo admin_url( 'edit.php?post_type=' . $type ) ?>" class="button button-default" target="_blank"><?php _e( 'Show index page', 'vkExUnit' ); ?></a>
</td></tr>
<?php endwhile;?>
</table>
<?php submit_button(); ?>
</div>
