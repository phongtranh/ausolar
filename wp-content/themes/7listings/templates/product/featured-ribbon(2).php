<?php
global $product;
if ( $product->is_featured() )
	echo '<div class="featured-listing">' . __( 'Featured', '7listings' ) . '</div>';