<?php if( $orders ) { ?>
<ul class="recent-orders">
	<?php foreach( $orders as $order ) { ?>
<?php
		$this_order = new jigoshop_order( $order->ID );
		$total_items = 0;
		foreach ( $this_order->items as $index => $item )
			$total_items += $item['qty'];
?>
	<li>
		<span class="order-status <?php echo sanitize_title( $this_order->status ); ?>"><?php echo ucwords( __( $this_order->status, 'jigoshop' ) ); ?></span> <a href="<?php echo admin_url( 'post.php?post=' . $order->ID ); ?>&action=edit"><?php echo get_the_time( __( 'l jS \of F Y h:i:s A', 'jigoshop' ), $order->ID ); ?></a><br />
		<small><?php echo sizeof( $this_order->items ); ?> <?php echo _n( 'Item', 'Items', sizeof( $this_order->items ), 'jigoshop' ); ?> (<?php echo __( 'Total Quantity', 'jigoshop' ); ?> <?php echo $total_items; ?>) <span class="order-cost"><?php echo __( 'Total: ', 'jigoshop' ); ?><?php echo jigoshop_price( $this_order->order_total ); ?></span></small>
	</li>
	<?php } ?>
</ul>
<?php } ?>