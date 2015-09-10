<?php
$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', get_post_type() ), true );

if ( empty( $resources ) )
{
	echo '<div class="message alert">';
	_e( '<strong>Enter Booking Resources!</strong> This tab is empty because you have to enter at least 1 Booking Resource.', '7listings' );
	echo '</div>';

	return;
}

$warning = '<div class="alert">' . __( '<strong>Warning!</strong><br>No Booking Resources or Prices Entered! Please enter details to use scheduling.', '7listings' ) . '</div>';
?>

<h2><?php _e( 'Seasonal Price Changes', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'High/Low Season Price Changes, schedule prices for specific times of the year.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php
$table_tpl = '
	<table class="widefat seas from-to">
		<thead>
			<tr>
				<th id="seas-resource">' . __( 'Booking Resource', '7listings' ) . '</th>
				<th id="seas-price">' . __( 'Price', '7listings' ) . '</th>
				<th id="seas-from">' . __( 'From', '7listings' ) . '</th>
				<th id="seas-to">' . __( 'To', '7listings' ) . '</th>
				<th id="seas-fixed">$</th>
				<th id="seas-percentage">%%</th>
				<th id="seas-new-price">' . __( '<span>Scheduled</span> Price', '7listings' ) . '</th>
				<th id="seas-schedule">' . __( 'Scheduling', '7listings' ) . '</th>
			</tr>
		</thead>
		<tbody>%s</tbody>
	</table>
';
$tr_tpl    = '
	<tr class="%s" data-resource="%s">
		<td class="seas-resource">%s</td>
		<td class="seas-price">%s</td>
		<td class="seas-from"><input type="text" class="from datepicker" name="%s" value="%s"> <span class="calendar-big"></span></td>
		<td class="seas-to"><input type="text" class="to datepicker" name="%s" value="%s"> <span class="calendar-big"></span></td>
		<td class="seas-fixed">
			<div class="input-prepend">
				<span class="add-on">$</span>
				<input type="text" name="%s" value="%s">
			</div>
		</td>
		<td class="seas-percentage">
			<div class="input-append">
				<input type="text" name="%s" value="%s">
				<span class="add-on">%%</span>
			</div>
		</td>
		<td class="seas-new-price"><span>%s</span><input type="hidden" name="%s" value="%s"></td>
		<td class="seas-schedule">
			<span class="checkbox">
				<input type="checkbox" id="%s" name="%s" value="1" %s>
				<label for="%s">&nbsp;</label>
			</span>
			<a href="#" class="add-md add-schedule %s"></a>
			<a href="#" class="delete-md delete-schedule %s"></a>
		</td>
	</tr>
';

$season    = get_post_meta( get_the_ID(), 'season', true );
$trs       = '';
$all_found = false;
$default   = array(
	'from'       => '',
	'to'         => '',
	'fixed'      => '',
	'percentage' => '',
	'source'     => '',
	'enable'     => 0,
);
foreach ( $resources as $index => $resource )
{
	if ( empty( $resource['price'] ) )
		continue;

	// If no values, show an empty row
	$values = isset( $season[$index] ) ? $season[$index] : array( $default );

	$k     = 0;
	$total = count( $values ) - 1;
	foreach ( $values as $value )
	{
		$value = array_merge( $default, $value );

		$price = sl_calculate_seasonal_price( $resource['price'], $value );

		// HTML name for inputs
		$name = "season[$index][$k]";

		// CSS classes for row
		$classes = array();
		if ( $k )
			$classes[] = 'secondary';
		if ( ! $value['enable'] )
			$classes[] = 'disabled';

		// Note: wrap price label & number in <span> to hide them in secondary row but keep its value for referencing
		$trs .= sprintf(
			$tr_tpl,
			implode( ' ', $classes ),
			$index,
			"<span>{$resource['title']}</span>",
			"<span>{$resource['price']}</span>",
			"{$name}[from]", $value['from'],
			"{$name}[to]", $value['to'],
			"{$name}[fixed]", $value['fixed'],
			"{$name}[percentage]", $value['percentage'],
			$price, "{$name}[source]", $value['source'],
			"{$name}[enable]", "{$name}[enable]", checked( $value['enable'], 1, false ), "{$name}[enable]",
			$k == $total ? '' : 'hidden',
			$k == $total ? 'hidden' : ''
		);

		$k ++;
	}

	$all_found = true;
}
if ( $all_found )
	printf( $table_tpl, $trs );
else
	echo $warning;
?>

<h2><?php _e( 'Permanent Price Change', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Change rates permanently for a new season or year.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php
$table_tpl = '
	<table class="widefat perm">
		<thead>
			<tr>
				<th id="perm-resource">' . __( 'Booking Resource', '7listings' ) . '</th>
				<th id="perm-price">' . __( 'Price', '7listings' ) . '</th>
				<th id="perm-change-date">' . __( 'Change Date', '7listings' ) . '</th>
				<th id="perm-new-price">' . __( 'New Price', '7listings' ) . '</th>
				<th id="perm-schedule">' . __( 'Scheduling', '7listings' ) . '</th>
			</tr>
		</thead>
		<tbody>%s</tbody>
	</table>
';
$tr_tpl    = '
	<tr%s>
		<td class="perm-resource">%s</td>
		<td class="perm-price">%s</td>
		<td class="perm-change-date"><input type="text" class="datepicker" name="%s" value="%s"> <span class="calendar-big"></span></td>
		<td class="perm-new-price">
			<div class="input-prepend">
				<span class="add-on">$</span>
				<input type="text" name="%s" value="%s">
			</div>
		</td>
		<td class="perm-schedule">
			<span class="checkbox">
				<input type="checkbox" id="%s" name="%s" value="1" %s>
				<label for="%s">&nbsp;</label>
			</span>
		</td>
	</tr>
';

$schedule  = get_post_meta( get_the_ID(), 'schedule', true );
$trs       = '';
$all_found = false;
foreach ( $resources as $index => $resource )
{
	if ( empty( $resource['price'] ) )
		continue;

	$name  = "schedule[$index]";
	$value = isset( $schedule[$index] ) && isset( $schedule[$index] ) ? $schedule[$index] : array();
	$value = array_merge( array(
		'date'   => '',
		'price'  => '',
		'enable' => '',
	), $value );

	$trs .= sprintf(
		$tr_tpl,
		$value['enable'] ? '' : ' class="disabled"',
		"<span>{$resource['title']}</span>",
		$resource['price'],
		"{$name}[date]", $value['date'],
		"{$name}[price]", $value['price'],
		"{$name}[enable]", "{$name}[enable]", checked( $value['enable'], 1, false ), "{$name}[enable]"
	);

	$all_found = true;
}
if ( $all_found )
	printf( $table_tpl, $trs );
else
	echo $warning;
?>

<h2><?php _e( 'Allocation Changes', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Temporarily change allocation for tours, to allow for more or no bookings. To disable bookings set allocation to 0', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php
$table_tpl = '
	<table class="widefat allo from-to">
		<thead>
			<tr>
				<th id="allo-resource">' . __( 'Booking Resource', '7listings' ) . '</th>
				<th id="allo-allocation">' . __( 'Allocation', '7listings' ) . '</th>
				<th id="allo-from">' . __( 'From', '7listings' ) . '</th>
				<th id="allo-to">' . __( 'To', '7listings' ) . '</th>
				<th id="allo-new-allocation">' . __( '<span>Scheduled</span> Allocation', '7listings' ) . '</th>
				<th id="allo-schedule">' . __( 'Scheduling', '7listings' ) . '</th>
			</tr>
		</thead>
		<tbody>%s</tbody>
	</table>
';
$tr_tpl    = '
	<tr class="%s" data-resource="%s">
		<td class="allo-resource">%s</td>
		<td class="allo-allocation">%s</td>
		<td class="allo-from"><input type="text" class="from datepicker" name="%s" value="%s"> <span class="calendar-big"></span></td>
		<td class="allo-to"><input type="text" class="to datepicker" name="%s" value="%s"> <span class="calendar-big"></span></td>
		<td class="allo-new-allocation">
			<div class="input-append">
				<input type="text" name="%s" value="%s">
				<span class="add-on">' . __( '/night', '7listings' ) . '</span>
			</div>
		</td>
		<td class="allo-schedule">
			<span class="checkbox">
				<input type="checkbox" id="%s" name="%s" value="1" %s>
				<label for="%s">&nbsp;</label>
			</span>
			<a href="#" class="add-md add-schedule %s"></a>
			<a href="#" class="delete-md delete-schedule %s"></a>
		</td>
	</tr>
';

$allocations = get_post_meta( get_the_ID(), 'allocations', true );
$trs         = '';
$default     = array(
	'from'       => '',
	'to'         => '',
	'allocation' => '',
	'enable'     => 0,
);
foreach ( $resources as $index => $resource )
{
	$values = isset( $allocations[$index] ) ? $allocations[$index] : array( $default );

	$k     = 0;
	$total = count( $values ) - 1;
	foreach ( $values as $value )
	{
		$value = array_merge( $default, $value );

		// HTML name of inputs
		$name = "allocations[$index][$k]";

		// CSS classes for row
		$classes = array();
		if ( $k )
			$classes[] = 'secondary';
		if ( ! $value['enable'] )
			$classes[] = 'disabled';

		// Note: wrap allocation label & number in <span> to hide them in secondary row but keep its value for referencing
		$trs .= sprintf(
			$tr_tpl,
			implode( ' ', $classes ),
			$index,
			"<span>{$resource['title']}</span>",
			"<span>{$resource['allocation']}</span>",
			"{$name}[from]", $value['from'],
			"{$name}[to]", $value['to'],
			"{$name}[allocation]", $value['allocation'],
			"{$name}[enable]", "{$name}[enable]", checked( $value['enable'], 1, false ), "{$name}[enable]",
			$k == $total ? '' : 'hidden',
			$k == $total ? 'hidden' : ''
		);

		$k ++;
	}
}
printf( $table_tpl, $trs );
