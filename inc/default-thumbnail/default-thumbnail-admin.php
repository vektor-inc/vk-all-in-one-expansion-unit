<?php
/*
  Default Thumbnail
/*-------------------------------------------*/
$options = get_option( 'veu_defualt_thumbnail' );
?>
<h3><?php echo __( 'Default Thumbnail', 'vk-all-in-one-expansion-unit' ); ?></h3>
<div id="defaultThumbnailSetting" class="sectionBox">

<table class="form-table">
<tr>
<th><?php _e( 'Default Thumbnail Image', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>
<?php
// 現在保存されている画像idを取得して表示
$image = null;
if ( isset( $options['default_thumbnail_image'] ) && is_numeric( $options['default_thumbnail_image'] ) ) {
	$image = wp_get_attachment_image_src( $options['default_thumbnail_image'], 'large' );
}
?>
<div class="_display" style="height:auto">
	<?php if ( $image ) : ?>
		<img src="<?php echo $image[0]; ?>" style="width:200px;height:auto;" />
	<?php endif; ?>
</div>

<button
	class="button button-default button-block"
	style="display:block;width:200px;text-align: center; margin:4px 0;"
	onclick="javascript:veu_default_image_additional(this);return false;"
>
	<?php _e( 'Set image', 'vk-all-in-one-expansion-unit' ); ?>
</button>

<button
	class="button button-default button-block"
	style="display:block;width:200px;text-align: center; margin:4px 0;"
	onclick="javascript:veu_default_image_delete(this);return false;"
>
	<?php _e( 'Delete image', 'vk-all-in-one-expansion-unit' ); ?>
</button>

<?php
$default_thumbnail_image = '';
if ( ! empty( $options['default_thumbnail_image'] ) ) {
	$default_thumbnail_image = $options['default_thumbnail_image'];
}
?>
<input type="hidden" class="__id" name="veu_defualt_thumbnail[default_thumbnail_image]" value="<?php echo esc_attr( $default_thumbnail_image ); ?>" />

<script type="text/javascript">
if(veu_default_image_additional === undefined){
	var veu_default_image_additional = function(e){
		var d=jQuery(e).parent().children("._display");
		var w=jQuery(e).parent().children('.__id')[0];
		var u=wp.media({library:{type:'image'},multiple:false}).on('select', function(e){
			u.state().get('selection').each(function(f){
				// もともと表示されてた img タグを削除
				d.children().remove();
				// 新しく画像タグを挿入
				d.append(jQuery('<img style="width:200px;mheight:auto">').attr('src',f.toJSON().url));
				jQuery(w).val(f.toJSON().id).change();
			});
		});
		u.open();
	};
}
if(veu_default_image_delete === undefined){
	var veu_default_image_delete = function(e){
		var d=jQuery(e).parent().children("._display");
		var w=jQuery(e).parent().children('.__id')[0];
		d.children().remove();
		jQuery(w).val("").change();
	};
}
</script>
</td>
</tr>
</table>

<?php submit_button(); ?>
</div>
