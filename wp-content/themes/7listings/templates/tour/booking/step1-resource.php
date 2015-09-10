<?php
/**
 * Show date and time inputs depend on departure type. Supports built-in departure types
 * But other plugins (like 7Tour Ticket) can hook to sl_get_template to change the template for specific type
 */
$departure_type = isset( $resource['departure_type'] ) ? $resource['departure_type'] : '';
sl_get_template( "templates/tour/booking/departure-types/$departure_type", $params );

sl_get_template( 'templates/tour/booking/parts/guests', $params );
sl_get_template( 'templates/booking/parts/upsells', $params );
