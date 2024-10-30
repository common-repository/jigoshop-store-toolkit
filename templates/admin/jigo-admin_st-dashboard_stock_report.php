<?php
$outofstock = array();
$lowinstock = array();

if( Jigoshop_Base::get_options()->get_option( 'jigoshop_manage_stock' ) == 'yes' ) {

	$lowstockamount = Jigoshop_Base::get_options()->get_option('jigoshop_notify_low_stock_amount');
	if (!is_numeric($lowstockamount)) $lowstockamount = 1;

	$nostockamount = Jigoshop_Base::get_options()->get_option('jigoshop_notify_no_stock_amount');
	if (!is_numeric($nostockamount)) $nostockamount = 1;

	$args = array(
		'post_type'	=> 'product',
		'post_status' => 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' => -1
	);
	$my_query = new WP_Query($args);
	if ($my_query->have_posts()) : while ($my_query->have_posts()) : $my_query->the_post();

		$_product = new jigoshop_product( $my_query->post->ID );
		if (!$_product->managing_stock()) continue;

		$thisitem = '<li><a href="'.get_edit_post_link($my_query->post->ID).'">'.$my_query->post->post_title.'</a></li>';

//				if ($_product->stock<=$nostockamount) :
		if ( ! $_product->is_in_stock( true ) ) :    /* compare against global no stock threshold */
			$outofstock[] = $thisitem;
			continue;
		endif;

		if ($_product->stock<=$lowstockamount) $lowinstock[] = $thisitem;

	endwhile; endif;
	wp_reset_query();

}

if (sizeof($lowinstock)==0) :
	$lowinstock[] = '<tr><td colspan="2">'.__('No products are low in stock.', 'jigoshop').'</td></tr>';
endif;
if (sizeof($outofstock)==0) :
	$outofstock[] = '<tr><td colspan="2">'.__('No products are out of stock.', 'jigoshop').'</td></tr>';
endif;
?>
<div id="jigoshop_low_stock" class="jigoshop_right_now">
	<div class="table table_content">
		<p class="sub"><?php _e( 'Low Stock', 'jigoshop' ); ?></p>
		<ol>
			<?php echo implode('', $lowinstock); ?>
		</ol>
	</div>
	<div class="table table_discussion">
		<p class="sub"><?php _e( 'Out of Stock/Backorders', 'jigoshop' ); ?></p>
		<ol>
			<?php echo implode( '', $outofstock ); ?>
		</ol>
	</div>
	<br class="clear"/>
</div>
<!-- #jigoshop_low_stock -->