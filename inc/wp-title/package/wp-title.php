<?php
// WordPress -> 4.3
add_filter( 'wp_title', 'vkExUnit_get_wp_head_title', 11 );
// WordPress 4.4 ->
add_filter( 'pre_get_document_title', 'vkExUnit_get_wp_head_title', 11 );

/**
 * ExUnitの機能管理パッケージに登録
 *
 * @return void
 */
function vkExUnit_wp_title_init() {
	$tab_label         = __( '&lt;title&gt; tag setting', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_wp_title';
	$sanitize_callback = 'vkExUnit_wp_title_validate';
	$render_page       = 'vkExUnit_add_wp_title_page';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'vkExUnit_wp_title_init' );


/*********************************************
 * Head title
 */

function vkExUnit_get_wp_head_title() {
	global $wp_query;
	$post  = $wp_query->get_queried_object();
	$sep   = ' | ';
	$sep   = apply_filters( 'vkExUnit_get_wp_head_title_sep', $sep );
	$title = '';
	// Meta box から指定がある場合のタイトル整形（最優先）
	if ( is_singular() || ( is_front_page() && 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) ) {
		$post_meta = get_post_meta( $post->ID, 'veu_head_title', true );
		if ( ! empty( $post_meta['title'] ) ) {
			$title = $post_meta['title'];
			if ( ! empty( $post_meta['add_site_title'] ) ) {
				$title .= $sep . get_bloginfo( 'name' );
			}
		}
	}

	if ( ! $title ) {

		if ( is_front_page() ) {
			$options = vkExUnit_get_wp_title_options();
			if ( empty( $options['extend_frontTitle'] ) ) {
				$title = get_bloginfo( 'name' ) . $sep . get_bloginfo( 'description' );
			} else {
				$title = $options['extend_frontTitle'];
			}
		} elseif ( is_home() && ! is_front_page() ) {
			$title = vkExUnit_get_the_archive_title() . $sep . get_bloginfo( 'name' );
		} elseif ( is_archive() ) {
			$title = vkExUnit_get_the_archive_title() . $sep . get_bloginfo( 'name' );
			// Page
		} elseif ( is_singular() ) {
			$post_meta = get_post_meta( $post->ID, 'veu_head_title', true );
			if ( ! empty( $post_meta['title'] ) ) {
				$title = $post_meta['title'];
				if ( ! empty( $post_meta['add_site_title'] ) ) {
					$title .= $sep . get_bloginfo( 'name' );
				}
			} elseif ( is_page() ) {
				// Sub Pages
				if ( $post->post_parent ) {
					if ( $post->ancestors ) {
						foreach ( $post->ancestors as $post_anc_id ) {
							$post_id = $post_anc_id;
						}
					} else {
						$post_id = $post->ID;
					}
					$title = get_the_title() . $sep . get_the_title( $post_id ) . $sep . get_bloginfo( 'name' );
					// Not Sub Pages
				} else {
					$title = get_the_title() . $sep . get_bloginfo( 'name' );
				}
			} else {
				$title = get_the_title() . $sep . get_bloginfo( 'name' );
			}
			// Search
		} elseif ( is_search() ) {
			if ( get_search_query() ) {
				$title = sprintf( __( 'Search Results for : %s', 'vk-all-in-one-expansion-unit' ), get_search_query() ) . $sep . get_bloginfo( 'name' );
			} else {
				$title = sprintf( __( 'Search Results', 'vk-all-in-one-expansion-unit' ), get_search_query() ) . $sep . get_bloginfo( 'name' );
			}
			// 404
		} elseif ( is_404() ) {
			$title = __( 'Not found', 'vk-all-in-one-expansion-unit' ) . $sep . get_bloginfo( 'name' );
			// Other
		} else {
			$title = get_bloginfo( 'name' );
		}
	}

	// Add Page numner.
	global $paged;
	if ( $paged >= 2 ) {
		$title = '[' . sprintf( __( 'Page of %s', 'vk-all-in-one-expansion-unit' ), $paged ) . '] ' . $title;
	}

	$title = apply_filters( 'vkExUnit_get_wp_head_title', $title );
	// Remove Tags(ex:<i>) & return
	return strip_tags( $title );
}

function vkExUnit_add_wp_title_page() {
	$options = vkExUnit_get_wp_title_options();
	?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e( '&lt;title&gt; tag setting', 'vk-all-in-one-expansion-unit' ); ?></h3>
<p>
	<?php
	$sitetitle_link = '<a href="' . get_admin_url() . 'options-general.php" target="_blank">' . __( 'title of the site', 'vk-all-in-one-expansion-unit' ) . '</a>';
	printf( __( 'Normally "%1$s" is placed in the title tags of all the pages.', 'vk-all-in-one-expansion-unit' ), $sitetitle_link );
	?>
<br />
	<?php printf( __( 'For example, it appears in the form of <br />&lt;title&gt;page title | %1$s&lt;/title&gt;<br /> if using a static page.', 'vk-all-in-one-expansion-unit' ), $sitetitle_link ); ?><br />
</p>
<table class="form-table">
	<tr>
		<th><?php _e( 'Homepage', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td>
		<p>
		<?php
		$tagline_link = '<a href="' . get_admin_url() . 'options-general.php" target="_blank">' . __( 'Tagline', 'vk-all-in-one-expansion-unit' ) . '</a>';
		printf( __( 'In the top page will be output usually in the form of <br />&lt;title&gt;%1$s | %2$s&lt;/title&gt;', 'vk-all-in-one-expansion-unit' ), $sitetitle_link, $tagline_link );
		?>
		<br />
		<?php _e( 'However, it may be too long in the above format. If the input to the input field of the following, its contents will be reflected.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>

		<input type="text" name="vkExUnit_wp_title[extend_frontTitle]" value="<?php echo esc_attr( $options['extend_frontTitle'] ); ?>" />
		<?php
		$page_on_front = intval( get_option( 'page_on_front' ) );
		if ( 'page' === get_option( 'show_on_front' ) && $page_on_front ) {
			$edit_url = get_edit_post_link( $page_on_front );
			?>
			<p>* 
				<?php
				$edit_link = '<a href="' . $edit_url . '" target="_blank" rel="noopener noreferrer">' . __( 'Edit screen of the page specified as the front page', 'vk-all-in-one-expansion-unit' ) . '</a>';
				printf( __( 'If you specify the content of the title tag in %s, that will take precedence.', 'vk-all-in-one-expansion-unit' ), $edit_link );
				?>
			</p>
		<?php } ?>
		</td>
	</tr>

	<tr>
		<th><?php _e( 'Page / Posts', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td>
		<p>
		<?php _e( 'Title tags for pages and post can be specified from the VK all in One Expansion Unit Metabox under the content edit area of each edit screen.', 'vk-all-in-one-expansion-unit' ); ?></p>
			<?php if ( get_locale() === 'ja' ) { ?>
				<img style="max-width:100%;border:1px solid #ccc;" src="<?php echo esc_url( VEU_DIRECTORY_URI ); ?>/inc/wp-title/package/images/title-setting-from-page.png" alt="" />
			<?php } ?>

		</td>
	</tr>
</table>
	<?php submit_button(); ?>
</div>
	<?php
}

function vkExUnit_get_wp_title_options() {
	$options = get_option( 'vkExUnit_wp_title', array() );
	$options = wp_parse_args( $options, vkExUnit_get_wp_title_default() );
	return $options;
}

function vkExUnit_get_wp_title_default() {
	$default_options = array(
		'extend_frontTitle' => '',
	);
	return apply_filters( 'vkExUnit_wp_title_default', $default_options ); // phpcs:ignore
}

function vkExUnit_wp_title_validate( $input ) {
	$output                      = array();
	$output['extend_frontTitle'] = stripslashes( htmlspecialchars( $input['extend_frontTitle'] ) );
	return $output;
}
