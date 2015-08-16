<?php
/*
Main setting Page
*/
?>
<h3><?php _e('Call To Action', 'vkExUnit'); ?></h3>

<div id="on_setting" class="sectionBox">
<p>
<?php _e('The selected CTA post type of article will be displayed after the content.', 'vkExUnit'); ?><br/>
<?php _e('If you change this setting in each article will be given priority there is.', 'vkExUnit'); ?></p>

<a href="<?php echo admin_url('edit.php?post_type=cta') ?>" class="button button-default" target="_blank"><?php _e('Show CTA index page', 'vkExUnit'); ?></a>

<table class="form-table">
<?php while( list($type, $value) = each( $options ) ): ?>
<tr><th><label ><?php echo get_post_type_object($type)->label; ?></label></th>
<td><select name="vkExUnit_cta_settings[<?php echo $type; ?>]" id="vkExUnit_cta_settings">
<?php foreach($ctas as $cta): ?>
    <option value="<?php echo $cta['key'] ?>" <?php echo($value == $cta['key'])? 'selected':''; ?> ><?php echo $cta['label'] ?></option>
<?php endforeach; ?>
</select>
　<a href="<?php echo admin_url('edit.php?post_type=' . $type) ?>" class="button button-default" target="_blank"><?php _e('Show index page', 'vkExUnit'); ?></a>
</td></tr>
<?php endwhile;?>
</table>
<?php submit_button(); ?>
</div>