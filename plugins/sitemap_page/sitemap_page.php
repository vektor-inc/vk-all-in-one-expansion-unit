<?php

/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
/*  insert sitemap page
/*-------------------------------------------*/
/*  admin _ meta box
/*-------------------------------------------*/


/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_sitemap_options_page() {
	require dirname( __FILE__ ) . '/sitemap_admin.php';
}
/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
function vkExUnit_sitemap_options_init() {
	if ( false === vkExUnit_get_sitemap_options() ) {
		add_option( 'vkExUnit_sitemap_options', vkExUnit_get_sitemap_options_default() ); }

	vkExUnit_register_setting(
		__( 'HTML Sitemap', 'vkExUnit' ),
		'vkExUnit_sitemap_options',
		'vkExUnit_sitemap_options_validate',
		'vkExUnit_add_sitemap_options_page'
	);
}
add_action( 'admin_init', 'vkExUnit_sitemap_options_init' );

function vkExUnit_get_sitemap_options() {
	$options	= get_option( 'vkExUnit_sitemap_options', vkExUnit_get_sitemap_options_default() );
	$options_dafault	= vkExUnit_get_sitemap_options_default();
	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = (isset( $options[ $key ] )) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_sitemap_options', $options );
}

function vkExUnit_get_sitemap_options_default() {
	$default_options = array(
		'excludeId' => '',
	);
	return apply_filters( 'vkExUnit_sitemap_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
function vkExUnit_sitemap_options_validate( $input ) {
	$output = $defaults = vkExUnit_get_sitemap_options_default();

	$paras = array( 'excludeId' );

	foreach ( $paras as $key => $value ) {
		$output[ $value ] = (isset( $input[ $value ] )) ? $input[ $value ] : '';
	}
	return apply_filters( 'vkExUnit_sitemap_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*  insert sitemap page
/*-------------------------------------------*/
add_filter( 'the_content', 'show_sitemap', 7, 1 );

function show_sitemap( $content ) {
	global $is_pagewidget;
	if ( $is_pagewidget ) { return $content; }
	wp_reset_postdata(); // need under other section / ex:child page index
	global $post;
	$enable = get_post_meta( $post->ID, 'vkExUnit_sitemap',true );
	if ( $enable ) {
		return $content . "\n" . do_shortcode( '[vkExUnit_sitemap]' );
	}
	return $content;
}

function vkExUnit_sitemap( $atts ) {

	extract(shortcode_atts(array(
		'exclude' => '',
	), $atts));

	$sitemap_html = '<div class="row veu_sitemap">'.PHP_EOL;
	$options = vkExUnit_get_sitemap_options();
	$exclude = esc_attr( $options['excludeId'] );
	$exclude = str_replace( '，',',',$exclude );
	$exclude = mb_convert_kana( $exclude, 'kvrn' );

	/*-------------------------------------------*/
	/* pages
	/*-------------------------------------------*/
	$sitemap_html .= '<div class="col-md-6 sitemap-col">'.PHP_EOL;
	$sitemap_html .= '<ul class="link-list">'.PHP_EOL;
	$args = array(
		'title_li' 	=> '',
		'echo'		=> 0,
		'exclude_tree'	=> $exclude,
	);
	$sitemap_html .= wp_list_pages( $args );

	$sitemap_html .= '</ul><!-- [ /.link-list ] -->'.PHP_EOL;
	$sitemap_html .= '</div><!-- [ /.sitemap-col ] -->'.PHP_EOL;

	/*-------------------------------------------*/
	/* Posts & Custom posts
	/*-------------------------------------------*/
	$sitemap_html .= '<div class="col-md-6 sitemap-col">'.PHP_EOL;

	$page_for_posts = vkExUnit_get_page_for_posts();
	$allPostTypes = get_post_types( array( 'public' => true ) );

	foreach ( $allPostTypes as $postType ) {
		$post_type_object = get_post_type_object( $postType );

		if ( $post_type_object ) {
			$postType_name = esc_html( $post_type_object->name );
			// post-type is post
			if ( $postType_name === 'post' ) {

				$postTypes 	= array( 'post' );
				$taxonomies = get_taxonomies();
				// Loop all post types
				foreach ( $postTypes as $key => $postType ) {

					$sitemap_html .= '<div class="sectionBox">'.PHP_EOL;
					$post_type_object = get_post_type_object( $postType );
					if ( $post_type_object ) {

						// Post type name
						if ( $postType == 'post' && $page_for_posts['post_top_use'] ) {
							$postTypeName = $page_for_posts['post_top_name'];
							$postTypeTopUrl = get_the_permalink( $page_for_posts['post_top_id'] );
						} else {
							$postTypeName = $post_type_object->labels->name;
							$postTypeTopUrl = home_url().'/?post_type='.$postType;
						}
						$sitemap_html .= '<h4><a href="'.$postTypeTopUrl.'">'.esc_html( $postTypeName ).'</a></h4>'.PHP_EOL;

						// Loop for all taxonomies
						foreach ( $taxonomies as $key => $taxonomy ) {
							$taxonomy_info = get_taxonomy( $taxonomy );

							// Get tax related post type
							$taxonomy_postType = $taxonomy_info->object_type[0];
							if ( $taxonomy_postType == $postType && ( $taxonomy_info->name != 'post_format') ) {
								$sitemap_html .= '<h5>'.$taxonomy_info->labels->name.'</h5>'.PHP_EOL;
								$sitemap_html .= '<ul class="link-list">'.PHP_EOL;
													$args = array(
														'taxonomy' => $taxonomy,
														'title_li' => '',
														'orderby' => 'order',
														'echo'	=> 0,
														'show_option_none' => '',
													);
													$sitemap_html .= wp_list_categories( $args );
													$sitemap_html .= '</ul><!-- [ /.link-list ] -->'.PHP_EOL;
							}
						}
					} // end if($post_type_object)
				} // end foreach ($postTypes as $key => $postType)
			} // end post-type is post
			// not page_type and post_type
			else if ( $postType_name !== 'page' && $postType_name !== 'attachment' ) {
				$customPost_url = home_url().'/?post_type='.$postType_name;
				$sitemap_html .= '<h4><a href="'.$customPost_url.'">'.$post_type_object->labels->name.'</a></h4>'.PHP_EOL;

				$termNames = get_object_taxonomies( $postType_name );

				foreach ( $termNames as $termName ) {
					$termDate = get_taxonomy( $termName );
					$sitemap_html .= '<h5>'.$termDate->label.'</h5>'.PHP_EOL;
								$sitemap_html .= '<ul class="link-list">'.PHP_EOL;
													$args = array(
														'taxonomy' => $termDate->name,
														'title_li' => '',
														'orderby' => 'order',
														'echo'	=> 0,
														'show_option_none' => '',
													);
													$sitemap_html .= wp_list_categories( $args );
													$sitemap_html .= '</ul>'.PHP_EOL;
				}
			} // end not page_type and post_type
		} // end if($post_type_object)
	} // end foreach ($allPostTypes as $postType)
	$sitemap_html .= '</div><!-- [ /.sectionBox ] -->'.PHP_EOL;
	$sitemap_html .= '</div><!-- [ /.sitemap-col ] -->'.PHP_EOL;
	$sitemap_html .= '</div><!-- [ /.sitemap ] -->'.PHP_EOL;

	return $sitemap_html;
}
add_shortcode( 'vkExUnit_sitemap', 'vkExUnit_sitemap' );


add_filter( 'vkExUnit_customField_Page_activation', 'vkExUnit_sitemap_activate', 10, 1 );
function vkExUnit_sitemap_activate( $flag ) {
	return true;
}


/*-------------------------------------------*/
/*  admin _ meta box
/*-------------------------------------------*/
add_action( 'vkExUnit_customField_Page_box', 'vkExUnit_sitemap_meta_box' );
function vkExUnit_sitemap_meta_box() {
	global $post;
	// sitemap display
	$enable = get_post_meta( $post->ID, 'vkExUnit_sitemap', true );	?>

<div>
<input type="hidden" name="_nonce_vkExUnit__custom_field_sitemap" id="_nonce_vkExUnit__custom_field_sitemap" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) );?>" />
<label for="vkExUnit_sitemap">
	<input type="checkbox" id="vkExUnit_sitemap" name="vkExUnit_sitemap" <?php echo ($enable)? ' checked' : ''; ?> />
	<?php _e( 'Display a HTML sitemap', 'vkExUnit' );?>
</label>
</div>

	<?php
}


// save custom field sitemap
add_action( 'save_post', 'vkExUnit_save_custom_field_sitemapData' );
function vkExUnit_save_custom_field_sitemapData( $post_id ) {
	$sitemap = isset( $_POST['_nonce_vkExUnit__custom_field_sitemap'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_field_sitemap'] ) : null;

	if ( ! wp_verify_nonce( $sitemap, plugin_basename( __FILE__ ) ) ) {
			return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$data = isset( $_POST['vkExUnit_sitemap'] ) ? htmlspecialchars( $_POST['vkExUnit_sitemap'] ) : null;

	if ( 'page' == $data ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) { return $post_id; }
	}

	if ( '' == get_post_meta( $post_id, 'vkExUnit_sitemap' ) ) {
		add_post_meta( $post_id, 'vkExUnit_sitemap', $data, true );
	} else if ( $data != get_post_meta( $post_id, 'vkExUnit_sitemap' ) ) {
		update_post_meta( $post_id, 'vkExUnit_sitemap', $data );
	} else if ( '' == $data ) {
		delete_post_meta( $post_id, 'vkExUnit_sitemap' );
	}
}
