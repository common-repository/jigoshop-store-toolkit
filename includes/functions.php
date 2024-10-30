<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// WordPress Administration menu
	function jigo_st_admin_menu() {

		add_submenu_page( 'jigoshop', __( 'Store Toolkit', 'jigo_st' ), __( 'Store Toolkit', 'jigo_st' ), 'manage_options', 'jigo_st', 'jigo_st_html_page' );

	}
	add_action( 'admin_menu', 'jigo_st_admin_menu', 11 );

	function jigo_st_template_header( $title = '', $icon = 'jigoshop' ) {

		if( $title )
			$output = $title;
		else
			$output = __( 'Store Toolkit', 'jigo_st' ); ?>
<div class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-jigoshop-settings"><br /></div>
	<h2><?php echo $output; ?></h2>
<?php
	}

	function jigo_st_template_footer() { ?>
</div>
<!-- .wrap -->
<?php
	}

	function jigo_st_support_donate() {

		$output = '';
		$show = true;
		if( function_exists( 'jigo_vl_we_love_your_plugins' ) ) {
			if( in_array( JIGO_ST_DIRNAME, jigo_vl_we_love_your_plugins() ) )
				$show = false;
		}
		if( $show ) {
			$donate_url = 'http://www.visser.com.au/#donations';
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . JIGO_ST_DIRNAME;
			$output = '
	<div id="support-donate_rate" class="support-donate_rate">
		<p>' . sprintf( __( '<strong>Like this Plugin?</strong> %s and %s.', 'jigo_st' ), '<a href="' . $donate_url . '" target="_blank">' . __( 'Donate to support this Plugin', 'jigo_st' ) . '</a>', '<a href="' . add_query_arg( array( 'rate' => '5' ), $rate_url ) . '#postform" target="_blank">rate / review us on WordPress.org</a>' ) . '</p>
	</div>
';
		}
		echo $output;

	}

	function jigo_st_meta_boxes() {

		$post_type = 'product';
		add_meta_box( 'jigoshop-aioseop', __( 'All in One SEO Pack', 'jigo_st' ), 'jigo_st_aioseop_box', $post_type, 'normal', 'default' );

	}

	function jigo_st_aioseop_box() {

		global $post, $wpdb;

		$aioseop_enabled = true;
		if( !function_exists( 'aioseop_get_version' ) ) {
			$aioseop_enabled = false;
			$link = 'http://wordpress.org/extend/plugins/all-in-one-seo-pack/';
			$message = sprintf( __( 'To enter All in One SEO Pack details you must install and activate the <a href="%s" target="_blank">All in One SEO Pack</a> (via WordPres Extend) Plugin.', 'jigo_st' ), $link );
			$output = '<div class="error-message"><p>' . $message . '</p></div>';
			echo $output;
		}

		wp_nonce_field( 'jigoshop_save_data', 'jigoshop_meta_nonce' );

		$keywords = get_post_meta( $post->ID, '_aioseop_keywords', true );
		$description = get_post_meta( $post->ID, '_aioseop_description', true );
		$title = get_post_meta( $post->ID, '_aioseop_title', true );
		$title_atr = get_post_meta( $post->ID, '_aioseop_titleatr', true );
		$menu_label = get_post_meta( $post->ID, '_aioseop_menulabel', true );

		include_once( JIGO_ST_PATH . 'templates/admin/jigo-admin_st_aioseop.php' );

	}

	function jigo_st_process_product_meta( $post_id, $post ) {

		update_post_meta( $post_id, '_aioseop_title', stripslashes( $_POST['aioseop_title'] ) );
		update_post_meta( $post_id, '_aioseop_description', stripslashes( $_POST['aioseop_description'] ) );
		update_post_meta( $post_id, '_aioseop_keywords', stripslashes( $_POST['aioseop_keywords'] ) );
		update_post_meta( $post_id, '_aioseop_titleatr', stripslashes( $_POST['aioseop_titleatr'] ) );
		update_post_meta( $post_id, '_aioseop_menulabel', stripslashes( $_POST['aioseop_menulabel'] ) );

	}

	function jigo_st_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			// Jigoshop

			case 'products':
				$post_type = 'product';
				$count = wp_count_posts( $post_type );
				break;

			case 'variations':
				$post_type = 'product_variation';
				$count = wp_count_posts( $post_type );
				break;

			case 'images':
				$post_type = 'product';
				$products = (array)get_posts( array(
					'post_type' => $post_type,
					'post_status' => jigo_st_post_statuses(),
					'numberposts' => -1
				) );
				$count = 0;
				if( $products ) {
					foreach( $products as $product ) {
						$args = array(
							'post_type' => 'attachment',
							'post_parent' => $product->ID,
							'post_status' => 'inherit',
							'post_mime_type' => 'image',
							'numberposts' => -1
						);
						$images = get_children( $args );
						if( $images )
							$count = $count + count( $images );
					}
				}
				break;

			case 'categories':
				$term_taxonomy = 'product_cat';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'orders':
				$post_type = 'shop_order';
				$count = wp_count_posts( $post_type );
				break;

			case 'coupons':
				$post_type = 'shop_coupon';
				$count = wp_count_posts( $post_type );
				break;

			case 'attributes':
				$count_sql = "SELECT COUNT(`attribute_id`) FROM `" . $wpdb->prefix . "jigoshop_attribute_taxonomies`";
				break;

			// 3rd Party

			case 'credit-cards':
				$post_type = 'offline_payment';
				$count = wp_count_posts( $post_type );
				break;

			// WordPress

			case 'posts':
				$post_type = 'post';
				$count = wp_count_posts( $post_type );
				break;

			case 'post_categories':
				$term_taxonomy = 'category';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'post_tags':
				$term_taxonomy = 'post_tag';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'links':
				$count_sql = "SELECT COUNT(`link_id`) FROM `" . $wpdb->prefix . "links`";
				break;

			case 'comments':
				$count = wp_count_comments();
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				if( is_object( $count ) ) {
					$count_object = $count;
					$count = 0;
					foreach( $count_object as $key => $item )
						$count = $item + $count;
				}
				return $count;
			} else {
				$count = $wpdb->get_var( $count_sql );
			}
			return $count;
		} else {
			return 0;
		}

	}

	function jigo_st_clear_dataset( $dataset, $data = null ) {

		global $wpdb;

		switch( $dataset ) {

			// Jigoshop

			case 'products':
				$post_type = 'product';
				$products = (array)get_posts( array(
					'post_type' => $post_type,
					'post_status' => jigo_st_post_statuses(),
					'numberposts' => -1
				) );
				if( $products ) {
					foreach( $products as $product ) {
						wp_delete_post( $product->ID, true );
						wp_set_object_terms( $product->ID, null, 'product_tag' );
						$attributes_sql = "SELECT `attribute_id` as ID, `attribute_name` as name, `attribute_label` as label, `attribute_type` as type FROM `" . $wpdb->prefix . "jigoshop_attribute_taxonomies`";
						$attributes = $wpdb->get_results( $attributes_sql );
						if( $attributes ) {
							foreach( $attributes as $attribute )
								wp_set_object_terms( $product->ID, null, 'pa_' . $attribute->name );
						}
					}
				}
				break;

			case 'variations':
				$post_type = 'product_variation';
				delete_option( 'product_variation_children' );
				break;

			case 'categories':
				$term_taxonomy = 'product_cat';
				if( $data ) {
					foreach( $data as $single_category ) {
						$post_type = 'product';
						$args = array(
							'post_type' => $post_type,
							'tax_query' => array(
								array(
									'taxonomy' => $term_taxonomy,
									'field' => 'id',
									'terms' => $single_category
								)
							),
							'numberposts' => -1
						);
						$products = get_posts( $args );
						if( $products ) {
							foreach( $products as $product )
								wp_delete_post( $product->ID, true );
						}
					}
				} else {
					$categories = get_terms( $term_taxonomy, array( 'hide_empty' => false ) );
					if( $categories ) {
						foreach( $categories as $category ) {
							wp_delete_term( $category->term_id, $term_taxonomy );
							$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = %d", $category->term_id ) );
							$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->term_relationships . "` WHERE `term_taxonomy_id` = %d", $category->term_taxonomy_id ) );
							$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "jigoshop_termmeta` WHERE `jigoshop_term_id` = %d", $category->term_id ) );
							delete_metadata( 'jigoshop_term', $category->term_id, 'thumbnail_id' );
						}
						update_option( $term_taxonomy . '_children', '' );
					}
				}
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$tags = get_terms( $term_taxonomy, array( 'hide_empty' => false ) );
				if( $tags ) {
					foreach( $tags as $tag ) {
						wp_delete_term( $tag->term_id, $term_taxonomy );
						$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = %d", $tag->term_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->term_relationships . "` WHERE `term_taxonomy_id` = %d", $tag->term_id ) );
					}
				}
				break;

			case 'images':
				$post_type = 'product';
				$products = (array)get_posts( array(
					'post_type' => $post_type,
					'post_status' => jigo_st_post_statuses(),
					'numberposts' => -1
				) );
				if( $products ) {
					$upload_dir = wp_upload_dir();
					foreach( $products as $product ) {
						$args = array(
							'post_type' => 'attachment',
							'post_parent' => $product->ID,
							'post_status' => 'inherit',
							'post_mime_type' => 'image',
							'numberposts' => -1
						);
						$images = get_children( $args );
						if( $images ) {
							foreach( $images as $image ) {
								wp_delete_attachment( $image->ID, true );
							}
							unset( $images, $image );
						}
					}
				}
				break;

			case 'orders':
				$post_type = 'shop_order';
				$term_taxonomy = 'shop_order_status';
				if( $data ) {
					foreach( $data as $single_order ) {
						$args = array(
							'post_type' => $post_type,
							'tax_query' => array(
								array(
									'taxonomy' => $term_taxonomy,
									'field' => 'id',
									'terms' => $single_order
								)
							),
							'numberposts' => -1
						);
						$orders = get_posts( $args );
						if( $orders ) {
							foreach( $orders as $order )
								wp_delete_post( $order->ID, true );
						}
					}
				} else {
					$orders = (array)get_posts( array(
						'post_type' => $post_type,
						'post_status' => jigo_st_post_statuses(),
						'numberposts' => -1
					) );
					if( $orders ) {
						foreach( $orders as $order ) {
							if( isset( $order->ID ) )
								wp_delete_post( $order->ID, true );
						}
						$wpdb->query( "DELETE FROM `" . $wpdb->prefix . "jigoshop_downloadable_product_permissions`" );
					}
				}
				break;

			case 'coupons':
				$post_type = 'shop_coupon';
				$coupons = (array)get_posts( array(
					'post_type' => $post_type,
					'post_status' => jigo_st_post_statuses(),
					'numberposts' => -1
				) );
				if( $coupons ) {
					foreach( $coupons as $coupon ) {
						if( isset( $coupon->ID ) )
							wp_delete_post( $coupon->ID, true );
					}
				}
				break;

			case 'attributes':
				if( !isset( $_POST['jigo_st_attributes'] ) ) {
					$attributes_sql = "SELECT `attribute_id` as ID, `attribute_name` as name, `attribute_label` as label, `attribute_type` as type FROM `" . $wpdb->prefix . "jigoshop_attribute_taxonomies`";
					$attributes = $wpdb->get_results( $attributes_sql );
					if( $attributes ) {
						foreach( $attributes as $attribute ) {
							$terms_sql = $wpdb->prepare( "SELECT `term_id` FROM `" . $wpdb->prefix . "term_taxonomy` WHERE `taxonomy` = 'pa_%s'", $attribute->name );
							$terms = $wpdb->get_results( $terms_sql );
							if( $terms ) {
								foreach( $terms as $term )
									wp_delete_term( $term->term_id, 'pa_' . $attribute->name );
							}
							$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "jigoshop_termmeta` WHERE `meta_key` = 'order_%s'", $attribute->name ) );
							$wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->term_relationships . "` WHERE `term_taxonomy_id` = %d", $attribute->ID ) );
						}
					}
					$wpdb->query( "DELETE FROM `" . $wpdb->prefix . "jigoshop_attribute_taxonomies`" );
				}
				break;

			// 3rd Party

			case 'credit-cards':
				$post_type = 'offline_payment';
				$credit_cards = (array)get_posts( array( 
					'post_type' => $post_type,
					'post_status' => jigo_st_post_statuses(),
					'numberposts' => -1
				) );
				if( $credit_cards ) {
					foreach( $credit_cards as $credit_card ) {
						if( isset( $credit_card->ID ) )
							wp_delete_post( $credit_card->ID, true );
					}
				}
				break;

			// WordPress

			case 'posts':
				$post_type = 'post';
				$posts = (array)get_posts( array( 
					'post_type' => $post_type,
					'post_status' => jigo_st_post_statuses(),
					'numberposts' => -1
				) );
				if( $posts ) {
					foreach( $posts as $post ) {
						if( isset( $post->ID ) )
							wp_delete_post( $post->ID, true );
					}
				}
				break;

			case 'post_categories':
				$term_taxonomy = 'category';
				$post_categories = get_terms( $term_taxonomy, array( 'hide_empty' => false ) );
				if( $post_categories ) {
					foreach( $post_categories as $post_category ) {
						wp_delete_term( $post_category->term_id, $term_taxonomy );
						$wpdb->query( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = " . $post_category->term_id );
						$wpdb->query( "DELETE FROM `" . $wpdb->term_relationships . "` WHERE `term_taxonomy_id` = " . $post_category->term_taxonomy_id );
					}
				}
				$wpdb->query( "DELETE FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'" );
				break;

			case 'post_tags':
				$term_taxonomy = 'post_tag';
				$post_tags = get_terms( $term_taxonomy, array( 'hide_empty' => false ) );
				if( $post_tags ) {
					foreach( $post_tags as $post_tag ) {
						wp_delete_term( $post_tag->term_id, $term_taxonomy );
						$wpdb->query( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = " . $post_tag->term_id );
						$wpdb->query( "DELETE FROM `" . $wpdb->term_relationships . "` WHERE `term_taxonomy_id` = " . $post_tag->term_taxonomy_id );
					}
				}
				$wpdb->query( "DELETE FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'" );
				break;

			case 'links':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "links`" );
				break;

			case 'comments':
				$comments = get_comments();
				if( $comments ) {
					foreach( $comments as $comment ) {
						if( $comment->comment_ID )
							wp_delete_comment( $comment->comment_ID, true );
					}
				}
				break;

		}

	}

	function jigo_st_remove_filename_extension( $filename ) {

		$extension = strrchr( $filename, '.' );
		$filename = substr( $filename, 0, -strlen( $extension ) );

		return $filename;

	}

	function jigo_st_post_statuses() {

		$output = array(
			'publish',
			'pending',
			'draft',
			'auto-draft',
			'future',
			'private',
			'inherit',
			'trash'
		);
		return $output;

	}

	function jigo_st_admin_active_tab( $tab_name = null, $tab = null ) {

		if( isset( $_GET['tab'] ) && !$tab )
			$tab = $_GET['tab'];
		else
			$tab = 'overview';

		$output = '';
		if( isset( $tab_name ) && $tab_name ) {
			if( $tab_name == $tab )
				$output = ' nav-tab-active';
		}
		echo $output;

	}

	function jigo_st_tab_template( $tab = '' ) {

		if( !$tab )
			$tab = 'overview';

		switch( $tab ) {

			case 'nuke':
				$products = jigo_st_return_count( 'products' );
				$images = jigo_st_return_count( 'images' );
				$tags = jigo_st_return_count( 'tags' );
				$categories = jigo_st_return_count( 'categories' );
				if( $categories ) {
					$term_taxonomy = 'product_cat';
					$args = array(
						'hide_empty' => 0
					);
					$categories_data = get_terms( $term_taxonomy, $args );
				}
				$orders = jigo_st_return_count( 'orders' );
				if( $orders ) {
					$term_taxonomy = 'shop_order_status';
					$args = array(
						'hide_empty' => 0
					);
					$orders_data = get_terms( $term_taxonomy, $args );
				}
				$coupons = jigo_st_return_count( 'coupons' );

				$credit_cards = jigo_st_return_count( 'credit-cards' );
				$attributes = jigo_st_return_count( 'attributes' );

				$posts = jigo_st_return_count( 'posts' );
				$post_categories = jigo_st_return_count( 'post_categories' );
				$post_tags = jigo_st_return_count( 'post_tags' );
				$links = jigo_st_return_count( 'links' );
				$comments = jigo_st_return_count( 'comments' );

				if( $products || $images || $tags || $categories || $orders || $credit_cards || $attributes )
					$show_table = true;
				else
					$show_table = false;
				break;

		}
		if( $tab )
			include_once( JIGO_ST_PATH . 'templates/admin/tabs-' . $tab . '.php' );

	}

	function jigo_st_convert_sale_status( $sale_status ) {

		$output = '';
		if( $sale_status ) {
			switch( $sale_status ) {

				case 'cancelled':
					$output = __( 'Cancelled', 'jigo_st' );
					break;

				case 'completed':
					$output = __( 'Completed', 'jigo_st' );
					break;

				case 'on-hold':
					$output = __( 'On-Hold', 'jigo_st' );
					break;

				case 'pending':
					$output = __( 'Pending', 'jigo_st' );
					break;

				case 'processing':
					$output = __( 'Processing', 'jigo_st' );
					break;

				case 'refunded':
					$output = __( 'Refunded', 'jigo_st' );
					break;
			}
		}
		return $output;

	}

	function wpsc_st_return_percentage( $after = 0, $before = 0, $display_html = true ) {

		$output = 0;
		if( (int)$after <> 0 || (int)$before <> 0 ) {
			$output = (int)( ( ( $after / $before ) * 100 ) - 100 );
			if( $display_html && (int)$output > 0 )
				$output = '+' . $output;
		}
		return $output;

	}

	function wpsc_st_percentage_symbol_class( $after = 0, $before = 0 ) {

		$output = '';
		$percentage = wpsc_st_return_percentage( $after, $before, false );
		if( $percentage < 0 ) {
			$output = 'down';
		} else if( $percentage > 0 ) {
			$output = 'up';
		} else {
			$output = 'line';
		}
		$output = ' class="' . $output . '"';
		return $output;

	}

	/* End of: WordPress Administration */

}

