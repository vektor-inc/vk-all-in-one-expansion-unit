<?php

add_post_type_support( 'page', 'excerpt' ); // add excerpt

/*
  Add OGP
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_print_og', 20 );
function vkExUnit_print_og() {
	global $vkExUnit_sns_options;

	// $ogImage = $vkExUnit_sns_options['ogImage'];
	// $fbAppId = $vkExUnit_sns_options['fbAppId'];
	global $wp_query;
	$post = $wp_query->get_queried_object();
	if ( is_home() || is_front_page() ) {
		$linkUrl = home_url( '/' );
	} elseif ( is_single() || is_page() ) {
		$linkUrl = get_permalink();
	} else {
		$linkUrl = get_permalink();
	}
	$vkExUnitOGP  = '<!-- [ ' . veu_get_name() . ' OGP ] -->' . "\n";
	$vkExUnitOGP .= '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:url" content="' . $linkUrl . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:title" content="' . veu_get_the_sns_title() . '" />' . "\n";
	$vkExUnitOGP .= '<meta property="og:description" content="' . esc_attr( vk_get_page_description() ) . '" />' . "\n";

	$addImageTag = false;

	if ( isset( $vkExUnit_sns_options['fbAppId'] ) && $vkExUnit_sns_options['fbAppId'] ) {
		$vkExUnitOGP = $vkExUnitOGP . '<meta property="fb:app_id" content="' . $vkExUnit_sns_options['fbAppId'] . '" />' . "\n";
	}
	if ( is_front_page() || is_home() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="website" />' . "\n";
		if ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";

			//image:width,image:height INSERT
			$addImageTag = array(
				'type' => 'url',
				'url'  => $vkExUnit_sns_options['ogImage'],
			);
		}
	} elseif ( is_category() || is_archive() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";
		if ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";

			//image:width,image:height INSERT
			$addImageTag = array(
				'type' => 'url',
				'url'  => $vkExUnit_sns_options['ogImage'],
			);
		}
	} elseif ( is_page() || is_single() ) {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";

		// image
		if ( has_post_thumbnail() ) {
			$image_id     = get_post_thumbnail_id();
			$image_url    = wp_get_attachment_image_src( $image_id, 'large', true );
			$vkExUnitOGP .= '<meta property="og:image" content="' . $image_url[0] . '" />' . "\n";

			//image:width,image:height INSERT
			$addImageTag = array(
				'type'   => 'id',
				'width'  => $mage_url[1],
				'height' => $image_url[2],
			);

		} elseif ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";

			//image:width,image:height INSERT
			$addImageTag = array(
				'type' => 'url',
				'url'  => $vkExUnit_sns_options['ogImage'],
			);
		}
	} else {
		$vkExUnitOGP .= '<meta property="og:type" content="article" />' . "\n";
		if ( isset( $vkExUnit_sns_options['ogImage'] ) && $vkExUnit_sns_options['ogImage'] ) {
			$vkExUnitOGP .= '<meta property="og:image" content="' . $vkExUnit_sns_options['ogImage'] . '" />' . "\n";

			//image:width,image:height INSERT
			$addImageTag = array(
				'type' => 'url',
				'url'  => $vkExUnit_sns_options['ogImage'],
			);
		}
	}

	//image:width,image:height INSERT
	if ( is_array( $addImageTag ) ) {
		if ( $addImageTag['type'] == 'id' ) {
			$vkExUnitOGP .= '<meta property="og:image:width" content="' . $addImageTag['width'] . '" />' . "\n";
			$vkExUnitOGP .= '<meta property="og:image:height" content="' . $addImageTag['height'] . '" />' . "\n";
		} elseif ( $addImageTag['type'] == 'url' ) {
			function ___getImageSizeFromURL( $url ) {
				$findPos = strpos( $url, '/uploads/' );
				if ( $findPos > 0 ) {
					$lpath = '';
					$lpath = substr( $url, strlen( '/uploads/' ) + $findPos );
					if ( strlen( $lpath ) ) {
						$imgpath                         = dirname( get_theme_root() ) . '/uploads/' . $lpath;
						list($width,$height,$type,$text) = @getimagesize( $imgpath );
						return array( $width, $height );
					}
				}
				return array( false, false );
			}
			if ( function_exists( '___getImageSizeFromURL' ) ) {
				list($width,$height) = ___getImageSizeFromURL( $addImageTag['url'] );
				if ( $width && $height ) {
					$vkExUnitOGP .= '<meta property="og:image:width" content="' . $width . '" />' . "\n";
					$vkExUnitOGP .= '<meta property="og:image:height" content="' . $height . '" />' . "\n";
				}
			}
		}
	}

	$vkExUnitOGP .= '<!-- [ / ' . veu_get_name() . ' OGP ] -->' . "\n";
	if ( isset( $vkExUnit_sns_options['ogTagDisplay'] ) && $vkExUnit_sns_options['ogTagDisplay'] != true ) {
		$vkExUnitOGP = '';
	}
	$vkExUnitOGP = apply_filters( 'vkExUnitOGPCustom', $vkExUnitOGP );
	echo $vkExUnitOGP;
}
