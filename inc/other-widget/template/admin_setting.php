<?php
	$_enable_ids = VEU_Widget_Control::enable_widget_ids();
?>

<h2><?php echo __( 'Widget Enablation', 'vk-all-in-one-expansion-unit' ); ?></h2>
<!-- ここでウィジェット設定が反映されたか判定 -->
<input type="hidden" name="vkExUnit_widget_setting" value="true">
<table id="widget_enablation" class="wp-list-table widefat plugins table-widget-enablation">
	<thead>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select all', 'vk-all-in-one-expansion-unit' ); ?></label><input id="cb-select-all-w" onclick="veuwcb()" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'><?php _e( 'Function', 'vk-all-in-one-expansion-unit' ); ?></th><th scope='col' id='description' class='manage-column column-description'><?php _e( 'Description', 'vk-all-in-one-expansion-unit' ); ?></th>
		</tr>
	</thead>

	<tbody id="the-list">
		<?php foreach ( veu_widget_packages() as $package ) : ?>
			<?php
			$class = '';
			if ( in_array( $package['id'], $_enable_ids ) ) {
				$class = ' class="active"';
			}
			?>
			<tr<?php echo $class; ?>>
			<td><input type="checkbox" name="vkExUnit_common_options[enable_widgets][]" value="<?php echo $package['id']; ?>" class="vew_enable_widget_checkbox" id="vew_widget_enable_input_<?php echo $package['id']; ?>" 
			<?php
			if ( in_array( $package['id'], $_enable_ids ) ) {
				echo 'checked'; }
			?>
			 onclick="veuwd()" /></td>
			<td><label for="vew_widget_enable_input_<?php echo $package['id']; ?>" ><?php echo $package['class']::veu_widget_name(); ?></label></td>
			<td><?php echo $package['class']::veu_widget_description(); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>

<script type="text/javascript">
let veuwcb=()=>{let a='';if(document.getElementById('cb-select-all-w').checked)a='checked';Array.prototype.forEach.call(document.getElementsByClassName('vew_enable_widget_checkbox'),(i)=>{i.checked=a});};
let veuwd=()=>{let a=true;Array.prototype.forEach.call(document.getElementsByClassName('vew_enable_widget_checkbox'),(i)=>{a=a&&i.checked});if(a)b='checked';document.getElementById('cb-select-all-w').checked=a}
</script>
<br/>
