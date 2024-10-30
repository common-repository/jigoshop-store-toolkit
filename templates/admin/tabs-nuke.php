<ul class="subsubsub">
	<li><a href="#empty-jigoshop-tables"><?php _e( 'Empty Jigoshop Tables', 'jigo_st' ); ?></a> |</li>
	<li><a href="#empty-product-by-category"><?php _e( 'Empty Products by Product Category', 'jigo_st' ); ?></a> |</li>
	<li><a href="#delete-sales-by-sale-status"><?php _e( 'Delete Orders by Order Status', 'jigo_st' ); ?></a> |</li>
	<li><a href="#empty-wordpress-tables"><?php _e( 'Empty WordPress Tables', 'jigo_st' ); ?></a></li>
</ul>
<br class="clear" />
<h3><?php _e( 'Nuke Jigoshop', 'jigo_st' ); ?></h3>
<p><?php _e( 'Select the Jigoshop tables you wish to empty then click Remove to permanently remove Jigoshop generated details from your WordPress database.', 'jigo_st' ); ?></p>
<form method="post" onsubmit="showProgress()">
	<div id="poststuff">

		<div class="postbox" id="empty-jigoshop-tables">
			<h3 class="hndle"><?php _e( 'Empty Jigoshop Tables', 'jigo_st' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Permanently remove Jigoshop details.', 'jigo_st' ); ?></p>
				<p><a href="javascript:void(0)" id="jigoshop-checkall"><?php _e( 'Check All', 'jigo_st' ); ?></a> | <a href="javascript:void(0)" id="jigoshop-uncheckall"><?php _e( 'Uncheck All', 'jigo_st' ); ?></a></p>
				<fieldset>
					<table class="form-table">

						<tr>
							<th>
								<label for="products"><?php _e( 'Products', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="products" name="jigo_st_products"<?php echo disabled( $products, 0 ); ?> /> (<?php echo $products; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="product_categories"><?php _e( 'Product Categories', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="product_categories" name="jigo_st_product_categories"<?php echo disabled( $categories, 0 ); ?> /> (<?php echo $categories; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="product_tags"><?php _e( 'Product Tags', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="product_tags" name="jigo_st_product_tags"<?php echo disabled( $tags, 0 ); ?> /> (<?php echo $tags; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="product_images"><?php _e( 'Product Images', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="product_images" name="jigo_st_product_images"<?php echo disabled( $images, 0 ); ?> /> (<?php echo $images; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="attributes"><?php _e( 'Product Attributes', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="attributes" name="jigo_st_attributes"<?php echo disabled( $attributes, 0 ); ?> /> (<?php echo $attributes; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="sales_orders"><?php _e( 'Orders', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="sales_orders" name="jigo_st_sales_orders"<?php echo disabled( $orders, 0 ); ?> /> (<?php echo $orders; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="coupons"><?php _e( 'Coupons', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="coupons" name="jigo_st_coupons"<?php echo disabled( $coupons, 0 ); ?> /> (<?php echo $coupons; ?>)
							</td>
						</tr>

<?php if( isset( $credit_cards ) ) { ?>
						<tr>
							<th>
								<label for="creditcards"><?php _e( 'Credit Cards', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="creditcards" name="jigo_st_creditcards"<?php echo disabled( $credit_cards, 0 ); ?> /> (<?php echo $credit_cards; ?>)
								<span class="description"><?php echo sprintf( __( 'via %s', 'jigo_st' ), '<a href="http://www.visser.com.au/jigoshop/plugins/offline-credit-card-processing/" target="_blank">' . __( 'Offline Credit Card Processing', 'jigo_st' ) . '</a>' ); ?></span>
							</td>
						</tr>

<?php } ?>
					</table>
				</fieldset>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Remove', 'jigo_st' ); ?>" class="button-primary" />
				</p>
			</div>
		</div>
		<!-- .postbox -->

		<div class="postbox">
			<h3 class="hndle" id="empty-product-by-category"><?php _e( 'Empty Products by Product Category', 'jigo_st' ); ?></h3>
			<div class="inside">
<?php if( $categories ) { ?>
				<p><?php _e( 'Remove Products from specific Product Categories by selecting the Product Categories below, then click Remove to permanently remove those Products.', 'jigo_st' ); ?></p>
				<ul>
	<?php foreach( $categories_data as $category_single ) { ?>
					<li>
						<label>
							<input type="checkbox" name="jigo_st_categories[<?php echo $category_single->term_id; ?>]" value="<?php echo $category_single->term_id; ?>"<?php if( $category_single->count == 0 ) { ?> disabled="disabled"<?php } ?> />
							<?php echo $category_single->name; ?> (<?php echo $category_single->count; ?>)
						</label>
					</li>
	<?php } ?>
				</ul>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Remove', 'jigo_st' ); ?>" class="button-primary" />
				</p>
<?php } else { ?>
				<p><?php _e( 'No Categories have been created.', 'jigo_st' ); ?></p>
<?php } ?>
			</div>
		</div>
		<!-- .postbox -->

		<div class="postbox">
			<h3 class="hndle" id="delete-sales-by-sale-status"><?php _e( 'Delete Orders by Order Status', 'jigo_st' ); ?></h3>
			<div class="inside">
<?php if( $orders ) { ?>
				<p><?php _e( 'Remove Orders based on the Order Status by selecting the the Order Status below, then click Remove to permanently remove those Orders.', 'jigo_st' ); ?></p>
				<ul>
	<?php foreach( $orders_data as $order_single ) { ?>
					<li>
						<label>
							<input type="checkbox" name="jigo_st_orders[<?php echo $order_single->term_id; ?>]" value="<?php echo $order_single->term_id; ?>"<?php if( $order_single->count == 0 ) { ?> disabled="disabled"<?php } ?> />
							<?php echo jigo_st_convert_sale_status( $order_single->name ); ?> (<?php echo $order_single->count; ?>)
						</label>
					</li>
	<?php } ?>
				</ul>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Remove', 'jigo_st' ); ?>" class="button-primary" />
				</p>
<?php } else { ?>
				<p><?php _e( 'No Orders were found.', 'jigo_st' ); ?></p>
<?php } ?>
			</div>
		</div>
		<!-- .postbox -->

		<div class="postbox" id="empty-wordpress-tables">
			<h3 class="hndle"><?php _e( 'Empty WordPress Tables', 'jigo_st' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Permanently remove WordPress details.', 'jigo_st' ); ?></p>
				<p><a href="javascript:void(0)" id="wordpress-checkall"><?php _e( 'Check All', 'jigo_st' ); ?></a> | <a href="javascript:void(0)" id="wordpress-uncheckall"><?php _e( 'Uncheck All', 'jigo_st' ); ?></a></p>
				<fieldset>
					<table class="form-table">

						<tr>
							<th>
								<label for="posts"><?php _e( 'Posts', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="posts" name="jigo_st_posts"<?php echo disabled( $posts, 0 ); ?> /> (<?php echo $posts; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="post_categories"><?php _e( 'Post Categories', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="post_categories" name="jigo_st_post_categories"<?php echo disabled( $post_categories, 0 ); ?> /> (<?php echo $post_categories; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="post_tags"><?php _e( 'Post Tags', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="post_tags" name="jigo_st_post_tags"<?php echo disabled( $post_tags, 0 ); ?> /> (<?php echo $post_tags; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="links"><?php _e( 'Links', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="links" name="jigo_st_links"<?php echo disabled( $links, 0 ); ?> /> (<?php echo $links; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="comments"><?php _e( 'Comments', 'jigo_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="links" name="jigo_st_comments"<?php echo disabled( $comments, 0 ); ?> /> (<?php echo $comments; ?>)
							</td>
						</tr>

					</table>
				</fieldset>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Remove', 'jigo_st' ); ?>" class="button-primary" />
				</p>
			</div>
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->

	<input type="hidden" name="action" value="nuke" />
	<?php wp_nonce_field( 'nuke', 'jigo_st_nuke' ); ?>
</form>