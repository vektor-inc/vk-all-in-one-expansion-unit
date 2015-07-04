<?php
/*--------------------------------------------------*/
/*	Display a Ex_unit information on the dashboard
/*--------------------------------------------------*/

add_filter('ex_unit_is_plugin_dashboard_info_widget', 'ex_unit_dash_beacon', 10, 1 );
function ex_unit_dash_beacon($flag){
	$flag = true;
	return $flag;
}

//displays dashboard widget only for Japanese version
if ( 'ja' == get_locale() ) {
	add_action( 'wp_dashboard_setup', 'ex_unit_dashboard_widget' );
}

function ex_unit_dashboard_widget()
{
	wp_add_dashboard_widget(
		'ex_unit_dashboard_widget',
		'Ex-unit ニュース',
		'ex_unit_dashboard_widget_function'
	);
}

function ex_unit_dashboard_widget_function()
{

	include_once(ABSPATH . WPINC . '/feed.php');
	
	$my_feeds = array( 
		array('feed_url' => 'http://ex-unit.bizvektor.com/?feed?'.date('his')) 
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
			$output .= '<ul>';

			if ( $maxitems == 0 )
			{
				$output .= '<li>';
				$output .= '表示するニュースがありません。';	
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
					$output .= '<a href="' . esc_url( $item->get_permalink() ) . '" title="' . $item_date . '">';
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