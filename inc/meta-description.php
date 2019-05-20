<?php
/**
 * VkExUnit meta_discription.php
 * Set meta tag of description for single page each
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    26/Jun/2015
 */

// Public post type auto support
$postTypes = get_post_types( array( 'public' => true ) );

foreach ( $postTypes as $postType ) {
	add_post_type_support( $postType, 'excerpt' );
} // foreach ($postTypes as $postType) {

function vkExUnit_description_options_init() {
	vkExUnit_register_setting(
		__( 'Meta Description', 'vk-all-in-one-expansion-unit' ),    // tab label.
		'vkExUnit_description_options',          // name attr
		false,                                   // sanitaise function name
		'vkExUnit_add_description_options_page'  // setting_page function name
	);
}
add_action( 'veu_package_init', 'vkExUnit_description_options_init' );


function vkExUnit_add_description_options_page() {
?>
<h3><?php _e( 'Meta Description', 'vk-all-in-one-expansion-unit' ); ?></h3>
<div id="meta_description" class="sectionBox">
<table class="form-table">
<tr><th><?php _e( 'Meta Description', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>

<?php _e( 'What you have to complete the "excerpt" column of the edit screen of each page will be reflected in the description of the meta tag.', 'vk-all-in-one-expansion-unit' ); ?><br/>
<?php _e( 'Description of meta tags in the search results screen of search sites such as Google, will be Displayed, such as the bottom of the site title. If the excerpt column is blank, is 240 characters than text beginning of a sentence has become a specification that is applied as a description.', 'vk-all-in-one-expansion-unit' ); ?><br/>
<?php _e( 'The meta description of the top page is subject to the catchphrase of the site. However, its contents will be reflected if the excerpt is entered in fixed page that was set on the top page.', 'vk-all-in-one-expansion-unit' ); ?><br/>
* <?php _e( 'If "excerpt" column is not found, Click "Display Option" of page top at each article edit page, and check the expert column display.', 'vk-all-in-one-expansion-unit' ); ?><br/>
</td></tr>
</table>
</div>
<?php
}

/*
  head_description
/*-------------------------------------------*/
add_filter( 'wp_head', 'vkExUnit_render_HeadDescription', 5 );
function vkExUnit_render_HeadDescription() {
	echo '<meta name="description" content="' . esc_attr( vk_get_page_description() ) . '" />';
}
