<h3><a href="<?php echo add_query_arg( 'tab', 'nuke' ); ?>"><?php _e( 'Nuke', 'jigo_st' ); ?></a></h3>
<p><?php _e( 'Permanently remove Jigoshop generated details from your WordPress database.', 'jigo_st' ); ?></p>
<ul class="ul-disc">
	<li><a href="<?php echo add_query_arg( 'tab', 'nuke' ); ?>#empty-jigoshop-tables"><?php _e( 'Empty Jigoshop Tables', 'jigo_st' ); ?></a></li>
	<li><a href="<?php echo add_query_arg( 'tab', 'nuke' ); ?>#empty-product-by-category"><?php _e( 'Empty Products by Product Category', 'jigo_st' ); ?></a></li>
	<li><a href="<?php echo add_query_arg( 'tab', 'nuke' ); ?>#delete-sales-by-sale-status"><?php _e( 'Delete Sales by Sale Status', 'jigo_st' ); ?></a></li>
	<li><a href="<?php echo add_query_arg( 'tab', 'nuke' ); ?>#empty-wordpress-tables"><?php _e( 'Empty WordPress Tables', 'jigo_st' ); ?></a></li>
</ul>

<h3><a href="<?php echo add_query_arg( 'tab', 'tools' ); ?>"><?php _e( 'Tools', 'jigo_st' ); ?></a></h3>
<p><?php _e( 'A growing set of commonly-used Jigoshop administration tools aimed at web developers and store maintainers.', 'jigo_st' ); ?></p>
<ul class="ul-disc">
	<li><?php _e( 'Re-link existing Sales from pre-registered Users', 'jigo_st' ); ?></li>
	<li><?php _e( 'Re-link File Downloads to User\'s My Account', 'jigo_st' ); ?></li>
	<li><?php _e( 'Un-link File Download\'s from deleted Users', 'jigo_st' ); ?></li>
</ul>