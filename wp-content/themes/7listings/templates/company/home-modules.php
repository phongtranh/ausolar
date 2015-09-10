<?php
/**
 * Display module content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_show_company_modules( $id )
{
	if ( 'company_logos' != $id )
		return;

	$prefix = 'homepage_company_logos_';

	$args = array(
		'post_type'      => 'company',
		'post_status'    => 'publish',
		'posts_per_page' => sl_setting( "{$prefix}total" ),
	);

	// Get companies by meta
	if ( sl_setting( "{$prefix}featured" ) )
	{
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'   => 'featured',
				'value' => 1,
			),
			array(
				'key'   => 'featured',
				'value' => 2,
			),
		);
	}
	$by_meta = get_posts( $args );
	$by_meta = wp_list_pluck( $by_meta, 'ID' );

	// Get companies by membership
	$selected_memberships = array();
	$memberships          = array( 'none', 'bronze', 'silver', 'gold' );
	foreach ( $memberships as $membership )
	{
		if ( sl_setting( "{$prefix}{$membership}" ) )
			$selected_memberships[] = $membership;
	}
	$all_users = get_users();
	$users     = array();
	foreach ( $all_users as $user )
	{
		if (
			in_array( $user->membership, $selected_memberships ) ||
			( $user->membership == '' && in_array( 'none', $selected_memberships ) )
		)
			$users[] = $user->ID;
	}
	$args['meta_query'] = array(
		array(
			'key'     => 'user',
			'value'   => $users,
			'compare' => 'IN',
		),
	);
	$by_membership      = get_posts( $args );
	$by_membership      = wp_list_pluck( $by_membership, 'ID' );

	// Get list of companies
	$companies = array_intersect( $by_meta, $by_membership );
	if ( empty( $companies ) )
		return;

	$title = sl_setting( "{$prefix}title" );
	if ( $title )
	{
		$heading_style  = sl_heading_style( 'homepage_company_logos_title' );
		$title          = "<" . $heading_style . " class='title section'>$title</" . $heading_style . ">";
	}
	?>
	<section id="company-logos" class="section">
		<div class="container">
			<?php echo $title; ?>
		</div>
		<?php
		$params = array(
			'speed'           => sl_setting( "{$prefix}speed" ),
			'transitionSpeed' => sl_setting( "{$prefix}transition_speed" )
		);
		$height = sl_setting( "{$prefix}height" );
		?>
		<div class="slider" style="height:<?php echo $height; ?>px" data-params="<?php echo esc_attr( json_encode( $params ) ); ?>">
			<ul>
				<?php
				foreach ( $companies as $company )
				{
					list( $src ) = wp_get_attachment_image_src( get_post_thumbnail_id( $company ), 'full' );
					if ( ! $src )
						continue;

					$title = esc_attr( get_the_title( $company ) );
					printf(
						'<li><a href="%s" title="%s"><img src="%s" alt="%s" style="max-height:%dpx"></a></li>',
						get_permalink( $company ),
						$title, $src, $title, $height
					);
				}
				?>
			</ul>
		</div>
	</section>
<?php
}
