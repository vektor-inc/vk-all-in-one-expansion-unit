<?php
add_filter( 'wp_title','vkExUnit_get_wp_head_title',11 );


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
        if( empty( $options['extend_flontTitle'] ) )
            $title = get_bloginfo( 'name' ).$sep.get_bloginfo( 'description' );
        else
            $title = $options['extend_flontTitle'];

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
add_action( 'admin_init', 'vkExUnit_wp_title_init' );



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
printf( __( 'Normally, I will include the %1$s in the title tag.', 'vkExUnit' ), $sitetitle_link );?><br />
<?php _e('For example, it appears in the form of <br />&lt;title&gt;page title | site title&lt;/title&gt;<br /> if using a static page.', 'vkExUnit'); ?>
<?php
printf( __('However, it might have negative impact on search engine rankings if the &lt;title&gt; is too long, <strong>therefore please include the most popular keywords in a summarized manner, keeping the %s as short as possible.</strong>', 'vkExUnit'),$sitetitle_link) ; ?>
<?php _e('However, in the home page, as described above, other title will not be added, it is possible to make the &lt;title&gt; little longer, which can be set separately here.', 'vkExUnit'); ?></p>

<input type="text" name="vkExUnit_wp_title[extend_flontTitle]" value="<?php echo  $options['extend_flontTitle']; ?>" placeholder="<?php _e( 'Noting, set automatically', 'vkExUnit' ); ?>" />
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
        'extend_flontTitle' => '',
    );
    return apply_filters( 'vkExUnit_wp_title_default', $default_options );
}



function vkExUnit_wp_title_validate( $input ) {
    $output = array();
    $output['extend_flontTitle'] = htmlspecialchars( $input['extend_flontTitle'] );
    return $output;
}
