<h3><?php _e('call to action', 'vkExUnit'); ?></h3>
<div id="on_setting" class="sectionBox">
<?php while( list($type, $value) = each($options['types']) ): ?>
<table class="form-table">
<th><label ><?php echo $type; ?></label></dt>
<td><select name="vkExUnit_cta_settings[types][<?php echo $type; ?>]" id="vkExUnit_cta_settings">
<?php foreach($ctas as $cta): ?>
    <option value="<?php echo $cta['key'] ?>" <?php echo($value == $cta['key'])? 'selected':''; ?> ><?php echo $cta['label'] ?></option>
<?php endforeach; ?>
</select></td>
</table>
<?php endwhile;?>
<?php submit_button(); ?>
</div>