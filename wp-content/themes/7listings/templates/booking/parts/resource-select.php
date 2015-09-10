<div class="resource select">
	<h4 class="title"><?php echo $resource['title']; ?></h4>
	<?php
	// Show list of booking resources to switch only if not in cart booking page and there are >= 2 resources
	if ( ! $is_cart && count( $resources ) > 1 ) :
		?>
		<a href="#" class="change-resource"><?php printf( __( 'Change %s', '7listings' ), sl_setting( get_post_type() . '_label' ) ); ?></a>
		<ul class="hidden resources">
			<?php
			$class = 'Sl_' . ucfirst( get_post_type() ) . '_Helper';
			foreach ( $resources as $resource_id => $resource_data )
			{
				// Ignore current resource
				if ( $resource_id == $resource['resource_id'] )
				{
					continue;
				}
				$resource_slug = sanitize_title( $resource_data['title'] );
				$price         = Sl_Currency::format( call_user_func( array( $class, 'get_resource_price', ), $resource_data ), 'type=plain' );
				printf(
					'<li>
						<a href="%s">
							<span class="title">%s</span>
							<span class="price">%s</span>
						</a>
					</li>',
					home_url( 'book/' . get_post_field( 'post_name', null ) . '/' . $resource_slug . '/' ),
					$resource_data['title'],
					$price
				);
			}
			?>
		</ul>
	<?php endif; ?>
</div>
