<?php
/*-------------------------------------------*/
/*  Main setting
/*-------------------------------------------*/

function veu_main_setting_add_common() {
	vkExUnit_register_setting(
		__( 'Common setting', 'vk-all-in-one-expansion-unit' ),
		'vkExUnit_common_options',
		'',
		'veu_add_common_setting_page'
	);
}
add_action( 'vkExUnit_package_init', 'veu_main_setting_add_common' );

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
// function veu_sitemap_options_validate( $input ) {
// 	$output = $defaults = veu_get_sitemap_options_default();
//
// 	$paras = array( 'excludeId' );
//
// 	foreach ( $paras as $key => $value ) {
// 		$output[ $value ] = ( isset( $input[ $value ] ) ) ? $input[ $value ] : '';
// 	}
// 	return apply_filters( 'veu_sitemap_options_validate', $output, $input, $defaults );
// }


function veu_add_common_setting_page() {
	$options = get_option( 'vkExUnit_common_options' );
	// $options_default = veu_get_sns_options_default();
	/*-------------------------------------------*/
	/*  sitemap page
	/*-------------------------------------------*/
	?>
	<div id="sitemapSetting" class="sectionBox">
	<h3><?php _e( 'Common Settings', 'vk-all-in-one-expansion-unit' ); ?></h3>
	<table class="form-table">
	<!-- sitemap -->
	<tr>
	<th><?php _e( 'Post edit page metabox settings', 'vk-all-in-one-expansion-unit' ); ?></th>
	<td>
		<?php
		if ( ! empty( $options['post_metabox_individual'] ) ) {
			$checked = ' checked';
		}
			?>
	<p><label><input type="checkbox" name="vkExUnit_common_options[post_metabox_individual]" id="vkExUnit_common_options[post_metabox_individual]" value="true" /><?php _e( 'Do not combine ExUnit\'s meta box', 'vk-all-in-one-expansion-unit' ); ?></label></p>

	</td>
	</tr>
	</table>
	<?php submit_button(); ?>
	</div>
	<?php
}
