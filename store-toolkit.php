<?php
/*
Plugin Name: Jigoshop - Store Toolkit
Plugin URI: http://www.visser.com.au/jigoshop/plugins/store-toolkit/
Description: Store Toolkit includes a growing set of commonly-used Jigoshop administration tools aimed at web developers and store maintainers.
Version: 1.4.0
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
Text Domain: jigoshop-store-toolkit
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'JIGO_ST_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'JIGO_ST_RELPATH', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'JIGO_ST_PATH', plugin_dir_path( __FILE__ ) );
define( 'JIGO_ST_PREFIX', 'jigo_st' );

include_once( JIGO_ST_PATH . 'includes/common.php' );
include_once( JIGO_ST_PATH . 'includes/functions.php' );

function jigo_st_i18n() {

	load_plugin_textdomain( 'jigo_st', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}
add_action( 'init', 'jigo_st_i18n' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	// Add Settings link to the Plugins screen
	function jigo_st_add_settings_link( $links, $file ) {

		static $this_plugin;
		if( !$this_plugin ) $this_plugin = plugin_basename( __FILE__ );
		if( $file == $this_plugin ) {
			// Manage
			$manage_link = sprintf( '<a href="%s">' . __( 'Manage', 'jigo_st' ) . '</a>', add_query_arg( 'page', 'jigo_st', 'admin.php' ) );
			array_unshift( $links, $manage_link );
		}
		return $links;

	}
	add_filter( 'plugin_action_links', 'jigo_st_add_settings_link', 10, 2 );

	function jigo_st_enqueue_scripts( $hook ) {

		// Settings
		$page = 'jigoshop_page_jigo_st';
		if( $page == $hook ) {
			wp_enqueue_script( 'jigo_st_scripts', plugins_url( '/templates/admin/jigo-admin_st-toolkit.js', __FILE__ ), array( 'jquery' ) );
		}
		// Simple check that Jigoshop is activated
		if( class_exists( 'jigoshop' ) ) {
			wp_enqueue_style( 'jigoshop_admin_styles', jigoshop::assets_url() . '/assets/css/admin.css' );
			wp_enqueue_script( 'jquery_flot', jigoshop::assets_url() . '/assets/js/jquery.flot.min.js', array( 'jquery' ), '1.0' );
			wp_enqueue_script( 'jquery_flot_pie', jigoshop::assets_url() . '/assets/js/jquery.flot.pie.min.js', array( 'jquery' ), '1.0' );
		}
		wp_enqueue_style( 'jigo_st_styles', plugins_url( '/templates/admin/jigo-admin_st-toolkit.css', __FILE__ ) );

	}
	add_action( 'admin_enqueue_scripts', 'jigo_st_enqueue_scripts' );

	function jigo_st_admin_init() {

		global $wpdb;

		// Check the User has the manage_options capability
		if( current_user_can( 'manage_options' ) == false )
			return;

		$action = ( function_exists( 'jigo_get_action' ) ? jigo_get_action() : false );
		switch( $action ) {

			case 'nuke':
				// Make sure we play nice with other Jigoshop and WordPress nukes
				if( !isset( $_POST['jigo_st_nuke'] ) ) {
					$url = add_query_arg( array( 'action' => null, 'message' => __( 'A required $_POST element was not detected so the requested nuke will not proceed', 'jigo_ce' ) ) );
					wp_redirect( $url );
					exit();
				}

				// We need to verify the nonce.
				check_admin_referer( 'nuke', 'jigo_st_nuke' );

				if( !ini_get( 'safe_mode' ) )
					set_time_limit( 0 );

				// Jigoshop
				if( isset( $_POST['jigo_st_products'] ) )
					jigo_st_clear_dataset( 'products' );
				if( isset( $_POST['jigo_st_categories'] ) ) {
					$categories = $_POST['jigo_st_categories'];
					jigo_st_clear_dataset( 'categories', $categories );
				} else if( isset( $_POST['jigo_st_product_categories'] ) ) {
					jigo_st_clear_dataset( 'categories' );
				}
				if( isset( $_POST['jigo_st_product_tags'] ) )
					jigo_st_clear_dataset( 'tags' );
				if( isset( $_POST['jigo_st_product_images'] ) )
					jigo_st_clear_dataset( 'images' );
				if( isset( $_POST['jigo_st_coupons'] ) )
					jigo_st_clear_dataset( 'coupons' );
				if( isset( $_POST['jigo_st_attributes'] ) )
					jigo_st_clear_dataset( 'attributes' );
				if( isset( $_POST['jigo_st_orders'] ) ) {
					$orders = $_POST['jigo_st_orders'];
					jigo_st_clear_dataset( 'orders', $orders );
				} else if( isset( $_POST['jigo_st_sales_orders'] ) ) {
					jigo_st_clear_dataset( 'orders' );
				}

				// 3rd Party
				if( isset( $_POST['jigo_st_creditcards'] ) )
					jigo_st_clear_dataset( 'credit-cards' );

				// WordPress
				if( isset( $_POST['jigo_st_posts'] ) )
					jigo_st_clear_dataset( 'posts' );
				if( isset( $_POST['jigo_st_post_categories'] ) )
					jigo_st_clear_dataset( 'post_categories' );
				if( isset( $_POST['jigo_st_post_tags'] ) )
					jigo_st_clear_dataset( 'post_tags' );
				if( isset( $_POST['jigo_st_links'] ) )
					jigo_st_clear_dataset( 'links' );
				if( isset( $_POST['jigo_st_comments'] ) )
					jigo_st_clear_dataset( 'comments' );
				break;

			case 'delete-inactive-users':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'jigo_st_delete_inactive_users' ) ) {
					$user_ids = array();
					$roles = array( 'subscriber', 'bbp_blocked', 'bbp_participant', 'bbp_spectator' );
					foreach( $roles as $role ) {
						$role_args = array(
							'role' => $role,
							'fields' => 'ID'
						);
						$user_query = new WP_User_Query( $role_args );
						// User Loop
						if ( !empty( $user_query->results ) ) {
							foreach ( $user_query->results as $user_id ) {
								// Check if the User has commented
								$comment_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(comment_ID) FROM ' . $wpdb->comments. ' WHERE user_id = %d AND comment_approved = 1', $user_id ) );
								// Check if the User has made any purchases
								$post_type = 'shop_order';
								$order_args = array(
									'post_type' => $post_type,
									'post_status' => array( 'any' ),
									'meta_key' => 'customer_user',
									'meta_value' => $user_id,
									'fields' => 'ids'
								);
								$order_query = new WP_Query( $order_args );
								$order_count = $order_query->found_posts;
								if( $comment_count == 0 && $order_count == 0 )
									$user_ids[] = $user_id;
							}
							unset( $user_id );
						}
					}
					$deleted_users = count( $user_ids );
					if( $deleted_users > 0 )
						$message = sprintf( __( '%s inactive Users have been deleted.', 'jigo_st' ), '<strong>' . $deleted_users . '</strong>' );
					else
						$message = __( 'No inactive Users have been deleted.', 'jigo_st' );
					$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
					echo $output;
				}
				break;

			case 'unlink-deleted-user-downloads':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'jigo_st_unlink_deleted_user_downloads' ) ) {
					$downloads_sql = "SELECT * FROM `" . $wpdb->prefix . "jigoshop_downloadable_product_permissions` WHERE `user_id` <> 0";
					$downloads = $wpdb->get_results( $downloads_sql );
					$size = $wpdb->num_rows;
					if( $downloads ) {
						$adjusted_downloads = 0;
						foreach( $downloads as $download ) {
							if( !$user_info = get_userdata( $download->user_id ) ) {
								$wpdb->update( $wpdb->prefix . 'jigoshop_downloadable_product_permissions', array(
									'user_id' => 0
								), array( 'product_id' => $download->product_id, 'user_email' => $download->user_email, 'order_key' => $download->order_key ) );
								$adjusted_downloads++;
							}
							unset( $user_info );
						}
					}
					if( $adjusted_downloads > 0 )
						$message = sprintf( __( '%s of %s ghost File Download\'s have been un-linked from their Users.', 'jigo_st' ), '<strong>' . $adjusted_downloads . '</strong>', '<strong>' . $size . '</strong>' );
					else
						$message = __( 'No ghost File Downloads have been un-linked.', 'jigo_st' );
					$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
					echo $output;
				}
				break;

			case 'relink-existing-preregistered-sales':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'jigo_st_relink_existing_preregistered_sales' ) ) {
					// Re-link Orders
					$orders = jigo_st_get_unlinked_sales();
					$total_orders = count( $orders );
					if( $orders ) {
						$adjusted_orders = 0;
						foreach( $orders as $order ) {
							if( !$order_user = get_post_meta( $order->ID, 'customer_user', true ) ) {
								if( $order_email = jigo_st_get_email_from_sale( $order->ID ) ) {
									if( $order_user = get_user_by( 'email', $order_email ) ) {
										update_post_meta( $order->ID, 'customer_user', $order_user->ID );
										$adjusted_orders++;
									}
								}
							}
						}
					}
					if( $adjusted_orders > 0 )
						$message = sprintf( __( '%s of %s unlinked Order\'s from pre-registered Users have been re-linked.', 'jigo_st' ), '<strong>' . $adjusted_orders . '</strong>', '<strong>' . $total_orders . '</strong>' );
					else
						$message = __( 'No existing Orders from pre-registered Users have been re-linked.', 'jigo_st' );
					$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
					echo $output;
				}
				break;

			case 'relink-my_account-downloads':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'jigo_st_relink_my_account_downloads' ) ) {
					// Re-link File Downloads
					$downloads_sql = "SELECT * FROM `" . $wpdb->prefix . "jigoshop_downloadable_product_permissions` WHERE `user_id` = 0";
					$downloads = $wpdb->get_results( $downloads_sql );
					$total_downloads = $wpdb->num_rows;
					if( $downloads ) {
						$adjusted_downloads = 0;
						foreach( $downloads as $download ) {
							if( $download->user_email ) {
								if( $order_user = get_user_by( 'email', $download->user_email ) ) {
									$download->user_id = $order_user->ID;
									$wpdb->update( $wpdb->prefix . 'jigoshop_downloadable_product_permissions', array(
										'user_id' => $download->user_id
									), array( 'product_id' => $download->product_id, 'user_email' => $download->user_email, 'order_key' => $download->order_key ) );
									$adjusted_downloads++;
								}
							}
						}
					}
					if( $adjusted_downloads > 0 )
						$message = sprintf( __( '%s of %s unlinked File Download\'s have been re-linked to their customers.', 'jigo_st' ), '<strong>' . $adjusted_downloads . '</strong>', '<strong>' . $total_downloads . '</strong>' );
					else
						$message = __( 'No unlinked File Downloads have been re-linked.', 'jigo_st' );
					$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
					echo $output;
				}
				break;

			default:
				add_action( 'add_meta_boxes', 'jigo_st_meta_boxes' );
				if( function_exists( 'aioseop_get_version' ) )
					add_action( 'jigoshop_process_product_meta', 'jigo_st_process_product_meta', 1, 2 );
				break;

		}

	}
	add_action( 'admin_init', 'jigo_st_admin_init' );

	function jigo_st_default_html_page() {

		global $wpdb;

		$tab = false;
		if( isset( $_GET['tab'] ) )
			$tab = $_GET['tab'];

		include_once( JIGO_ST_PATH . 'templates/admin/jigo-admin_st-toolkit.php' );

	}

	function jigo_st_html_page() {

		global $wpdb;

		jigo_st_template_header();
		jigo_st_support_donate();
		$action = ( function_exists( 'jigo_get_action' ) ? jigo_get_action() : false );
		switch( $action ) {

			case 'nuke':
				$message = __( 'Chosen Jigoshop details have been permanently erased from your Jigoshop store.', 'jigo_st' );
				$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
				echo $output;

				jigo_st_default_html_page();
				break;

			default:
				jigo_st_default_html_page();
				break;

		}
		jigo_st_template_footer();

	}

	// Add Jigoshop store details to WordPress Administration Dashboard
	function jigo_st_add_dashboard_widgets() {

		wp_add_dashboard_widget( 'jigo_st-dashboard_monthly_report', __( 'Monthly Report', 'jigo_st' ), 'jigo_st_dashboard_monthly_report' );
		wp_add_dashboard_widget( 'jigo_st-dashboard_recent_orders', __( 'Recent Orders', 'jigo_st' ), 'jigo_st_dashboard_recent_orders' );
		wp_add_dashboard_widget( 'jigo_st-dashboard_right_now', __( 'Right Now in Store', 'jigoshop' ), 'jigo_st_dashboard_right_now' );
		wp_add_dashboard_widget( 'jigo_st-dashboard_stock_report', __( 'Stock Report', 'jigoshop' ), 'jigo_st_dashboard_stock_report' );
		wp_add_dashboard_widget( 'jigo_st-dashboard_sales', __( 'Sales Summary', 'jigo_st' ), 'jigo_st_dashboard_sales_summary' );

	}
	add_action( 'wp_dashboard_setup', 'jigo_st_add_dashboard_widgets' );

	function jigo_st_dashboard_monthly_report() {

		global $current_month_offset;

		$current_month_offset = (int)date( 'm' );

		if( isset( $_GET['month'] ) )
			$current_month_offset = (int)$_GET['month'];

		include_once( 'templates/admin/jigo-admin_st-dashboard_monthly_report.php' );

	}

	function jigo_st_dashboard_recent_orders() {

		$post_type = 'shop_order';
		$args = array(
			'numberposts'	=> 10,
			'orderby'		=> 'post_date',
			'order'			=> 'DESC',
			'post_type'		=> $post_type,
			'post_status'	=> 'publish'
		);
		$orders = get_posts( $args );

		include_once( 'templates/admin/jigo-admin_st-dashboard_recent_orders.php' );

	}

	function jigo_st_dashboard_right_now() {

		include_once( 'templates/admin/jigo-admin_st-dashboard_right_now.php' );

	}

	function jigo_st_dashboard_stock_report() {

		include_once( 'templates/admin/jigo-admin_st-dashboard_stock_report.php' );
		
	}

	function jigo_st_dashboard_sales_summary() {

		global $wpdb;

		// Set defaults
		$sales_today = (float)0;
		$sales_yesterday = (float)0;
		$sales_week = (float)0;
		$sales_last_week = (float)0;
		$sales_month = (float)0;
		$sales_last_month = (float)0;

		$post_type = 'shop_order';
		$term_taxonomy = 'shop_order_status';
		$order_status = implode( "','", array( 'completed', 'processing', 'on-hold' ) );

		// Get totals for last month
		$orders_last_month_sql = $wpdb->prepare( "SELECT posts.ID FROM {$wpdb->posts} as posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )
			WHERE 	posts.post_type 	= %s
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= %s
			AND		term.slug			IN ( '{$order_status}' )
			AND 	posts.post_date >= '" . date( 'Y-m-d', strtotime( 'first day of last month' ) ) . "'
			AND 	posts.post_date <= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "'
		", $post_type, $term_taxonomy );
		$orders = $wpdb->get_results( $orders_last_month_sql );
		if( !empty( $orders ) ) {
			foreach( $orders as $order ) {
				$order_data = new jigoshop_order( $order );
				if( $order_data->status == 'cancelled' || $order_data->status == 'refunded' )
					continue;
				if( isset( $sales_month ) )
					$sales_last_month = $sales_last_month + $order_data->order_total;
				else
					$sales_last_month = (float)$order_data->status;
			}
		}

		// Get totals for this month
		$orders_month_sql = $wpdb->prepare( "SELECT posts.ID FROM {$wpdb->posts} as posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )
			WHERE 	posts.post_type 	= %s
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= %s
			AND		term.slug			IN ( '{$order_status}' )
			AND 	posts.post_date >= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "'
			AND 	posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "'
		", $post_type, $term_taxonomy );
		$orders = $wpdb->get_results( $orders_month_sql );
		if( !empty( $orders ) ) {
			foreach( $orders as $order ) {
				$order_data = new jigoshop_order( $order );
				if( $order_data->status == 'cancelled' || $order_data->status == 'refunded' )
					continue;
				if( isset( $sales_month ) )
					$sales_month = $sales_month + $order_data->order_total;
				else
					$sales_month = (float)$order_data->status;
			}
		}

		// Get totals for last week
		$orders_last_week_sql = $wpdb->prepare( "SELECT posts.ID FROM {$wpdb->posts} as posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )
			WHERE 	posts.post_type 	= %s
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= %s
			AND		term.slug			IN ( '{$order_status}' )
			AND 	posts.post_date >= '" . date( 'Y-m-d', strtotime( 'Monday last week' ) ) . "'
			AND 	posts.post_date <= '" . date( 'Y-m-d H:i:s', strtotime( 'Monday this week' ) ) . "'
		", $post_type, $term_taxonomy );
		$orders = $wpdb->get_results( $orders_last_week_sql );
		if( !empty( $orders ) ) {
			foreach( $orders as $order ) {
				$order_data = new jigoshop_order( $order );
				if( $order_data->status == 'cancelled' || $order_data->status == 'refunded' )
					continue;
				if( isset( $sales_last_week ) )
					$sales_last_week = $sales_last_week + $order_data->order_total;
				else
					$sales_last_week = (float)$order_data->status;
			}
		}

		// Get totals for this week
		$orders_week_sql = $wpdb->prepare( "SELECT posts.ID FROM {$wpdb->posts} as posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )
			WHERE 	posts.post_type 	= %s
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= %s
			AND		term.slug			IN ( '{$order_status}' )
			AND 	posts.post_date >= '" . date( 'Y-m-d', strtotime( 'Monday this week' ) ) . "'
			AND 	posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "'
		", $post_type, $term_taxonomy );
		$orders = $wpdb->get_results( $orders_week_sql );
		if( !empty( $orders ) ) {
			foreach( $orders as $order ) {
				$order_data = new jigoshop_order( $order );
				if( $order_data->status == 'cancelled' || $order_data->status == 'refunded' )
					continue;
				if( isset( $sales_week ) )
					$sales_week = $sales_week + $order_data->order_total;
				else
					$sales_week = (float)$order_data->status;
			}
		}

		// Get totals for yesterday
		$orders_yesterday_sql = $wpdb->prepare( "SELECT posts.ID FROM {$wpdb->posts} as posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )
			WHERE 	posts.post_type 	= %s
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= %s
			AND		term.slug			IN ( '{$order_status}' )
			AND 	posts.post_date >= '" . date( 'Y-m-d', strtotime( 'Yesterday' ) ) . "'
			AND 	posts.post_date <= '" . date( 'Y-m-d H:i:s', strtotime( 'Today' ) ) . "'
		", $post_type, $term_taxonomy );
		$orders = $wpdb->get_results( $orders_yesterday_sql );
		if( !empty( $orders ) ) {
			foreach( $orders as $order ) {
				$order_data = new jigoshop_order( $order );
				if( $order_data->status == 'cancelled' || $order_data->status == 'refunded' )
					continue;
				if( isset( $sales_yesterday ) )
					$sales_yesterday = $sales_yesterday + $order_data->order_total;
				else
					$sales_yesterday = (float)$order_data->status;
			}
		}

		// Get totals for today
		$orders_today_sql = $wpdb->prepare( "SELECT posts.ID FROM {$wpdb->posts} as posts
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )
			WHERE 	posts.post_type 	= %s
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= %s
			AND		term.slug			IN ( '{$order_status}' )
			AND 	posts.post_date >= '" . date( 'Y-m-d', strtotime( 'Today' ) ) . "'
			AND 	posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "'
		", $post_type, $term_taxonomy );
		$orders = $wpdb->get_results( $orders_today_sql );
		if( !empty( $orders ) ) {
			foreach( $orders as $order ) {
				$order_data = new jigoshop_order( $order );
				if( $order_data->status == 'cancelled' || $order_data->status == 'refunded' )
					continue;
				if( isset( $sales_today ) )
					$sales_today = $sales_today + $order_data->order_total;
				else
					$sales_today = (float)$order_data->status;
			}
		}

		include_once( 'templates/admin/jigo-admin_st-dashboard_sales_summary.php' );

	}

	function jigo_st_order_data_tabs( $post, $data ) {

		$output = '<li><a href="#order_extra">' . __( 'Extra', 'jigoshop' ) . '</a></li>';
		echo $output;

	}
	add_action( 'jigoshop_order_data_tabs', 'jigo_st_order_data_tabs', 10, 2 );

	function jigo_st_order_data_panels( $post, $data ) {

		$order_key = '';
		$order_key_comment = '';
		if( $post->ID )
			$order_key = get_post_meta( $post->ID, 'order_key', true );
		if( empty( $order_key ) ) {
			$order_key = uniqid( 'order_' );
			$order_key_comment = '<span class="description"> (' . __( 'A new Order Key has been generated', 'jigoshop' ) . ')</span>';
		}
		$output = '
<div id="order_extra" class="panel jigoshop_options_panel">
	<p class="form-field">
		<label for="order_key">' . __( 'Order Key', 'jigoshop' ) . ':</label>
		<input type="text" name="order_key" id="order_key" value="' . $order_key . '" />
		' . $order_key_comment . '
	</p>
</div>';
		echo $output;

	}
	add_action( 'jigoshop_order_data_panels', 'jigo_st_order_data_panels', 10, 2 );

	function jigo_st_process_shop_order_meta( $post_id, $post ) {

		global $wpdb;

		// Order Key
		$order_key = (string)$_POST['order_key'];
		if( $order_key )
			update_post_meta( (int)$post_id, 'order_key', $order_key );

		$delete_download_sql = "DELETE FROM `" . $wpdb->prefix . "jigoshop_downloadable_product_permissions` WHERE `order_key` = '" . $order_key . "'";
		$delete_download = $wpdb->query( $delete_download_sql );

		foreach ( $_POST['jigo_st_item_id'] as $key => $item ) {
			$wpdb->insert(
				$wpdb->prefix . 'jigoshop_downloadable_product_permissions',
				array(
					'product_id'          => $_POST['jigo_st_item_id'][$key],
					'user_email'          => $_POST['jigo_st_customer_email'][$key],
					'user_id'             => $_POST['jigo_st_customer_id'][$key],
					'order_key'           => $order_key,
					'downloads_remaining' => $_POST['jigo_st_remaining'][$key]
				)
			);
		}		

	}
	add_action( 'jigoshop_process_shop_order_meta', 'jigo_st_process_shop_order_meta', 10, 2 );

	function add_order_data_meta_box( $post_type, $post = '' ) {

		if( $post->post_status <> 'auto-draft' ) {
			$post_type = 'shop_order';
			add_meta_box( 'jigoshop-order-post_data', __( 'Order Post Meta', 'jigo_st' ), 'jigo_st_order_data_meta_box', $post_type, 'normal', 'default' );
			add_meta_box( 'jigoshop-order-downloads_data', __( 'Order Downloads Meta', 'jigo_st' ), 'jigo_st_order_downloads_meta_box', $post_type, 'normal', 'default' );
		}

	}
	add_action( 'add_meta_boxes', 'add_order_data_meta_box', 10, 2 );

	function add_order_downloads_meta_box() {

		$post_type = 'shop_order';
		add_meta_box( 'jigoshop-order-downloads', __( 'Order Download Authorizations', 'jigo_st' ), 'jigo_st_order_download_permissions_meta_box', $post_type, 'normal', 'default' );

	}
	add_action( 'add_meta_boxes', 'add_order_downloads_meta_box' );

	function jigo_st_order_data_meta_box() {

		global $post;

		$post_meta = get_post_custom( $post->ID );

		if( empty( $post_meta ) ) {
			echo __( 'There are no Post meta for this Order.', 'jigo_st' );
			return;
		}

		include_once( JIGO_ST_PATH . 'templates/admin/jigo-admin_st-orders_data.php' );

	}

	function jigo_st_order_downloads_meta_box() {

		global $post, $wpdb;

		$_order = new jigoshop_order( $post->ID );
		$order_downloads_sql = "SELECT * FROM `" . $wpdb->prefix . "jigoshop_downloadable_product_permissions` WHERE `order_key` = '" . $_order->order_key . "'";
		$order_downloads = $wpdb->get_results( $order_downloads_sql );

		if( empty( $order_downloads ) ) {
			echo __( 'There are no downloadable Products linked to this Order.', 'jigo_st' );
			return;
		}


		include_once( JIGO_ST_PATH . 'templates/admin/jigo-admin_st-orders_downloads.php' );

	}

	function jigo_st_order_download_permissions_meta_box() {

		global $post, $wpdb;

		$_order = new jigoshop_order( $post->ID );
		$order_downloads_sql = "SELECT * FROM `" . $wpdb->prefix . "jigoshop_downloadable_product_permissions` WHERE `order_key` = '" . $_order->order_key . "'";
		$order_downloads = $wpdb->get_results( $order_downloads_sql );

		require( JIGO_ST_PATH . 'templates/admin/jigo-admin_st-orders_download_permissions.php' );

	}

	function add_product_data_meta_box( $post_type, $post = '' ) {

		if( $post->post_status <> 'auto-draft' ) {
			$post_type = 'product';
			add_meta_box( 'jigo-product-post_data', __( 'Product Post Meta', 'jigo_st' ), 'jigo_st_product_data_meta_box', $post_type, 'normal', 'default' );
		}

	}
	add_action( 'add_meta_boxes', 'add_product_data_meta_box', 10, 2 );

	function jigo_st_product_data_meta_box() {

		global $post;

		$post_meta = get_post_custom( $post->ID );

		include_once( JIGO_ST_PATH . 'templates/admin/jigo-admin_st-products_data.php' );

	}

	/* End of: WordPress Administration */

}

