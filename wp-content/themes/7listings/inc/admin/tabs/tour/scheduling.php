<?php
$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', get_post_type() ), true );

if ( empty( $resources ) )
{
	echo '<div class="message alert">';
	_e( '<strong>Enter Booking Resources!</strong><br>This tab is empty because you have to enter at least 1 Booking Resource.', '7listings' );
	echo '</div>';

	return;
}

$warning = '<div class="alert">' . __( '<strong>Warning!</strong><br>No Booking Resources or Prices Entered! Please enter details to use scheduling.', '7listings' ) . '</div>';
?>

<h2><?php _e( 'Seasonal Price Changes', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'High/Low Season Price Changes, schedule prices for specific times of the year.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php
$table_tpl     = '
	<table class="widefat seas from-to">
		<thead>
			<tr>
				<th id="seas-resource">' . __( 'Booking Resource', '7listings' ) . '</th>
				<th id="seas-price">' . __( 'Price', '7listings' ) . '</th>
				<th id="seas-from">' . __( 'From', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enter date when<br>the price change<br>STARTS', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="seas-to">' . __( 'To', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enter date when<br>the price change<br>ENDS', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="seas-fixed">$' . do_shortcode( '[tooltip content="' . __( 'Enter price change in $<br>Total ($$$)<br>Increase (+$$)<br>reduce (-$$)', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="seas-percentage">%%' . do_shortcode( '[tooltip content="' . __( 'Enter price change in percents<br>Total (XXX)<br>Increase (+XX)<br>reduce (-XX)', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="seas-new-price">' . __( '<span>Scheduled</span> Price', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'New price<br>for selected dates', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="seas-schedule">' . __( 'Scheduling', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Add (+)<br>Enable (I/O)<br>temporary price changes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
			</tr>
		</thead>
		<tbody>%s</tbody>
	</table>
';
$row_content   = '
	<td class="seas-resource">%s</td>
	<td class="seas-price">%s</td>
	<td class="seas-from"><input type="text" class="from datepicker" name="%s" value="%s" placeholder="-"></td>
	<td class="seas-to"><input type="text" class="to datepicker" name="%s" value="%s" placeholder="-"></td>
	<td class="seas-fixed">
		<div class="input-prepend">
			<span class="add-on">$</span>
			<input type="text" name="%s" value="%s" placeholder="00">
		</div>
	</td>
	<td class="seas-percentage">
		<div class="input-append">
			<input type="text" name="%s" value="%s" placeholder="00">
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
';
$tr_tpl        = '<tr class="%s" data-resource="%s" data-type="%s">' . $row_content . '</tr>';
$upsell_tr_tpl = '<tr class="%s hidden" data-resource="%s" data-upsell="%s">' . $row_content . '</tr>';
$prices        = array(
	'adult'  => __( 'Adults', '7listings' ),
	'child'  => __( 'Children', '7listings' ),
	'senior' => __( 'Seniors', '7listings' ),
	'family' => __( 'Families', '7listings' ),
	'infant' => __( 'Infants', '7listings' ),
);

$season    = get_post_meta( get_the_ID(), 'season', true );
$trs       = '';
$all_found = false;
$default = array(
	'from'       => '',
	'to'         => '',
	'fixed'      => '',
	'percentage' => '',
	'source'     => '',
	'enable'     => 0,
);
foreach ( $resources as $index => $resource )
{
	$trs .= '<tr class="title">';
	$trs .= "<td colspan='8' class='seas-resource title'><b>{$resource['title']}</b></td>";
	$trs .= '</tr>';

	$found = false;
	foreach ( $prices as $type => $label )
	{
		$price_type = "price_{$type}";
		if ( empty( $resource[$price_type] ) )
			continue;

		// If no values, show an empty row
		$values = isset( $season[$index][$type] ) ? $season[$index][$type] : array( $default );

		$k     = 0;
		$total = count( $values ) - 1;
		foreach ( $values as $value )
		{
			$value = array_merge( $default, $value );
			$price = sl_calculate_seasonal_price( $resource[$price_type], $value );

			// HTML name for inputs
			$name = "season[$index][$type][$k]";

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
				$type,
				"<span>&dash; $label</span>",
				"<span>{$resource[$price_type]}</span>",
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

		$found = true;
	}

	// If no prices are entered, show warning and go to the next resource
	if ( ! $found )
	{
		$trs .= "<tr><td colspan='8'>{$warning}</tr>";
		continue;
	}

	// We know that there is at least one resource has prices
	$all_found = true;

	// Adding upsells schedule
	if ( empty( $resource['upsells'] ) || empty( $resource['upsell_items'] ) )
		continue;

	// The heading line with arrow to show/hide upsell schedules
	$trs .= '<tr><td colspan="8" class="seas-upsells title upsells-title"><span class="dashicons dashicons-arrow-down"></span>' . __( 'Upsells', '7listings' ) . '</tr>';

	foreach ( $resource['upsell_items'] as $k => $label )
	{
		// If no values, show an empty row
		$values = isset( $season[$index] ) && isset( $season[$index]['upsells'][$k] ) ? $season[$index]['upsells'][$k] : array( $default );

		$j            = 0;
		$total        = count( $values ) - 1;
		$upsell_price = $resource['upsell_prices'][$k];
		foreach ( $values as $value )
		{
			$value = array_merge( $default, $value );
			$price = sl_calculate_seasonal_price( $upsell_price, $value );

			// HTML name for inputs
			$name = "season[$index][upsells][$k][$j]";

			// CSS classes for row
			$classes = array( 'upsell' );
			if ( $j )
				$classes[] = 'secondary';
			if ( ! $value['enable'] )
				$classes[] = 'disabled';

			// Note: wrap price label & number in <span> to hide them in secondary row but keep its value for referencing
			$trs .= sprintf(
				$upsell_tr_tpl,
				implode( ' ', $classes ),
				$index,
				$k,
				"<span>&dash; $label</span>",
				"<span>$upsell_price</span>",
				"{$name}[from]", $value['from'],
				"{$name}[to]", $value['to'],
				"{$name}[fixed]", $value['fixed'],
				"{$name}[percentage]", $value['percentage'],
				$price, "{$name}[source]", $value['source'],
				"{$name}[enable]", "{$name}[enable]", checked( $value['enable'], 1, false ), "{$name}[enable]",
				$j == $total ? '' : 'hidden',
				$j == $total ? 'hidden' : ''
			);

			$j ++;
		}
	}
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
				<th id="perm-change-date">' . __( 'Change Date', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enter date<br>when the price changes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="perm-new-price">' . __( 'New Price', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enter new price', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="perm-schedule">' . __( 'Scheduling', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enable (I/O)<br>permanent price changes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
			</tr>
		</thead>
		<tbody>%s</tbody>
	</table>
';
$tr_tpl    = '
	<tr%s>
		<td class="perm-resource"><span>%s</span></td>
		<td class="perm-price"><span>%s</span></td>
		<td class="perm-change-date"><input type="text" class="datepicker" name="%s" value="%s" placeholder="-"></td>
		<td class="perm-new-price">
			<div class="input-prepend">
				<span class="add-on">$</span>
				<input type="number" class="price" name="%s" value="%s"  placeholder="00">
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
$prices    = array(
	'adult'  => __( 'Adults', '7listings' ),
	'child'  => __( 'Children', '7listings' ),
	'senior' => __( 'Seniors', '7listings' ),
	'family' => __( 'Families', '7listings' ),
	'infant' => __( 'Infants', '7listings' ),
);

$schedule  = get_post_meta( get_the_ID(), 'schedule', true );
$trs       = '';
$all_found = false;
$default   = array(
	'date'   => '',
	'price'  => '',
	'enable' => '',
);
foreach ( $resources as $index => $resource )
{
	$trs .= '<tr class="title">';
	$trs .= "<td colspan='5' class='perm-resource title'><b>{$resource['title']}</b></td>";
	$trs .= '</tr>';

	$found = false;

	foreach ( $prices as $type => $label )
	{
		$price_type = "price_{$type}";
		if ( empty( $resource[$price_type] ) )
			continue;

		$name  = "schedule[$index][$type]";
		$value = isset( $schedule[$index][$type] ) ? $schedule[$index][$type] : array();
		$value = array_merge( $default, $value );

		$trs .= sprintf(
			$tr_tpl,
			$value['enable'] ? '' : ' class="disabled"',
			"&dash; $label",
			$resource[$price_type],
			"{$name}[date]", $value['date'],
			"{$name}[price]", $value['price'],
			"{$name}[enable]", "{$name}[enable]", checked( $value['enable'], 1, false ), "{$name}[enable]"
		);

		$found = true;
	}

	if ( ! $found )
		$trs .= "<tr><td colspan='8'>{$warning}</tr>";
	else
		$all_found = true;

	// Adding upsells schedule
	if ( empty( $resource['upsells'] ) || empty( $resource['upsell_items'] ) )
		continue;

	// The heading line with arrow to show/hide upsell schedules
	$trs .= '<tr><td colspan="8" class="perm-upsells title upsells-title"><span class="dashicons dashicons-arrow-down"></span>' . __( 'Upsells', '7listings' ) . '</tr>';

	foreach ( $resource['upsell_items'] as $k => $label )
	{
		if ( empty( $resource['upsell_prices'][$k] ) )
			continue;

		$name  = "schedule[$index][upsells][$k]";
		$value = isset( $schedule[$index] ) && isset( $schedule[$index]['upsells'][$k] ) ? $schedule[$index]['upsells'][$k] : array();
		$value = array_merge( $default, $value );

		$trs .= sprintf(
			$tr_tpl,
			// data-upsell=-1 is used for Javascript to show/hide permanent schedules
			// It makes sure cloning seasonal schedules are not affected
			$value['enable'] ? ' data-upsell="-1" class="upsell hidden"' : ' class="upsell hidden disabled"',
			"<span>&dash; $label</span>",
			$resource['upsell_prices'][$k],
			"{$name}[date]", $value['date'],
			"{$name}[price]", $value['price'],
			"{$name}[enable]", "{$name}[enable]", checked( $value['enable'], 1, false ), "{$name}[enable]"
		);
	}
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
				<th id="allo-from">' . __( 'From', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enter date<br>when allocation change<br>STARTS', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="allo-to">' . __( 'To', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Enter date<br>when allocation change<br>ENDS', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="allo-new-allocation">' . __( '<span>Scheduled</span> Allocation', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'New allocation<br>for selected dates', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
				<th id="allo-schedule">' . __( 'Scheduling', '7listings' ) . do_shortcode( '[tooltip content="' . __( 'Add (+)<br>Enable (I/O)<br>allocation changes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ) . '</th>
			</tr>
		</thead>
		<tbody>%s</tbody>
	</table>
';
$tr_tpl    = '
	<tr class="%s" data-resource="%s">
		<td class="allo-resource">%s</td>
		<td class="allo-allocation">%s</td>
		<td class="allo-from"><input type="text" class="from datepicker" name="%s" value="%s" placeholder="-"></td>
		<td class="allo-to"><input type="text" class="to datepicker" name="%s" value="%s" placeholder="-"></td>
		<td class="allo-new-allocation">
			<div class="input-append">
				<input type="text" name="%s" value="%s" placeholder="00">
				<span class="add-on"><span class="dashicons dashicons-admin-users"></span></span>
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
	'allocation' => 0,
	'enable'     => 0,
);
foreach ( $resources as $index => $resource )
{
	// Show empty row if no values entered
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
