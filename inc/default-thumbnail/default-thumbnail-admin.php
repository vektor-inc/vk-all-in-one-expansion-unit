<h3><?php echo __( 'Default Thumbnail', 'vk-all-in-one-expansion-unit' ); ?></h3>
<?php
    $options = get_option( 'veu_defualt_thumbnail' );

/*
  SNS
/*-------------------------------------------*/
?>
<div id="snsSetting" class="sectionBox">

<!-- OGP hidden -->
<table class="form-table">
<!-- OGP -->
<tr>
<th><?php _e( 'Default Thumbnail Image', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><?php _e( '', 'vk-all-in-one-expansion-unit' ); ?><br />
<input type="text" name="veu_defualt_thumbnail[default_thumbnail_image]" id="default_thumbnail_image" value="<?php echo esc_attr( $options['default_thumbnail_image'] ); ?>" />
<button id="media_src_default_thumbnail_image" class="media_btn button"><?php _e( 'Select an image', 'vk-all-in-one-expansion-unit' ); ?></button><br />
<span><?php _e( 'ex) ', 'vk-all-in-one-expansion-unit' ); ?>https://www.vektor-inc.co.jp/images/ogImage.png</span><br />
<?php _e( '* Picture sizes are 1280x720 pixels or more and picture ratio 16:9 is recommended.', 'vk-all-in-one-expansion-unit' ); ?>
</td>
</tr>
</table>

<?php submit_button(); ?>
</div>