function jigo_st_add_order_download() {

	if ( isset( $_POST['item_to_add'] ) && isset( $_POST['customer_user'] ) ) {

		$item_id = $_POST['item_to_add'];
		$customer = get_userdata( $_POST['customer_user'] );

		if ( wp_get_post_parent_id( $item_id ) ) {
			$_product = new jigoshop_product_variation( $item_id );
        } else {
			$_product = new jigoshop_product( $item_id );
        }

		?>
		<tr class="item">
			<td class="product-id"><?php echo $item_id; ?></td>
			<td class="variation-id"><?php if ( isset( $_product->variation_id ) ) echo $_product->variation_id; else echo '-'; ?></td>
			<td class="product-sku"><?php if ( $_product->sku ) echo $_product->sku; ?></td>
			<td class="name"><a href="<?php echo esc_url( admin_url('post.php?post='. $_product->id .'&action=edit') ); ?>"><?php echo $_product->get_title(); ?></a></td>
			<td class="variation"><?php
				if ( isset( $_product->variation_data ) ) :
					echo jigoshop_get_formatted_variation( $_product, $_product->variation_data, true );
				else :
					echo '-';
				endif;
			?></td>
			<td class="customer-id"><?php echo $customer->ID; ?></td>
			<td class="customer"><?php echo $customer->user_nicename; ?></td>
			<td class="remaining">
				<input type="text" name="jigo_st_remaining[]" placeholder="" size="3" value="" />
			</td>
			<td class="center">
				<input type="hidden" name="jigo_st_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
				<input type="hidden" name="jigo_st_item_name[]" value="<?php echo esc_attr( $_product->get_title() ); ?>" />
                <input type="hidden" name="jigo_st_item_variation_id[]" value="<?php if ( isset( $_product->variation_id ) ) echo $_product->variation_id; else echo ''; ?>" />
				<input type="hidden" name="jigo_st_customer_id[]" value="<?php echo esc_attr( $customer->ID ); ?>" />
				<input type="hidden" name="jigo_st_customer_email[]" value="<?php echo esc_attr( $customer->user_email ); ?>" />
				<button type="button" class="remove_row button">&times;</button>
			</td>
		</tr>
	<?php
	}
	
	die();
}
add_action( 'wp_ajax_jigo_st_add_order_download', 'jigo_st_add_order_download' );
?>