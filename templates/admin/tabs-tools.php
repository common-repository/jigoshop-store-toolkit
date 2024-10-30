<h3><?php _e( 'Jigoshop Tools', 'jigo_st' ); ?></h3>
<p><?php _e( 'A growing set of commonly-used Jigoshop administration tools aimed at web developers and store maintainers.', 'jigo_st' ); ?></p>
<form method="post">

	<div id="poststuff">

		<div class="postbox">
			<h3 class="hndle"><?php _e( 'Tools', 'jigo_st' ); ?></h3>
			<div class="inside">
				<div>
					<a href="<?php echo add_query_arg( array( 'page' => 'jigo_st', 'action' => 'relink-existing-preregistered-sales', '_wpnonce' => wp_create_nonce( 'jigo_st_relink_existing_preregistered_sales' ) ) ); ?>"><?php _e( 'Re-link existing Sales from pre-registered Users', 'jigo_st' ); ?></a>
					<p class="description"><?php _e( 'This tool will attempt to re-link Sales with no User linked to existing Users, this is common where a customer makes a purchase then later registers for the site. Using this tool customer Sales will appear within My Account.', 'jigo_st' ); ?></p>
				</div>
				<div>
					<a href="<?php echo add_query_arg( array( 'page' => 'jigo_st', 'action' => 'relink-my_account-downloads', '_wpnonce' => wp_create_nonce( 'jigo_st_relink_my_account_downloads' ) ) ); ?>"><?php _e( 'Re-link File Downloads to User\'s My Account', 'jigo_st' ); ?></a>
					<p class="description"><?php _e( 'This tool will attempt to re-link File Downloads with no User linked to existing Users, this is common after migrating from another e-Commerce platform. Using this tool customer File Downloads will appear within My Account.', 'jigo_st' ); ?></p>
				</div>
				<div>
					<a href="<?php echo add_query_arg( array( 'page' => 'jigo_st', 'action' => 'unlink-deleted-user-downloads', '_wpnonce' => wp_create_nonce( 'jigo_st_unlink_deleted_user_downloads' ) ) ); ?>"><?php _e( 'Un-link File Download\'s from deleted Users', 'jigo_st' ); ?></a>
					<p class="description"><?php _e( 'This tool will attempt to un-link File Downloads from Users that no longer exist, this is common after migrating from another e-Commerce platform.', 'jigo_st' ); ?></p>
				</div>
				<div>
					<a href="<?php echo add_query_arg( array( 'page' => 'jigo_st', 'action' => 'delete-inactive-users', '_wpnonce' => wp_create_nonce( 'jigo_st_delete_inactive_users' ) ) ); ?>"><?php _e( 'Delete inactive/zombie Users', 'jigo_st' ); ?></a>
					<p class="description"><?php _e( 'This tool will attempt to delete WordPress Users that have made no approved activity (e.g. no comments/posts/reviews/topics/purchases).', 'jigo_st' ); ?></p>
				</div>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->

	<input type="submit" value="<?php _e( 'Save Changes', 'jigo_st' ); ?>" class="button-primary" />
	<input type="hidden" name="action" value="tools" />

</form>