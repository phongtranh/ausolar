<?php
$post_type = 'rental';

$details = get_post_meta( get_the_ID(), 'booking', true );

// Fields templates
$title         = sprintf( '
	<div class="sl-settings resource-title">
		<div class="sl-label">
			<label>%s <span class="warning-lg required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" class="detail_title" name="detail_title_%%s" value="%%s" placeholder="%s">
		</div>
	</div>',
	__( 'Title', '7listings' ),
	sprintf( __( '%s item title', '7listings' ), ucwords( $post_type ) )
);
$desc          = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s <span class="warning-sm required left"></span></label>
		</div>
		<div class="sl-input sl-block">
			<textarea class="detail_desc" name="detail_desc_%%s" placeholder="%s">%%s</textarea>
		</div>
	</div>',
	__( 'Description', '7listings' ),
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

		<img src="%%s">
		<input type="text" class="tour_detail_photos" value="%%s" data-attachment_id="%%s" placeholder="%s">
		<a class="button delete-file" href="#" data-attachment_id="%%s" data-resource_id="%%s">%s</a>
	</li>',
	__( 'Photo', '7listings' ),
	__( 'Photo description', '7listings' ),
	__( 'Delete', '7listings' )
);
$price         = sprintf( '
	<div class="sl-settings rental-price">
		<div class="sl-label">
			<label class="single">%%s</label>
		</div>
		<div class="sl-input">
			<span class="input-prepend input-append">
				<span class="add-on">%s</span>
				<input type="number" step="any" min="0" class="amount large detail_price" name="detail_price_%%s[%%s]" value="%%s" placeholder="%s">
				<span class="add-on">%%s</span>
			</span>
		</div>
	</div>',
	Sl_Currency::symbol(),
	__( 'Price', '7listings' )
);
$allocation    = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="input-append">
				<input type="number" min="0" class="amount" name="detail_allocation_%%s" value="%%s">
				<span class="add-on">%s</span>
			</span>
		</div>
	</div>',
	__( 'Allocation', '7listings' ),
	__( '/day', '7listings' )
);
$upsell_button = sprintf( '
	<div class="sl-settings checkbox-toggle">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="checkbox">
				<input type="checkbox" %%s class="detail_upsells" id="detail_upsells_%%s" name="detail_upsells_%%s">
				<label for="detail_upsells_%%s">&nbsp;</label>
			</span>
		</div>
	</div>',
	__( 'Upsells', '7listings' )
);
$upsell_item   = sprintf( '
	<div class="sl-settings upsell">
		<div class="sl-label">
			<label class="single"><span class="warning-sm required right"></span></label>
		</div>
		<div class="sl-input">
			<span class="input">
				<input type="text" class="detail_upsells_item %%s" name="detail_upsells_item_%%s[%%s]" value="%%s" placeholder="%s">
				<span class="input-prepend input-append">
					<span class="add-on">$</span>
					<input type="number" step="any" min="0" class="small-text detail_upsells_price %%s" name="detail_upsells_price_%%s[%%s]" value="%%s" placeholder="%s">
					<span class="add-on">%s</span>
				</span>
			</span>
			<a href="#" class="button delete-upsell-item %%s">%s</a>
			%%s
			%%s
		</div>
	</div>',
	__( 'Item description', '7listings' ),
	__( 'Price', '7listings' ),
	__( '/day', '7listings' ),
	__( 'Delete', '7listings' )
);

$default = array(
	'title'      => '',
	'desc'       => '',
	'photos'     => array(),
	'price'      => array(),
	'allocation' => 0,
	'upsells'    => 0,
);
$details = empty( $details ) ? array( $default ) : $details;

foreach ( $details as $index => $detail )
{
	echo '<div class="detail">';
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

	for ( $i = 1; $i <= 7; $i ++ )
	{
		printf(
			$price,
			1 == $i ? __( 'Price', '7listings' ) : '&nbsp;',
			$index,
			$i,
			isset( $detail['price'][$i] ) ? $detail['price'][$i] : '',
			1 == $i ? __( '1 Day', '7listings' ) : ( 7 == $i ? __( '7+ Days', '7listings' ) : sprintf( __( '%d Days', '7listings' ), $i ) )
		);
	}

	echo '<hr>';

	// Allocation
	printf( $allocation, $index, $detail['allocation'] );

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
			$upsell_price = isset( $upsell_prices[$k] ) ? $upsell_prices[$k] : '';

			printf(
				$upsell_item,
				'',                  // Class
				$index, $k,          // Name
				$item,               // Value
				'',                  // Class
				$index, $k,          // Name
				$upsell_price,       // Value
				$count == $total ? 'hidden' : '', // Class
				$count == $total ? '<a href="#" class="button add-upsell-item">' . __( 'Add', '7listings' ) . '</a>' : '',
				''
			);
		}

		if ( $total )
		{
			echo '</div>';
		}
	}

	if ( ! $has_upsells )
	{
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
			''
		);
	}

	echo '</div>';
}

echo '<a href="#" class="add-booking button" title="' . __( 'Add Booking Resource', '7listings' ) . '">' . __( 'Add resource', '7listings' ) . '</a>';
