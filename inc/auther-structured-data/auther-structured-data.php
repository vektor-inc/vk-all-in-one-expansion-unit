<?php
/**
 * Author Structured Data
 *
 * @package vk-all-in-one-expanaion-unit
 */

require dirname( __FILE__ ) . '/auther-structured-data-options.php';

function veu_asd_print_jsonLD_in_head() {
  $post_id = get_post(get_the_id());
  $author = get_userdata( $post_id -> post_author );
  $author_id = $author -> ID;
  if( is_single() ){
    echo veu_asd_generate_jsonLD( $author_id );
  }
}
add_action( 'wp_head', 'veu_asd_print_jsonLD_in_head', 9999 );

function veu_asd_generate_jsonLD( $author_id ) {
  $author_id = get_the_author_meta('ID');
	if ( ! isset( $author_id ) ) {
		return;
	}

$author_data = get_userdata( $author_id );
  $author_name = get_user_meta( $author_id, 'nickname', true ) ?: $author_data->display_name;
  $author_type = get_user_meta( $author_id, 'type', true ) ?: 'Organization';
  $sameAs = get_user_meta( $author_id, 'sameAs', true ) ?: '';

  $data = [
    '@type'         => $author_type,
    'name'          => $author_name,
    'url'           => home_url( '/' ),
    'sameAs'        => $sameAs,
  ];

	$JSON = '
  <!-- [ VK All in One Expansion Unit Author Structured Data ] -->
<script type="application/ld+json">
{
  "@context" : "https://schema.org/",
  "@type" : "Article",
  "author":
    {
      "@type": "' . esc_attr( $data['@type'] ) .'",
      "name":  "' . esc_attr( $data['name'] ) . '",
      "url": "' . esc_attr( $data['url'] ) . '",
      "sameAs": "' . esc_attr( $data['sameAs'] ) . '"
    }';
	$JSON .= '
}
</script>
<!-- [ / VK All in One Expansion Unit Author Structured Data ] -->
';

	return $JSON;
}
