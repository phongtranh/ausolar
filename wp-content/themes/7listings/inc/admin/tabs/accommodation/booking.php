<?php
$post_type = 'accommodation';
$details   = get_post_meta( get_the_ID(), sl_meta_key( 'booking', $post_type ), true );

// Fields templates
$title      = sprintf( '
	<div class="sl-settings resource-title">
		<div class="sl-label">
			<label>%s <span class="warning-lg required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" class="hotel_detail_title" name="hotel_detail_title_%%s" value="%%s" placeholder="%s">
		</div>
	</div>',
	__( 'Title', '7listings' ),
	sprintf( __( '%s item title', '7listings' ), ucwords( $post_type ) )
);
$desc       = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input sl-block">
			<textarea class="hotel_detail_desc" name="hotel_detail_desc_%%s" placeholder="%s">%%s</textarea>
		</div>
	</div>',
	__( 'Description', '7listings' ),
	sprintf( __( '%s item description', '7listings' ), ucwords( $post_type ) )
);
$upload           = sprintf( '
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
$img        = sprintf( '
	<li class="sl-settings uploaded photo-detail-container" id="item_%%s">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<img src="%%s">
			<input type="text" class="tour_detail_photos" value="%%s" data-attachment_id="%%s" placeholder="%s">
			<a class="button delete-file" href="#" data-attachment_id="%%s" data-resource_id="%%s">%s</a>
		</div>
	</li>',
	__( 'Photo', '7listings' ),
	__( 'Photo description', '7listings' ),
	__( 'Delete', '7listings' )
);
$price      = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s <span class="warning-sm required right"></span></label>
		</div>
		<div class="sl-input">
			<span class="input-prepend input-append">
				<span class="add-on">' . Sl_Currency::symbol() . '</span>
				<input type="number" step="any" min="0" class="amount large hotel_detail_price" name="hotel_detail_price_%%s" value="%%s" placeholder="%s">
				<span class="add-on">%s</span>
			</span>
		</div>
	</div>',
	__( 'Price', '7listings' ),
	__( 'Price', '7listings' ),
	__( '/night', '7listings' )
);
$occupancy  = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="input-append">
				<input type="number" min="0" class="amount hotel_detail_occupancy" name="hotel_detail_occupancy_%%s" value="%%s">
				<span class="add-on">%s</span>
			</span>
		</div>
	</div>',
	__( 'Occupancy', '7listings' ),
	__( 'Guests', '7listings' )
);
$extra      = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="input-prepend input-append">
				<span class="add-on">' . Sl_Currency::symbol() . '</span>
				<input type="number" step="any" min="0" class="amount large hotel_detail_price_extra" name="hotel_detail_price_extra_%%s" value="%%s" placeholder="%s">
				<span class="add-on">%s</span>
			</span>
		</div>
	</div>',
	__( 'Price for extra Person', '7listings' ),
	__( 'Price', '7listings' ),
	__( '/night', '7listings' )
);
$max        = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="input-append">
				<input type="number" min="0" class="amount hotel_detail_max_occupancy" name="hotel_detail_max_occupancy_%%s" value="%%s">
				<span class="add-on">%s</span>
			</span>
		</div>
	</div>',
	__( 'Max. Occupancy', '7listings' ),
	__( 'Guests', '7listings' )
);
$allocation = sprintf( '
	<div class="sl-settings">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="input-append">
				<input type="number" min="0" class="amount" name="hotel_detail_allocation_%%s" value="%%s">
				<span class="add-on"></span>
			</span>
		</div>
	</div>',
	__( 'Allocation', '7listings' ),
	__( '/night', '7listings' )
);

$default = array(
	'title'         => '',
	'desc'          => '',
	'photos'        => array(),
	'price'         => '',
	'price_extra'   => '',
	'occupancy'     => 0,
	'max_occupancy' => 0,
	'allocation'    => 0,
);
$details = empty( $details ) ? array( $default ) : $details;

foreach ( $details as $index => $detail )
{
	echo '<div class="hotel_detail">';
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

	echo '<div class="col-prices">';
	printf( $price, $index, $detail['price'] );
	printf( $extra, $index, $detail['price_extra'] );
	echo '</div>';

	echo '<div class="col-occupancy">';

	// Occupancy
	printf( $occupancy, $index, $detail['occupancy'] );

	// Max Occupancy
	printf( $max, $index, $detail['max_occupancy'] );

	echo '</div>';

	echo '<hr>';

	// Allocation
	printf( $allocation, $index, $detail['allocation'] );

	echo '</div>';
}

echo '<a href="#" class="add-booking button" title="' . __( 'Add Booking Resource', '7listings' ) . '">' . __( 'Add resource', '7listings' ) . '</a>';
