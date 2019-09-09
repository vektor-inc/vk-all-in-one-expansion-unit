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
	<th><?php _e( 'Exclude post type from the sitemap', 'vk-all-in-one-expansion-unit' ); ?></th>
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
	</tr>
	</table>
	<p><?php _e( 'If you want to do not display specific page that, you can set on that page edit screen.', 'vk-all-in-one-expansion-unit' ); ?></p>

<?php
// 以前は除外設定をここから IDを , 区切りで行っていた。
// 除外指定がもし , 区切りで保存してあった場合に固定ページ側での除外指定で自動上書きする
if ( ! empty( $options['excludeId'] ) ) {
	$excludes = esc_attr( $options['excludeId'] );
	$excludes = str_replace( '，', ',', $excludes );
	$excludes = mb_convert_kana( $excludes, 'kvrn' );
	$excludes = explode( ',', $excludes );
	foreach ( $excludes as $key => $exclude_id ) {
		update_post_meta( $exclude_id, 'sitemap_hide', true );
	}
	$options['excludeId'] = '';
	update_option( 'vkExUnit_sitemap_options', $options );
} // if ( ! empty( $options['excludeId'] ) ) {
?>
	<?php submit_button(); ?>
	</div>
	<?php
}
