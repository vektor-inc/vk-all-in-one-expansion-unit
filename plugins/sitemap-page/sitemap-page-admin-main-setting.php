<?php
/*-------------------------------------------*/
/*  Main setting
/*-------------------------------------------*/

function veu_sitemap_set_main_setting() {
	vkExUnit_register_setting(
		__( 'HTML Sitemap', 'vk-all-in-one-expansion-unit' ),
		'vkExUnit_sitemap_options',
		'veu_sitemap_options_validate',
		'veu_add_sitemap_options_page'
	);
}
add_action( 'vkExUnit_package_init', 'veu_sitemap_set_main_setting' );

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
function veu_sitemap_options_validate( $input ) {
	$output = $defaults = veu_get_sitemap_options_default();

	$paras = array( 'excludeId' );

	foreach ( $paras as $key => $value ) {
		$output[ $value ] = ( isset( $input[ $value ] ) ) ? $input[ $value ] : '';
	}
	return apply_filters( 'veu_sitemap_options_validate', $output, $input, $defaults );
}


function veu_add_sitemap_options_page() {
	$options = veu_get_sitemap_options();
	// $options_default = veu_get_sns_options_default();
	/*-------------------------------------------*/
	/*  sitemap page
	/*-------------------------------------------*/
	?>
	<div id="sitemapSetting" class="sectionBox">
	<h3><?php _e( 'HTML Sitemap Settings', 'vk-all-in-one-expansion-unit' ); ?></h3>
	<table class="form-table">
	<!-- sitemap -->
	<tr>
	<th><?php _e( 'Exclude page Settings', 'vk-all-in-one-expansion-unit' ); ?></th>
	<td>
	<?php _e( 'Input you want to exclude page id.', 'vk-all-in-one-expansion-unit' ); ?><br />
	<p><input type="text" name="vkExUnit_sitemap_options[excludeId]" id="excludeId" value="<?php echo esc_attr( $options['excludeId'] ); ?>" style="width:50%;" /></p>
	<?php _e( '* Please enter separated by ","(commas) if there is more than one page ID that you want to exclude.', 'vk-all-in-one-expansion-unit' ); ?>
	</td>
	</tr>
	</table>
	<?php submit_button(); ?>
	</div>
	<?php
}
