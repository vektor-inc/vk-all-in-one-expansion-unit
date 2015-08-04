<h3><?php _e('call to action', 'vkExUnit'); ?></h3>
<div id="on_setting" class="sectionBox">
<?php _e('The selected CTA post type of article will be displayed after the content.', 'vkExUnit'); ?><br/>
<?php _e('If you change this setting in each article will be given priority there is.', 'vkExUnit'); ?><br/>
<table class="form-table">
<?php while( list($type, $value) = each( $options ) ): ?>
<tr><th><label ><?php echo $type; ?></label></th>
<td><select name="vkExUnit_cta_settings[<?php echo $type; ?>]" id="vkExUnit_cta_settings">
<?php foreach($ctas as $cta): ?>
    <option value="<?php echo $cta['key'] ?>" <?php echo($value == $cta['key'])? 'selected':''; ?> ><?php echo $cta['label'] ?></option>
<?php endforeach; ?>
</select></td></tr>
<?php endwhile;?>
</table>
<?php submit_button(); ?>
</div>