function jigo_st_get_total_unlinked_sales() {

	global $wpdb;

	$output = 0;
	$sales_sql = "SELECT COUNT(meta_id) FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = 'customer_user' AND `meta_value` = 0";
	$sales = $wpdb->get_var( $sales_sql );
	if( $sales )
		$output = $sales;
	return $output;

}

function jigo_st_get_unlinked_sales( $rows = 0, $offset = 0 ) {

	$output = array();
	$post_type = 'shop_order';
	if( $rows ) {
		$args = array(
			'post_type' => $post_type,
			'meta_key' => 'customer_user',
			'meta_value' => 0,
			//'numberposts' => -1
			'numberposts' => $rows,
			'offset' => $offset
		);
	} else {
		$args = array(
			'post_type' => $post_type,
			'meta_key' => 'customer_user',
			'meta_value' => 0,
			'numberposts' => -1
		);
	}
	$sales = get_posts( $args );
	if( $sales )
		$output = $sales;
	return $output;

}

function jigo_st_get_email_from_sale( $purchase_id = '' ) {

	global $wpdb;

	$output = '';
	if( $purchase_id ) {
		$order_data = get_post_meta( $purchase_id, 'order_data', true );
		$sale_email = $order_data['billing_email'];
		if( $sale_email )
			$output = $sale_email;
	}
	return $output;

}
?>