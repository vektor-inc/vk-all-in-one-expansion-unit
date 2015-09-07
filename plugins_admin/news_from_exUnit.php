<?php
function vkExUnit_news_body()
{

	include_once(ABSPATH . WPINC . '/feed.php');

	if ( 'ja' == get_locale() ) {
		$exUnit_feed_url = apply_filters( 'vkExUnit_news_RSS_URL_ja', 'http://ex-unit.bizvektor.com/ja/?feed?'.date('his') );
	} else {
		$exUnit_feed_url = apply_filters( 'vkExUnit_news_RSS_URL', 'http://ex-unit.bizvektor.com/?feed?'.date('his') );
	}

	$my_feeds = array(
		array('feed_url' => $exUnit_feed_url)
	);


	foreach ( $my_feeds as $feed )
	{
		$rss = fetch_feed( $feed["feed_url"] );

		if ( !is_wp_error($rss) )
		{
			$output = '';

			$maxitems = $rss->get_item_quantity( 5 ); //number of news to display (maximum)
			$rss_items = $rss->get_items( 0, $maxitems );

			$output .= '<div class="rss-widget">';

			$output .= '<div class="logo_exUnit">';
			$output .= '<img src="' . apply_filters( 'vkExUnit_news_image_URL_small', vkExUnit_get_directory_uri( '/images/head_logo_ExUnit.png' ) ) . '" alt="VK ExUnit" style="width:200px;" /></div>';
			$output .= '<ul>';

			if ( $maxitems == 0 )
			{
				$output .= '<li>';
				$output .= __('Sorry, there is no post', 'vkExUnit');
				$output .= '</li>';
			}
			else


			{
				foreach ( $rss_items as $item )
				{
					$test_date 	= $item->get_local_date();
					$content 	= $item->get_content();

					if( isset($test_date) && !is_null($test_date) )
						$item_date = $item->get_date( get_option('date_format') ) . '<br />';
					else
						$item_date = '';

					$output .= '<li style="color:#777;">';
					$output .= $item_date;
					$output .= '<a href="' . esc_url( $item->get_permalink() ) . '" title="' . $item_date . '" target="_blank">';
					$output .= esc_html( $item->get_title() );
					$output .= '</a>';
					$output .= '</li>';
				}
			}

			$output .= '</ul>';
			$output .= '</div>';

			echo $output;
		}
	}
}