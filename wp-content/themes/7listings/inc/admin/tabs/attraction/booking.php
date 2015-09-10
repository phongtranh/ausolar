<?php
$post_type = 'attraction';

$details = get_post_meta( get_the_ID(), sl_meta_key( 'booking', $post_type ), true );

// Fields templates
$title         = sprintf( '
	<div class="sl-settings resource-title">
		<div class="sl-label">
			<label>%s %s<span class="warning-lg required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" class="tour_detail_title" name="attraction_detail_title_%%s" value="%%s" placeholder="%s">
		</div>
	</div>',
	__( 'Title', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Insert a title<br>for your attraction or package', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ),
	sprintf( __( '%s item title', '7listings' ), ucwords( $post_type ) )
);
$desc          = sprintf( '
	<div class="sl-settings resource-description">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input sl-block">
			<textarea class="tour_detail_desc" name="attraction_detail_desc_%%s" placeholder="%s">%%s</textarea>
		</div>
	</div>',
	__( 'Description', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Insert a description<br>for your attraction or package', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ),
	sprintf( __( '%s item description', '7listings' ), ucwords( $post_type ) )
);
$upload        = sprintf( '
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
$img           = sprintf( '
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
$prices        = sprintf( '
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
$price_box     = sprintf( '
	<div class="price-box tour">
		<span class="checkbox-toggle" data-effect="fade">
			<span class="checkbox">
				<input type="checkbox" %%s id="attraction_detail_price_%%s_%%s">
				<label for="attraction_detail_price_%%s_%%s">&nbsp;</label>
			</span>
			<span class="price-type">%%s</span>
		</span>
		<span class="input-prepend input-append">
			<span class="add-on">%s</span>
			<input type="number" step="any" min="0" class="small-text tour_detail_price_%%s" name="attraction_detail_price_%%s_%%s" value="%%s" placeholder="%s">
			<span class="add-on">%%s</span>
		</span>
	</div>',
	Sl_Currency::symbol(),
	__( 'Price', '7listings' )
);
$lead_in_rate  = sprintf( '
	<div class="sl-settings lead-in-rate">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input">
			<select name="attraction_detail_lead_in_rate_%%s" class="sl-input-small attraction_detail_lead_in_rate">
				%%s
			</select>
		</div>
	</div>',
	__( 'Lead in rate', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Select a guest type if your attraction is a family package or child attraction and the other guest types are meant as extras', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' )
);
$upsell_button = sprintf( '
	<div class="sl-settings checkbox-toggle upsells">
		<div class="sl-label">
			<label>%s %s</label>
		</div>
		<div class="sl-input">
			<span class="checkbox">
				<input type="checkbox" %%s class="attraction_detail_upsells" id="attraction_detail_upsells_%%s" name="attraction_detail_upsells_%%s">
				<label for="attraction_detail_upsells_%%s">&nbsp;</label>
			</span>
		</div>
	</div>',
	__( 'Upsells', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Add upsells that customers can purchase with the attraction during booking', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' )
);
$upsell_item   = sprintf( '
	<div class="sl-settings upsell">
		<div class="sl-label">
			<label><span class="warning-sm required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" class="attraction_detail_upsells_item %%s" name="attraction_detail_upsells_item_%%s[%%s]" value="%%s" placeholder="%s">
			<span class="input-prepend">
				<span class="add-on">%s</span>
				<input type="number" step="any" min="0" class="small-text attraction_detail_upsells_price %%s" name="attraction_detail_upsells_price_%%s[%%s]" value="%%s" placeholder="%s">
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
		<input type="checkbox" class="attraction_detail_upsells_multiplier" id="attraction_detail_upsells_multiplier_%%s_%%s" name="attraction_detail_upsells_multiplier_%%s[%%s]" value="1" %%s>
		<label for="attraction_detail_upsells_multiplier_%%s_%%s">&nbsp;</label>
	</span>
	<span class="description">%s</span> %s',
	__( 'Multiply with Guests', '7listings' ),
	do_shortcode( '[tooltip content="' . __( 'Turn ON to multiply the upsell with all guests<br>example: extra night, transfer', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' )
);

$location    = sprintf( '
	<div class="sl-settings attraction-location checkbox-toggle">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="checkbox">
				<input type="checkbox" %%s class="attraction_detail_location" id="attraction_detail_location_%%s" name="attraction_detail_location_%%s" value="1">
				<label for="attraction_detail_location_%%s">&nbsp;</label>
			</span>
		</div>
	</div>',
	__( 'Attraction Location', '7listings' )
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
		</div>',
	__( 'Latitude', '7listings' ),
	__( 'Longitude', '7listings' ),
	__( 'Enter Latitude and Longitude to position map marker', '7listings' )
);
$price_types = array(
	'adult'  => __( 'Adult', '7listings' ),
	'child'  => __( 'Child', '7listings' ),
	'senior' => __( 'Senior', '7listings' ),
	'family' => __( 'Family', '7listings' ),
	'infant' => __( 'Infant', '7listings' ),
);

$default = array(
	'title'        => '',
	'desc'         => '',
	'photos'       => array(),
	'price_adult'  => '',
	'price_senior' => '',
	'price_child'  => '',
	'price_infant' => '',
	'price_family' => '',
	'lead_in_rate' => '',
	'upsells'      => 0,
	'location'     => 0,
	'latitude'     => '',
	'longitude'    => '',
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
	echo '
		<div class="sl-row">
			<div class="column-2">' . sprintf( $prices, implode( '', $price_boxes ) ) . '</div>
			<div class="column-2">' . sprintf( $lead_in_rate, $index, implode( '', $lead_in_rate_options ) ) . '</div>
		</div>
	';

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
			if ( sl_setting( 'attraction_multiplier' ) )
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
		if ( sl_setting( 'attraction_multiplier' ) )
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
	printf( $marker, $index, $detail['latitude'], $index, $detail['longitude'] );
	echo '</div>';
}

echo '<a href="#" class="add-booking button" title="' . __( 'Add Booking Resource', '7listings' ) . '">' . __( 'Add resource', '7listings' ) . '</a>';
