<?php
/*

Filename: common-dashboard_widgets.php
Description: common-dashboard_widgets.php loads commonly access Dashboard widgets across the Visser Labs suite.
Version: 1.1

*/

/* Start of: Jigoshop News - by Visser Labs */

if( !function_exists( 'jigo_vl_dashboard_setup' ) ) {

	function jigo_vl_dashboard_setup() {

		wp_add_dashboard_widget( 'jigo_vl_news_widget', __( 'Plugin News - by Visser Labs', 'jigo_vl' ), 'jigo_vl_news_widget' );

	}
	add_action( 'wp_dashboard_setup', 'jigo_vl_dashboard_setup' );

	function jigo_vl_news_widget() {

		include_once( ABSPATH . WPINC . '/feed.php' );

		$rss = fetch_feed( 'http://www.visser.com.au/blog/category/jigoshop/feed/' );
		$output = '<div class="rss-widget">';
		if( !is_wp_error( $rss ) ) {
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
			$output .= '<ul>';
			foreach ( $rss_items as $item ) :
				$output .= '<li>';
				$output .= '<a href="' . $item->get_permalink() . '" title="' . 'Posted ' . $item->get_date( 'j F Y | g:i a' ) . '" class="rsswidget">' . $item->get_title() . '</a>';
				$output .= '<span class="rss-date">' . $item->get_date( 'j F, Y' ) . '</span>';
				$output .= '<div class="rssSummary">' . $item->get_description() . '</div>';
				$output .= '</li>';
			endforeach;
			$output .= '</ul>';
		} else {
			$message = __( 'Connection failed. Please check your network settings.', 'jigo_vl' );
			$output .= '<p>' . $message . '</p>';
		}
		$output .= '</div>';

		echo $output;

	}

}

/* End of: Jigoshop News - by Visser Labs */
?>