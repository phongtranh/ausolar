<?php
$products = array(
	'Grid connect solar system',
	'Stand alone solar system',
	'Heat pump',
	'Solar hot water',
	'LED lighting',
	'Solar powered air conditioner',
	'Wireless energy monitor',
	'Insulation',
);

$saved = (array) get_post_meta( get_the_ID(), 'products', true );

$html = array();
$tpl  = '<input type="checkbox" name="products[]" value="%s" %s> %s';
foreach ( $products as $product )
{
	$html[] = sprintf(
		$tpl,
		$product,
		checked( in_array( $product, $saved ), 1, false ),
		$product
	);
}
echo implode( '<br>', $html );
