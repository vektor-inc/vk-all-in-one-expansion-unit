<?php

add_post_type_support( 'page', 'excerpt' ); // add excerpt

add_action( 'wp_head', 'vkExUnit_add_twitterCard', 21 );

function vkExUnit_add_twitterCard() {
	global $vkExUnit_sns_options;

	$options = veu_get_sns_options();
	$ignores = explode( ',', $options['snsBtn_ignorePosts'] );

	if ( in_array( get_the_id(), $ignores ) ) {
		return false;
	}

	// url
	$linkUrl = ( is_front_page() ) ? home_url() : get_permalink();
	// image
	if ( ( is_single() || is_page() ) && has_post_thumbnail() ) {
		$image_id         = get_post_thumbnail_id();
		$image_default_id = '';

		if ( veu_package_is_enable( 'default_thumbnail' ) ) {
			$image_option     = get_option( 'veu_defualt_thumbnail' );
			$image_default_id = ! empty( $image_option['default_thumbnail_image'] ) ? $image_option['default_thumbnail_image'] : '';
		}

		if ( ! empty( $image_id ) ) {
			$image_url      = wp_get_attachment_image_src( $image_id, 'large', true );
			$card_image_url = $image_url[0];
		} elseif ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$card_image_url = $vkExUnit_sns_options['ogImage'];
		} elseif ( ! empty( $image_default_id ) ) {
			$image_url      = wp_get_attachment_image_src( $image_default_id, 'large', true );
			$card_image_url = $image_url[0];
		}
	} else {
		$card_image_url = ( isset( $vkExUnit_sns_options['ogImage'] ) ) ? $vkExUnit_sns_options['ogImage'] : '';
	}

	// domain
	preg_match( '/https?:\/\/(.+?)\//i', admin_url(), $match );
	?>
<!-- [ <?php echo veu_get_name(); ?> twitter card ] -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:description" content="<?php echo esc_attr( vk_get_page_description() ); ?>">
<meta name="twitter:title" content="<?php echo esc_attr( veu_get_the_sns_title() ); ?>">
<meta name="twitter:url" content="<?php echo esc_url( $linkUrl ); ?>">
	<?php if ( isset( $card_image_url ) && $card_image_url ) { ?>
<meta name="twitter:image" content="<?php echo esc_url( $card_image_url ); ?>">
	<?php } ?>
<meta name="twitter:domain" content="<?php echo esc_attr( $match[1] ); ?>">
	<?php if ( isset( $vkExUnit_sns_options['twitterId'] ) && $vkExUnit_sns_options['twitterId'] ) : ?>
<meta name="twitter:site" content="@<?php echo esc_attr( $vkExUnit_sns_options['twitterId'] ); ?>">
	<?php endif; ?>
<!-- [ / <?php echo veu_get_name(); ?> twitter card ] -->
	<?php
}
