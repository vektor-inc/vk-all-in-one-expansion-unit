<?php
//WordPress -> 4.3
add_filter( 'wp_title', 'vkExUnit_get_wp_head_title', 11 );
//WordPress 4.4 ->
add_filter( 'pre_get_document_title', 'vkExUnit_get_wp_head_title', 11 );
/*-------------------------------------------*/
/*  Head title
/*-------------------------------------------*/
function vkExUnit_get_wp_head_title() {
	global $wp_query;
	$post = $wp_query->get_queried_object();
	$sep = ' | ';
	$sep = apply_filters( 'vkExUnit_get_wp_head_title', $sep );

	if ( is_front_page() ) {
		$options = vkExUnit_get_wp_title_options();
		if( empty( $options['extend_frontTitle'] ) )
			$title = get_bloginfo( 'name' ).$sep.get_bloginfo( 'description' );
		else
			$title = $options['extend_frontTitle'];

	} else if ( is_home() && ! is_front_page() ) {
		$title = vkExUnit_get_the_archive_title().$sep.get_bloginfo( 'name' );
	} else if ( is_archive() ) {
		$title = vkExUnit_get_the_archive_title().$sep.get_bloginfo( 'name' );
		// Page
	} else if ( is_page() ) {
		// Sub Pages
		if ( $post->post_parent ) {
			if ( $post->ancestors ) {
				foreach ( $post->ancestors as $post_anc_id ) {
					$post_id = $post_anc_id;
				}
			} else {
				$post_id = $post->ID;
			}
			$title = get_the_title().$sep.get_the_title( $post_id ).$sep.get_bloginfo( 'name' );
			// Not Sub Pages
		} else {
			$title = get_the_title().$sep.get_bloginfo( 'name' );
		}
	} else if ( is_single() || is_attachment() ) {
		$title = get_the_title().$sep.get_bloginfo( 'name' );

		// Search
	} else if ( is_search() ) {
		$title = sprintf( __( 'Search Results for : %s', 'vkExUnit' ),get_search_query() ).$sep.get_bloginfo( 'name' );
		// 404
	} else if ( is_404() ) {
		$title = __( 'Not found', 'vkExUnit' ).$sep.get_bloginfo( 'name' );
		// Other
	} else {
		$title = get_bloginfo( 'name' );
	}

	// Add Page numner.
	global $paged;
	if ( $paged >= 2 ) {
		$title = '['.sprintf( __( 'Page of %s', 'vkExUnit' ),$paged ).'] '.$title;
	}

	$title = apply_filters( 'vkExUnit_get_wp_head_title', $title );

	// Remove Tags(ex:<i>) & return
	return strip_tags( $title );
}


function vkExUnit_wp_title_init() {
	vkExUnit_register_setting(
		__( '&lt;title&gt; tag of homepage', 'vkExUnit' ),
		'vkExUnit_wp_title',
		'vkExUnit_wp_title_validate',
		'vkExUnit_add_wp_title_page'
	);
}
add_action( 'vkExUnit_package_init', 'vkExUnit_wp_title_init' );

function vkExUnit_add_wp_title_page(){
	$options = vkExUnit_get_wp_title_options();
?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e( '&lt;title&gt; tag of homepage', 'vkExUnit' ); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
<th><?php _e( '&lt;title&gt; tag of homepage', 'vkExUnit' ); ?></th>
<td>
<p>
<?php
$sitetitle_link = '<a href="'.get_admin_url().'options-general.php" target="_blank">'.__('title of the site', 'vkExUnit').'</a>';
printf( __( 'Normally "%1$s" is placed in the title tags of all the pages.', 'vkExUnit' ), $sitetitle_link );?><br />
<?php printf( __('For example, it appears in the form of <br />&lt;title&gt;page title | %1$s&lt;/title&gt;<br /> if using a static page.', 'vkExUnit'), $sitetitle_link ); ?><br />
<?php
printf( __('However, it might have negative impact on search engine rankings if the &lt;title&gt; is too long, <strong>therefore please include the most popular keywords in a summarized manner, keeping the %s as short as possible.</strong>', 'vkExUnit'),$sitetitle_link) ; ?><br />
<?php
$tagline_link = '<a href="'.get_admin_url().'options-general.php" target="_blank">'.__('Tagline', 'vkExUnit').'</a>';
printf( __( 'In the top page will be output usually in the form of <br />&lt;title&gt;%1$s | %2$s&lt;/title&gt;', 'vkExUnit'), $sitetitle_link ,$tagline_link );?><br />
<?php _e('However, it may be too long in the above format. If the input to the input field of the following, its contents will be reflected.', 'vkExUnit');?>
<?php /*_e('However, in the home page, as described above, other title will not be added, it is possible to make the &lt;title&gt; little longer, which can be set separately here.', 'vkExUnit');*/ ?></p>


<input type="text" name="vkExUnit_wp_title[extend_frontTitle]" value="<?php echo  $options['extend_frontTitle']; ?>" />
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
<?php
}



function vkExUnit_get_wp_title_options() {
	$options  = get_option( 'vkExUnit_wp_title', array() );
	$options  = wp_parse_args( $options, vkExUnit_get_wp_title_default() );
	return $options;
}



function vkExUnit_get_wp_title_default() {
	$default_options = array(
		'extend_frontTitle' => '',
	);
	return apply_filters( 'vkExUnit_wp_title_default', $default_options );
}



function vkExUnit_wp_title_validate( $input ) {
	$output = array();
	$output['extend_frontTitle'] = htmlspecialchars( $input['extend_frontTitle'] );
	return $output;
}
