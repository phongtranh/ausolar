<?php

$available_codes = array();
$sources = solar_get_sources();

$available_codes[''] = '';
foreach ( $sources as $key => $value )
{
    if ( in_array( $key, array( 'I', 'P', 'W', 'C' ) ) ) continue;
    $available_codes[$key] = $key;
}


$selected_code = get_post_meta( get_the_ID(), 'wholesale_code', true );
?>
<select name="wholesale_code">
    <?php
    $selected = isset( $selected_code ) ? $selected_code : '';
    SL_Form::options( $selected, $available_codes );
    ?>
</select>