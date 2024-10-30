<div class="stats" id="jigoshop-stats">
	<p>
		<?php if ($current_month_offset!=date('m')) : ?>
			<a href="admin.php?page=jigoshop&amp;month=<?php echo $current_month_offset+1; ?>" class="next"><?php _e('Next Month &rarr;','jigoshop'); ?></a>
		<?php endif; ?>
		<a href="admin.php?page=jigoshop&amp;month=<?php echo $current_month_offset-1; ?>" class="previous"><?php _e('&larr; Previous Month','jigoshop'); ?></a>
	</p>
	<div class="inside">
		<div id="placeholder" style="width:100%; height:300px; position:relative;"></div>
		<script type="text/javascript">
			/* <![CDATA[ */

			jQuery(function(){

				function weekendAreas(axes) {
					var markings = [];
					var d = new Date(axes.xaxis.min);
					// go to the first Saturday
					d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
					d.setUTCSeconds(0);
					d.setUTCMinutes(0);
					d.setUTCHours(0);
					var i = d.getTime();
					do {
						// when we don't set yaxis, the rectangle automatically
						// extends to infinity upwards and downwards
						markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
						i += 7 * 24 * 60 * 60 * 1000;
					} while (i < axes.xaxis.max);

					return markings;
				}

				<?php

					function orders_this_month( $where = '' ) {
						global $current_month_offset;

						$month = $current_month_offset;
						$year = (int) date('Y');

						$first_day = strtotime("{$year}-{$month}-01");
						$last_day = strtotime('-1 second', strtotime('+1 month', $first_day));

						$after = date('Y-m-d H:i:s', $first_day);
						$before = date('Y-m-d H:i:s', $last_day);

						$where .= " AND post_date >= '$after'";
						$where .= " AND post_date <= '$before'";

						return $where;
					}
					add_filter( 'posts_where', 'orders_this_month' );

					$args = array(
						'numberposts'     => -1,
						'orderby'         => 'post_date',
						'order'           => 'DESC',
						'post_type'       => 'shop_order',
						'post_status'     => 'publish' ,
						'suppress_filters'=> false
					);
					$orders = get_posts( $args );

					$order_counts = array();
					$order_amounts = array();

					// Blank date ranges to begin
					$month = $current_month_offset;
					$year = (int) date('Y');

					$first_day = strtotime("{$year}-{$month}-01");
					$last_day = strtotime('-1 second', strtotime('+1 month', $first_day));

					if ((date('m') - $current_month_offset)==0) :
						$up_to = date('d', strtotime('NOW'));
					else :
						$up_to = date('d', $last_day);
					endif;
					$count = 0;

					while ($count < $up_to) :

						$time = strtotime(date('Ymd', strtotime('+ '.$count.' DAY', $first_day))).'000';

						$order_counts[$time] = 0;
						$order_amounts[$time] = 0;

						$count++;
					endwhile;

					if ($orders) :
						foreach ($orders as $order) :

							$order_data = new jigoshop_order($order->ID);

							if ($order_data->status=='cancelled' || $order_data->status=='refunded') continue;

							$time = strtotime(date('Ymd', strtotime($order->post_date))) . '000';

							if (isset($order_counts[$time])) :
								$order_counts[$time]++;
							else :
								$order_counts[$time] = 1;
							endif;

							if (isset($order_amounts[$time])) :
								$order_amounts[$time] = $order_amounts[$time] + $order_data->order_total;
							else :
								$order_amounts[$time] = (float) $order_data->order_total;
							endif;

						endforeach;
					endif;

					remove_filter( 'posts_where', 'orders_this_month' );
				?>

				var d = [
					<?php
						$values = array();
						foreach ($order_counts as $key => $value) $values[] = "[$key, $value]";
						echo implode(',', $values);
					?>
				];

				for (var i = 0; i < d.length; ++i) d[i][0] += 60 * 60 * 1000;

				var d2 = [
					<?php
						$values = array();
						foreach ($order_amounts as $key => $value) $values[] = "[$key, $value]";
						echo implode(',', $values);
					?>
				];

				for (var i = 0; i < d2.length; ++i) d2[i][0] += 60 * 60 * 1000;

				var plot = jQuery.plot(jQuery("#placeholder"), [ { label: "<?php __('Number of sales','jigoshop'); ?>", data: d }, { label: "<?php __('Sales amount','jigoshop'); ?>", data: d2, yaxis: 2 } ], {
					series: {
						lines: { show: true },
						points: { show: true }
					},
					grid: {
						show: true,
						aboveData: false,
						color: '#ccc',
						backgroundColor: '#fff',
						borderWidth: 2,
						borderColor: '#ccc',
						clickable: false,
						hoverable: true,
						markings: weekendAreas
					},
					xaxis: {
						mode: "time",
						timeformat: "%d %b",
						tickLength: 1,
						minTickSize: [1, "day"]
					},
					yaxes: [ { min: 0, tickSize: 1, tickDecimals: 0 }, { position: "right", min: 0, tickDecimals: 2 } ],
					colors: ["#21759B", "#ed8432"]
				});

				function showTooltip(x, y, contents) {
					jQuery('<div id="tooltip">' + contents + '</div>').css( {
						position: 'absolute',
						display: 'none',
						top: y + 5,
						left: x + 5,
						border: '1px solid #fdd',
						padding: '2px',
						'background-color': '#fee',
						opacity: 0.80
					}).appendTo("body").fadeIn(200);
				}

				var previousPoint = null;
				jQuery("#placeholder").bind("plothover", function (event, pos, item) {
					if (item) {
						if (previousPoint != item.dataIndex) {
							previousPoint = item.dataIndex;

							jQuery("#tooltip").remove();

							if (item.series.label=="<?php __('Number of sales','jigoshop'); ?>") {

								var y = item.datapoint[1];
								showTooltip(item.pageX, item.pageY, item.series.label + " - " + y);

							} else {

								var y = item.datapoint[1].toFixed(2);
								showTooltip(item.pageX, item.pageY, item.series.label + " - <?php echo get_jigoshop_currency_symbol(); ?>" + y);

							}

						}
					}
					else {
						jQuery("#tooltip").remove();
						previousPoint = null;
					}
				});

			});

			/* ]]> */
		</script>
	</div>
	<!-- .inside -->
</div>
<!-- #jigoshop-stats -->