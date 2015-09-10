<?php
/**
 * Wholesale lead value Meta Box
 * @return Meta Box
 */

$value = get_post_meta( get_the_ID(), 'wholesale_lead_value', true );
?>

<input type="number" name="wholesale_lead_value" value="<?php echo $value ?>" > $ / lead