<div class="jigoshop_order_downloads_wrapper">
	<table cellpadding="0" cellspacing="0" class="jigoshop_order_downloads">
		<thead>
			<tr>
				<th class="product-id"><?php _e('ID', 'jigoshop'); ?></th>
				<th class="variation-id"><?php _e('Variation ID', 'jigoshop'); ?></th>
				<th class="product-sku"><?php _e('SKU', 'jigoshop'); ?></th>
				<th class="name"><?php _e('Name', 'jigoshop'); ?></th>
				<th class="variation"><?php _e('Variation', 'jigoshop'); ?></th>
				<th class="customer-id"><?php _e('Customer ID', 'jigoshop'); ?></th>
				<th class="customer"><?php _e('Customer', 'jigoshop'); ?></th>
				<th class="remaining"><?php _e('Downloads Remaining', 'jigoshop'); ?></th>
				<th class="center" width="1%"><?php _e('Remove', 'jigoshop'); ?></th>
			</tr>
		</thead>
		<tbody id="order_downloads_list">

			<?php if ( sizeof( $order_downloads ) > 0 ) foreach ( $order_downloads as $item ) :

				if ( wp_get_post_parent_id( $item->product_id ) ) {
					$_product = new jigoshop_product_variation( $item->product_id );
                } else {
					$_product = new jigoshop_product( $item->product_id );
                }

				$customer = get_userdata( $item->user_id );

				?>
				<tr class="item">
					<td class="product-id"><?php echo $item->product_id; ?></td>
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
                        <input type="text" name="jigo_st_remaining[]" placeholder="" size="3" value="<?php echo esc_attr( $item->downloads_remaining ); ?>" />
                    </td>
					<td class="center">
						<input type="hidden" name="jigo_st_item_id[]" value="<?php echo esc_attr( $item->product_id ); ?>" />
						<input type="hidden" name="jigo_st_item_name[]" value="<?php echo esc_attr( $_product->get_title() ); ?>" />
                        <input type="hidden" name="jigo_st_item_variation_id[]" value="<?php if ( isset( $_product->variation_id ) ) echo $_product->variation_id; else echo ''; ?>" />
						<input type="hidden" name="jigo_st_customer_id[]" value="<?php echo esc_attr( $customer->ID ); ?>" />
						<input type="hidden" name="jigo_st_customer_email[]" value="<?php echo esc_attr( $customer->user_email ); ?>" />
						<button type="button" class="remove_row button">&times;</button>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<p class="buttons">
	<input type='text' class='item_id' name='order_downloads_product_select' id='order_downloads_product_select' value='' placeholder="<?php _e('Choose a Product', 'jigoshop'); ?>" />
	<script type="text/javascript">
		jQuery(function($) {
			jQuery("#order_downloads_product_select").select2({
				minimumInputLength: 3,
				multiple: false,
				closeOnSelect: true,
				ajax: {
					url: "<?php echo (!is_ssl()) ? str_replace('https', 'http', admin_url('admin-ajax.php')) : admin_url('admin-ajax.php'); ?>",
					dataType: 'json',
					quietMillis: 100,
					data: function(term, page) {
						return {
							term:       term,
							action:     'jigoshop_json_search_products_and_variations',
							security:   '<?php echo wp_create_nonce( "search-products" ); ?>'
						};
					},
					results: function( data, page ) {
						return { results: data };
					}
				},
				initSelection: function( element, callback ) {
					var stuff = {
						action:     'jigoshop_json_search_products_and_variations',
						security:   '<?php echo wp_create_nonce( "search-products" ); ?>',
						term:       element.val()
					};
					jQuery.ajax({
						type: 		'GET',
						url:        "<?php echo (!is_ssl()) ? str_replace('https', 'http', admin_url('admin-ajax.php')) : admin_url('admin-ajax.php'); ?>",
						dataType: 	"json",
						data: 		stuff,
						success: 	function( result ) {
							callback( result );
						}
					});
				}
			});

			$('button.add_shop_order_download').click(function(e) {
				e.preventDefault();
				var item_id = $("#order_downloads_product_select").val();
				if (item_id) {
					$('table.jigoshop_order_downloads').block({ message: null, overlayCSS: { background: '#fff url(' + jigoshop_params.assets_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
	
					var data = {
						action: 		'jigo_st_add_order_download',
						item_to_add: 	item_id,
						customer_user:	$('#customer_user').val()
					};
	
					$.post( jigoshop_params.ajax_url, data, function(response) {
	
						$('table.jigoshop_order_downloads tbody#order_downloads_list').append( response );
						$('table.jigoshop_order_downloads').unblock();
						$("#order_downloads_product_select").select2('val', '');
						$("#order_downloads_product_select").css('border-color', '');
	
					});
	
				} else {
					$("#order_downloads_product_select").css('border-color', 'red');
				}
			});

			$(document.body).on('click', '#order_downloads_list button.remove_row', function(e) {
				e.preventDefault();
				var answer = confirm( '<?php _e( 'Remove this item? The user will no longer be able to download it.' ); ?>' );
				if (answer){
					$(this).parent().parent().remove();
				}
			});

		});
	</script>

	<style type="text/css">
		#jigoshop-order-downloads .inside {
			margin: 0;
			padding: 0;
			background: #fefefe;
		}
		#jigoshop-order-downloads .remaining input {
			width: 115px;
		}
		#jigoshop-order-downloads .buttons {
			float: left;
			padding-left: 6px;
			vertical-align: top;
		}
		#jigoshop-order-downloads .buttons .item_id {
			width: 300px;
			vertical-align: top;
		}
		.jigoshop_order_downloads_wrapper {
			overflow: auto;
			margin: 0;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads {
			width: 100%;
			background: #fff;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads thead th {
			background: #ECECEC;
			font-size: 11px;
			text-align: left;
			padding: 8px 10px;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads td {
			text-align: left;
			vertical-align: middle;
			border-bottom: 1px dotted #ececec;
			padding: 8px 10px;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads td select {
			width: 50%;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads .name {
			min-width: 100px;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads td input,
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads td textarea {
			width: 100%;
			font-size: 14px;
			color: #555;
			padding: 4px;
		}
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads .center,
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads .variation-id,
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads .product-id,
		.jigoshop_order_downloads_wrapper table.jigoshop_order_downloads .product-sku {
			text-align:center;
		}
		.admin-color-classic .jigoshop_order_downloads_wrapper table.jigoshop_order_downloads thead th {
			background-color:#D1E5EE;
		}
		
	</style>

	<button type="button" class="button button-primary add_shop_order_download"><?php _e('Add item', 'jigoshop'); ?></button>

</p>

<div class="clear"></div>
