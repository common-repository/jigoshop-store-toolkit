<div id="jigoshop_sales_summary">
	<div class="table table_content table_top">
		<p><strong><?php _e( 'Sales Today', 'jigo_st' ); ?></strong></p>
		<p class="price"><?php echo get_jigoshop_currency_symbol() . $sales_today; ?> <span<?php echo wpsc_st_percentage_symbol_class( $sales_today, $sales_yesterday ); ?>><?php echo wpsc_st_return_percentage( $sales_today, $sales_yesterday ); ?>%</span></p>
	</div>
	<!-- .table -->
	<div class="table table_discussion table_top">
		<p><strong><?php _e( 'Sales Yesterday', 'jigo_st' ); ?></strong></p>
		<p class="price"><?php echo get_jigoshop_currency_symbol() . $sales_yesterday; ?></p>
	</div>
	<!-- .table -->
	<br class="clear" />

	<div class="table table_content">
		<p><strong><?php _e( 'Sales This Week', 'jigo_st' ); ?></strong></p>
		<p class="price"><?php echo get_jigoshop_currency_symbol() . $sales_week; ?> <span<?php echo wpsc_st_percentage_symbol_class( $sales_week, $sales_last_week ); ?>><?php echo wpsc_st_return_percentage( $sales_week, $sales_last_week ); ?>%</span></p>
	</div>
	<!-- .table -->
	<div class="table table_discussion">
		<p><strong><?php _e( 'Sales Last Week', 'jigo_st' ); ?></strong></p>
		<p class="price"><?php echo get_jigoshop_currency_symbol() . $sales_last_week; ?></p>
	</div>
	<!-- .table -->
	<br class="clear" />

	<div class="table table_content">
		<p><strong><?php _e( 'Sales This Month', 'jigo_st' ); ?></strong></p>
		<p class="price"><?php echo get_jigoshop_currency_symbol() . $sales_month; ?> <span<?php echo wpsc_st_percentage_symbol_class( $sales_month, $sales_last_month ); ?>><?php echo wpsc_st_return_percentage( $sales_month, $sales_last_month ); ?>%</span></p>
	</div>
	<!-- .table -->
	<div class="table table_discussion">
		<p><strong><?php _e( 'Sales Last Month', 'jigo_st' ); ?></strong></p>
		<p class="price"><?php echo get_jigoshop_currency_symbol() . $sales_last_month; ?></p>
	</div>
	<!-- .table -->
	<br class="clear" />

</div>
<!-- #jigoshop_sales_summary -->