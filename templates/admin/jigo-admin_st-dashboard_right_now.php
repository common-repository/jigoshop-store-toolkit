<div id="jigoshop_right_now" class="jigoshop_right_now">
	<div class="table table_content">
		<p class="sub"><?php _e( 'Shop Content', 'jigoshop' ); ?></p>
		<table>
			<tbody>
				<tr class="first">
					<td class="first b"><a href="<?php echo add_query_arg( 'post_type', 'product', 'edit.php' ); ?>"><?php
						$num_posts = wp_count_posts( 'product' );
						$num = number_format_i18n( $num_posts->publish );
						echo $num;
					?></a></td>
					<td class="t"><a href="<?php echo add_query_arg( 'post_type', 'product', 'edit.php' ); ?>"><?php _e( 'Products', 'jigoshop' ); ?></a></td>
				</tr>
				<tr>
					<td class="first b"><a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_cat', 'post_type' => 'product' ), 'edit-tags.php' ); ?>"><?php
						echo wp_count_terms('product_cat');
					?></a></td>
					<td class="t"><a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_cat', 'post_type' => 'product' ), 'edit-tags.php' ); ?>"><?php _e( 'Product Categories', 'jigoshop' ); ?></a></td>
				</tr>
				<tr>
					<td class="first b"><a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_tag', 'post_type' => 'product' ), 'edit-tags.php' ); ?>"><?php
						echo wp_count_terms('product_tag');
					?></a></td>
					<td class="t"><a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_tag', 'post_type' => 'product' ), 'edit-tags.php' ); ?>"><?php _e( 'Product Tags', 'jigoshop' ); ?></a></td>
				</tr>
				<tr>
					<td class="first b"><a href="<?php echo add_query_arg( 'page', 'jigoshop_attributes', 'admin.php' ); ?>"><?php
						echo count( jigoshop_product::getAttributeTaxonomies());
					?></a></td>
					<td class="t"><a href="<?php echo add_query_arg( 'page', 'jigoshop_attributes', 'admin.php' ); ?>"><?php _e( 'Attributes', 'jigoshop' ); ?></a></td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- .table -->
	<div class="table table_discussion">
		<p class="sub"><?php _e( 'Orders', 'jigoshop' ); ?></p>
		<table>
			<tbody>
				<?php $jigoshop_orders = new jigoshop_orders(); ?>
				<tr class="first">
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'pending' ), 'edit.php' ); ?>"><span class="total-count"><?php echo $jigoshop_orders->pending_count; ?></span></a></td>
					<td class="last t"><a class="pending" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'pending' ), 'edit.php' ); ?>"><?php _e( 'Pending', 'jigoshop' ); ?></a></td>
				</tr>
				<tr>
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'on-hold' ), 'edit.php' ); ?>"><span class="total-count"><?php echo $jigoshop_orders->on_hold_count; ?></span></a></td>
					<td class="last t"><a class="onhold" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'on-hold' ), 'edit.php' ); ?>"><?php _e( 'On-Hold', 'jigoshop' ); ?></a></td>
				</tr>
				<tr>
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'processing' ), 'edit.php' ); ?>"><span class="total-count"><?php echo $jigoshop_orders->processing_count; ?></span></a></td>
					<td class="last t"><a class="processing" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'processing' ), 'edit.php' ); ?>"><?php _e( 'Processing', 'jigoshop' ); ?></a></td>
				</tr>
				<tr>
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'completed' ), 'edit.php' ); ?>"><span class="total-count"><?php echo $jigoshop_orders->completed_count; ?></span></a></td>
					<td class="last t"><a class="complete" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'completed' ), 'edit.php' ); ?>"><?php _e( 'Completed', 'jigoshop' ); ?></a></td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- .table -->
	<br class="clear"/>
	<div class="versions">
		<p id="wp-version-message"><?php _e( 'You are using', 'jigoshop' ); ?>
			<strong>JigoShop <?php echo jigoshop_get_plugin_data(); ?></strong>
		</p>
	</div>
	<!-- .versions -->
	<br class="clear"/>
</div>
<!-- #jigoshop_right_now -->