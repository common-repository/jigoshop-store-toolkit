var $j = jQuery.noConflict();

$j(function () {
	$j('#jigoshop-checkall').click(function () {
		$j('#empty-jigoshop-tables').find(':checkbox').attr('checked', true);
	});
	$j('#jigoshop-uncheckall').click(function () {
		$j('#empty-jigoshop-tables').find(':checkbox').attr('checked', false);
	});

	$j('#wordpress-checkall').click(function () {
		$j('#empty-wordpress-tables').find(':checkbox').attr('checked', true);
	});
	$j('#wordpress-uncheckall').click(function () {
		$j('#empty-wordpress-tables').find(':checkbox').attr('checked', false);
	});
});