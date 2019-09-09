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
add_action( 'veu_package_init', 'veu_sitemap_set_main_setting' );

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
function veu_sitemap_options_validate( $input ) {
	$output = $defaults = veu_get_sitemap_options_default();

	$paras = array( 'excludeId', 'excludePostTypes' );

	foreach ( $paras as $key => $value ) {
		if ( isset( $input[ $value ] ) ) {
			if ( is_array( $input[ $value ] ) ) {
				foreach ( $input[ $value ] as $post_typ => $post_type_boolean ) {
					$output[ $value ][ $post_typ ] = esc_html( $post_type_boolean );
				}
			} else {
				$output[ $value ] = ( isset( $input[ $value ] ) ) ? esc_html( $input[ $value ] ) : '';
			}
		} // if ( isset( $input[ $value ] ) {
	} // foreach ( $paras as $key => $value ) {
	return apply_filters( 'veu_sitemap_options_validate', $output );
}


function veu_add_sitemap_options_page() {
	$options = veu_get_sitemap_options();

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
	<tr>
	<th><?php _e( 'Exclude post type Settings', 'vk-all-in-one-expansion-unit' ); ?></th>
	<td>
			<?php
			$args = array(
				'name'               => 'vkExUnit_sitemap_options[excludePostTypes]',
				'checked'            => $options['excludePostTypes'],
				'exclude_post_types' => array( 'page', 'attachment' ),
			);
			vk_the_post_type_check_list( $args );
			?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
	</div>
	<?php
}
