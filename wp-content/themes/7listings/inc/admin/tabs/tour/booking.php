<?php
$post_type = 'tour';

$details = get_post_meta( get_the_ID(), sl_meta_key( 'booking', $post_type ), true );

// Fields templates
$title           = sprintf( '
	<div class="sl-settings resource-title">
		<div class="sl-label">
			<label>%s %s<span class="warning-lg required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" class="tour_detail_title" name="tour_detail_title_%%s" value="%%s" placeholder="%s">
		</div>
	</div>',
	__( 'Title', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Insert a title<br>for your tour or package', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ),
	sprintf( __( '%s item title', '7listings' ), ucwords( $post_type ) )
);
$desc            = sprintf( '
	<div class="sl-settings resource-description">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input sl-block">
			<textarea class="tour_detail_desc" name="tour_detail_desc_%%s" placeholder="%s">%%s</textarea>
		</div>
	</div>',
	__( 'Description', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Insert a description<br>for your tour or package', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ),
	sprintf( __( '%s item description', '7listings' ), ucwords( $post_type ) )
);
$upload          = sprintf( '
	<div class="sl-settings upload hidden">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<input type="hidden" class="booking_photos" name="booking_photo_ids_%%s[]">
			<img src="" class="hidden">
			<a href="#" class="choose-image hidden">&nbsp;</a>
			<a href="#" class="button delete-image hidden">%s</a>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label>&nbsp;</label>
		</div>
		<div class="sl-input">
			<a href="#" class="button add-file">%s</a>
		</div>
	</div>',
	__( 'Photo', '7listings' ),
	__( 'Delete', '7listings' ),
	__( 'Add Photo', '7listings' )
);
$img             = sprintf( '
	<li class="uploaded photo-detail-container" id="item_%%s">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<img src="%%s">
			<input type="text" class="tour_detail_photos" value="%%s" data-attachment_id="%%s" placeholder="%s">
			<a href="#" class="delete-file button" data-attachment_id="%%s" data-resource_id="%%s">%s</a>
		</div>
	</li>',
	__( 'Photo', '7listings' ),
	__( 'Photo description', '7listings' ),
	__( 'Delete', '7listings' )
);
$departure_types = Sl_Tour_Helper::get_departure_types();
$departures      = sprintf( '
	<div class="sl-sub-settings">
		<div class="sl-settings daily-departure" data-name="tour_detail_departure_type_%%s" data-value="daily">
			<div class="sl-label">
				<label class="double">%s <span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				%%s
				<a href="#" class="add-huge add-departure" title="%s">%s</a>
			</div>
		</div>
	</div>',
	__( 'Depart<br>Arrive', '7listings' ),
	__( 'Add Departure', '7listings' ),
	__( 'Add', '7listings' )
);
$departure_col   = sprintf( '
	<span class="col">
		<input type="text" class="daily-depart timepicker" name="tour_detail_depart_%%s[]" value="%%s" placeholder="Time"><br>
		<input type="text" class="daily-arrive timepicker" name="tour_detail_arrive_%%s[]" value="%%s" placeholder="Time"><br>
		<a href="#" class="delete-sm delete-departure %%s" title="%s">%s</a>
	</span>',
	__( 'Delete Departure', '7listings' ),
	__( 'Delete', '7listings' )
);
$by_day          = sprintf( '
	<div class="sl-settings departures-by-days-head checkbox-toggle">
		<div class="sl-label">
			<label>%%s %s</label>
		</div>
		<div class="sl-input">
			<span class="checkbox">
				<input type="checkbox" %%s class="day-%%s" id="tour_detail_departures_day_%%s_%%s">
				<label for="tour_detail_departures_day_%%s_%%s">&nbsp;</label>
			</span>
		</div>
	</div>
	<div class="sl-sub-settings">
		<div class="sl-settings departures-by-days-content">
			<div class="sl-label">
				<label>%s</label>
			</div>
			<div class="sl-input">
				%%s
				<a href="#" class="add-huge add-departure" title="%s">%s</a>
			</div>
		</div>
	</div>',
	do_shortcode( '[tooltip content="' . __( 'Turn ON to enter departure', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ),
	__( 'Depart<br>Arrive', '7listings' ),
	__( 'Add Departure', '7listings' ),
	__( 'Add', '7listings' )
);
$by_day_col      = sprintf( '
	<span class="col">
		<input type="text" class="%%s-depart timepicker" name="tour_detail_%%s_depart_%%s[]" value="%%s" placeholder="Time"><br>
		<input type="text" class="%%s-arrive timepicker" name="tour_detail_%%s_arrive_%%s[]" value="%%s" placeholder="Time"><br>
		<a href="#" class="delete-sm delete-departure %%s" title="%s">%s</a>
	</span>',
	__( 'Delete Departure', '7listings' ),
	__( 'Delete', '7listings' )
);
$prices          = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s <span class="warning-sm required right"></span></label>
		</div>
		<div class="sl-input">
			%%s
		</div>
	</div>',
	__( 'Price', '7listings' )
);
$price_box       = sprintf( '
	<div class="price-box tour">
		<span class="checkbox-toggle" data-effect="fade">
			<span class="checkbox">
				<input type="checkbox" %%s id="tour_detail_price_%%s_%%s">
				<label for="tour_detail_price_%%s_%%s">&nbsp;</label>
			</span>
			<span class="price-type">%%s</span>
		</span>
		<span class="input-prepend input-append">
			<span class="add-on">%s</span>
			<input type="number" step="any" min="0" class="small-text tour_detail_price_%%s" name="tour_detail_price_%%s_%%s" value="%%s" placeholder="%s">
			<span class="add-on">%%s</span>
		</span>
	</div>',
	Sl_Currency::symbol(),
	__( 'Price', '7listings' )
);
$lead_in_rate    = sprintf( '
	<div class="sl-settings lead-in-rate">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input">
			<select name="tour_detail_lead_in_rate_%%s" class="sl-input-small tour_detail_lead_in_rate">
				%%s
			</select>
		</div>
	</div>',
	__( 'Lead in rate', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Select a guest type if your tour is a family package or child tour and the other guest types are meant as extras', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' )
);
$allocation      = sprintf( '
	<div class="sl-settings allocation">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input">
			<span class="input-append">
				<input type="number" min="0" class="small-text tour_detail_allocation" name="tour_detail_allocation_%%s" value="%%s" placeholder="%s">
				<span class="add-on">%s</span>
			</span>
		</div>
	</div>',
	__( 'Allocation', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Enter amount of available seats on every departure/tour', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ),
	__( '#People', '7listings' ),
	__( 'guests', '7listings' )
);
$upsell_button   = sprintf( '
	<div class="sl-settings checkbox-toggle upsells">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input">
			<span class="checkbox">
				<input type="checkbox" %%s class="tour_detail_upsells" id="tour_detail_upsells_%%s" name="tour_detail_upsells_%%s">
				<label for="tour_detail_upsells_%%s">&nbsp;</label>
			</span>
		</div>
	</div>',
	__( 'Upsells', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Add upsells that customers can purchase with the tour during booking', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' )
);
$upsell_item     = sprintf( '
	<div class="sl-settings upsell">
		<div class="sl-label">
			<label><span class="warning-sm required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" class="tour_detail_upsells_item %%s" name="tour_detail_upsells_item_%%s[%%s]" value="%%s" placeholder="%s">
			<span class="input-prepend">
				<span class="add-on">%s</span>
				<input type="number" step="any" min="0" class="small-text tour_detail_upsells_price %%s" name="tour_detail_upsells_price_%%s[%%s]" value="%%s" placeholder="%s">
			</span>
			<a href="#" class="button delete-upsell-item %%s">%s</a>
			%%s
			%%s
		</div>
	</div>',
	__( 'Item description', '7listings' ),
	Sl_Currency::symbol(),
	__( 'Price', '7listings' ),
	__( 'Delete', '7listings' )
);

$multiplier_button = sprintf( '
	<span class="checkbox">
		<input type="checkbox" class="tour_detail_upsells_multiplier" id="tour_detail_upsells_multiplier_%%s_%%s" name="tour_detail_upsells_multiplier_%%s[%%s]" value="1" %%s>
		<label for="tour_detail_upsells_multiplier_%%s_%%s">&nbsp;</label>
	</span>
	<span class="description">%s</span> %s',
	__( 'Multiply with Guests', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Turn ON to multiply the upsell with all guests<br>example: extra night, transfer', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' )
);

$location    = sprintf( '
	<div class="sl-settings tour-location checkbox-toggle">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="checkbox">
				<input type="checkbox" %%s class="tour_detail_location" id="tour_detail_location_%%s" name="tour_detail_location_%%s" value="1">
				<label for="tour_detail_location_%%s">&nbsp;</label>
			</span>
		</div>
	</div>',
	__( 'Tour Location', '7listings' )
);
$marker      = sprintf( '
	<div class="sl-sub-settings">
		<div class="sl-settings">
			<div class="sl-label">
				<label>%s</label>
			</div>
			<div class="sl-input">
				<input type="number" step="any" name="latitude_%%s" class="sl-input-medium latitude" value="%%s">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label>%s</label>
			</div>
			<div class="sl-input">
				<input type="number" step="any" name="longitude_%%s" class="sl-input-medium longitude" value="%%s">
				<p class="description">%s</p>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label>%s<span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">%%s</div>
		</div>
		<a href="%s">%s</a>
	</div>',
	__( 'Latitude', '7listings' ),
	__( 'Longitude', '7listings' ),
	__( 'Enter Latitude and Longitude to position map marker', '7listings' ),
	__( 'Area', '7listings' ),
	admin_url( 'edit-tags.php?taxonomy=location&post_type=tour' ),
	__( 'Edit Locations', '7listings' )
);
$price_types = array(
	'adult'  => __( 'Adult', '7listings' ),
	'child'  => __( 'Child', '7listings' ),
	'senior' => __( 'Senior', '7listings' ),
	'family' => __( 'Family', '7listings' ),
	'infant' => __( 'Infant', '7listings' ),
);

$walker = new Sl_Walker_Category_Dropdown;

$default = array(
	'title'          => '',
	'desc'           => '',
	'photos'         => array(),
	'departure_type' => 'daily',
	'price_adult'    => '',
	'price_senior'   => '',
	'price_child'    => '',
	'price_infant'   => '',
	'price_family'   => '',
	'lead_in_rate'   => '',
	'upsells'        => 0,
	'location'       => 0,
	'latitude'       => '',
	'longitude'      => '',
	'area'           => '',
);
$details = empty( $details ) ? array( $default ) : $details;

foreach ( $details as $index => $detail )
{
	$detail  = array_merge( $default, $detail );
	$checked = array();
	foreach ( array( 'adult', 'senior', 'child', 'infant', 'family' ) as $type )
	{
		$checked[$type] = checked( '' !== $detail["price_{$type}"], true, false );
	}

	echo '<div class="tour_detail">';
	echo '<a href="#" class="delete-booking button" title="' . __( 'Delete Booking Resource', '7listings' ) . '">' . __( 'Delete resource', '7listings' ) . '</a>';

	printf( $title, $index, $detail['title'] );
	printf( $desc, $index, $detail['desc'] );

	if ( ! empty( $detail['photos'] ) )
	{
		echo "<ul class='reorder' data-resource_id='$index'>";
		foreach ( $detail['photos'] as $photo )
		{
			list( $photo_src ) = wp_get_attachment_image_src( $photo );
			$description = get_post_field( 'post_excerpt', $photo );

			printf(
				$img,
				$photo,
				$photo_src,
				$description,
				$photo,
				$photo,
				$index
			);
		}
		echo '</ul>';
	}

	printf( $upload, $index );

	echo '<hr>';

	echo '
		<div class="sl-settings toggle-choices">
			<div class="sl-label">
				<label>' . __( 'Departure Type', '7listings' ) . '</label>
			</div>
			<div class="sl-input">
				<select class="sl-input-medium departure-type" name="tour_detail_departure_type_' . $index . '">
		';
	Sl_Form::options( $detail['departure_type'], $departure_types );
	echo '
				</select>
			</div>
		</div>
	';

	// Daily departures
	$cols    = array();
	$departs = ! empty( $detail['depart'] ) ? $detail['depart'] : array();
	$arrives = ! empty( $detail['arrive'] ) ? $detail['arrive'] : array();

	if ( empty( $departs ) )
	{
		$cols[] = sprintf(
			$departure_col,
			$index, '',
			$index, '',
			'hidden'
		);
	}
	else
	{
		$total = count( $detail['depart'] ) - 1;
		foreach ( $departs as $k => $depart )
		{
			$cols[] = sprintf(
				$departure_col,
				$index, $depart,
				$index, ! empty( $arrives[$k] ) ? $arrives[$k] : '',
				$k == $total ? 'hidden' : ''
			);
		}
	}
	printf(
		$departures,
		$index,
		implode( '', $cols )
	);

	// Departures by days
	echo '<div class="departures-by-days" data-name="tour_detail_departure_type_' . $index . '" data-value="specific">';

	$days = array(
		'mon' => __( 'Monday', '7listings' ),
		'tue' => __( 'Tuesday', '7listings' ),
		'wed' => __( 'Wednesday', '7listings' ),
		'thu' => __( 'Thursday', '7listings' ),
		'fri' => __( 'Friday', '7listings' ),
		'sat' => __( 'Saturday', '7listings' ),
		'sun' => __( 'Sunday', '7listings' ),
	);
	foreach ( $days as $day => $name )
	{
		$cols    = array();
		$departs = isset( $detail["{$day}_depart"] ) ? $detail["{$day}_depart"] : array();
		$arrives = isset( $detail["{$day}_arrive"] ) ? $detail["{$day}_arrive"] : array();

		if ( empty( $departs ) )
		{
			$cols[] = sprintf(
				$by_day_col,
				$day,         // Departure class
				$day, $index, // Departure name
				'',           // Departure value
				$day,         // Arrive class
				$day, $index, // Arrive name
				'',           // Arrive value
				'hidden'
			);
		}
		else
		{
			$total = count( $departs ) - 1;
			foreach ( $departs as $k => $depart )
			{
				$cols[] = sprintf(
					$by_day_col,
					$day,         // Departure class
					$day, $index, // Departure name
					$depart,      // Departure value
					$day,         // Arrive class
					$day, $index, // Arrive name
					isset( $arrives[$k] ) ? $arrives[$k] : '', // Arrive value
					$k == $total ? 'hidden' : ''
				);
			}
		}

		printf(
			$by_day,
			$name,        // Label
			checked( ! empty( $departs ), true, false ), // Checked
			$day,         // Class
			$day, $index, // ID
			$day, $index, // Label for
			implode( '', $cols )
		);
	}

	echo '</div>'; // .departures-by-days

	do_action( 'tour_edit_tab_booking_after_departures', $detail, $index );

	echo '<hr>';

	$price_boxes          = array();
	$lead_in_rate_options = array();
	foreach ( $price_types as $type => $label )
	{
		$price_boxes[]          = sprintf(
			$price_box,
			$checked[$type],          // Checked
			$type, $index,            // ID
			$type, $index,            // Label for
			$label,
			$type,                    // Class
			$type, $index,            // Name
			$detail["price_{$type}"], // Value
			$label                    // Label
		);
		$lead_in_rate_options[] = sprintf(
			'<option value="%s"%s>%s</option>',
			$type,
			selected( $type, $detail['lead_in_rate'], false ),
			$label
		);
	}

	// Display prices in 1 column, 1 column for lead in rate and allocation
	echo '<div class="sl-row">';
	echo '<div class="column-2">' . sprintf( $prices, implode( '', $price_boxes ) ) . '</div>';
	echo '<div class="column-2">' . sprintf( $lead_in_rate, $index, implode( '', $lead_in_rate_options ) );
	do_action( 'tour_edit_tab_booking_after_lead_in_rate', $detail, $index );
	echo sprintf( $allocation, $index, $detail['allocation'] ) . '</div>';
	echo '</div>';

	printf(
		$upsell_button,
		checked( $detail['upsells'], 1, false ),
		$index,
		$index,
		$index
	);

	$has_upsells = false;
	if ( $detail['upsells'] )
	{
		$upsell_items  = ! empty( $detail['upsell_items'] ) ? $detail['upsell_items'] : array();
		$upsell_prices = ! empty( $detail['upsell_prices'] ) ? $detail['upsell_prices'] : array();
		$multipliers   = ! empty( $detail['upsell_multipliers'] ) ? $detail['upsell_multipliers'] : array();

		$count = 0;
		$total = count( $upsell_items );

		if ( $total )
		{
			echo '<div class="sl-sub-settings">';
			$has_upsells = true;
		}

		foreach ( $upsell_items as $k => $item )
		{
			$count ++;
			$price = isset( $upsell_prices[$k] ) ? $upsell_prices[$k] : '';

			$multiplier = '';
			if ( sl_setting( 'tour_multiplier' ) )
			{
				$multiplier = sprintf(
					$multiplier_button,
					$index, $k, // ID
					$index, $k, // Name
					checked( ! empty( $multipliers[$k] ), 1, false ), // Checked
					$index, $k  // Label for
				);
			}

			printf(
				$upsell_item,
				'',         // Class
				$index, $k,          // Name
				$item, // Value
				'',         // Class
				$index, $k,          // Name
				$price,       // Value
				$count == $total ? 'hidden' : '', // Class
				$count == $total ? '<a href="#" class="button add-upsell-item">' . __( 'Add', '7listings' ) . '</a>' : '',
				$multiplier
			);
		}

		if ( $total )
		{
			echo '</div>';
		}
	}

	if ( ! $has_upsells )
	{
		echo '<div class="sl-sub-settings">';

		$multiplier = '';
		if ( sl_setting( 'tour_multiplier' ) )
		{
			$multiplier = sprintf(
				$multiplier_button,
				$index, 0, // ID
				$index, 0, // Name
				'',   // Checked
				$index, 0 // Label for
			);
		}

		printf(
			$upsell_item,
			'',                 // Class
			$index, 0,          // Name
			'',                 // Value
			'',                 // Class
			$index, 0,          // Name
			'',                 // Value
			'hidden',           // Class
			'<a href="#" class="button add-upsell-item">' . __( 'Add', '7listings' ) . '</a>',
			$multiplier
		);

		echo '</div>';
	}

	echo '<hr>';

	printf( $location, checked( ! empty( $detail['location'] ), true, false ), $index, $index, $index );
	$area = wp_dropdown_categories( array(
		'hide_empty'       => 0,
		'selected'         => $detail['area'],
		'show_option_none' => __( 'Select', '7listings' ),
		'hierarchical'     => 1,
		'name'             => "tour_detail_area_{$index}[]",
		'taxonomy'         => 'location',
		'echo'             => 0,
		'class'            => 'select2',
		'multiple'         => true,
		'walker'           => $walker,
	) );
	$area = str_replace( '<select', '<select multiple', $area );
	printf( $marker, $index, $detail['latitude'], $index, $detail['longitude'], $area );
	echo '</div>';
	
}

echo '<a href="#" class="add-booking button" title="' . __( 'Add Booking Resource', '7listings' ) . '">' . __( 'Add resource', '7listings' ) . '</a>';
