<?php
    $_enable_ids = VEU_Widget_Controll::enable_widget_ids();
?>

<h2>Table Enablation</h2>
<table class="wp-list-table widefat plugins" style="width:auto;">
    <thead>
        <tr>
            <th scope='col' id='cb' class='manage-column column-cb check-column'><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select all', 'vk-all-in-one-expansion-unit' ); ?></label><input id="cb-select-all-1" type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name'><?php _e( 'Function', 'vk-all-in-one-expansion-unit' ); ?></th><th scope='col' id='description' class='manage-column column-description'><?php _e( 'Description', 'vk-all-in-one-expansion-unit' ); ?></th>
        </tr>
    </thead>

    <tbody id="the-list">
        <input type="checkbox" name="vkExUnit_common_options[enable_widget_idas]" value="aaaa" />
        <?php foreach(vew_widget_packages() as $package) : ?>
        <tr>
            <td><input type="checkbox" name="vkExUnit_common_options[enable_widgets][]" value="<?php echo $package['id']; ?>" id="vew_input_<?php echo $package['id']; ?>" <?php if ( in_array($package['id'], $_enable_ids) ) { echo 'checked'; } ?> /></td>
            <td><label for="vew_input_<?php echo $package['id']; ?>" ><?php echo $package['name']; ?></label></td>
            <td><?php echo $package['description'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br/>
