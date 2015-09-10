<?php
/**
 * The Template for displaying SINGLE COMPANIES
 * last edit: 5.0.9
 *
 * @package    WordPress
 * @subpackage 7Listings
 */
?>

<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

<?php
$sidebar_layout = sl_sidebar_layout();
$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
?>

<article id="content" <?php post_class( $content_class ); ?>>

<?php the_post(); ?>

<?php peace_action( 'entry_top' ); ?>

<div id="review-summary-wrapper">
	<?php get_template_part( 'templates/parts/company-rating-summary' ); ?>
	<span class="comments-link"><a class="button full" href="#comment-form" data-toggle="modal"><?php _e( 'Add a Review', '7listings' ); ?></a></span>
</div>

<?php
echo '<div id="description" class="entry-content" itemprop="description">';
the_content();
echo '</div>';

$membership = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership', true );
if ( ! $membership )
	$membership = 'none';

if ( sl_setting( "company_single_social_media_$membership" ) )
{
	$info  = array( 'facebook', 'googleplus', 'twitter', 'pinterest', 'linkedin', 'instagram', 'rss' );
	$found = false;
	foreach ( $info as $k )
	{
		$$k = get_post_meta( get_the_ID(), $k, true );
		if ( $$k )
			$found = true;
	}
	if ( $found )
	{
		echo '<section id="company-social-media-links" class="company-meta social-media-links">';
		foreach ( $info as $k )
		{
			if ( empty( $$k ) )
				continue;
			$link = $k == 'twitter' ? 'http://twitter.com/' . $$k : esc_url( $$k );
			echo '<a class="' . $k . '" href="' . $link . '" rel="nofollow" target="_blank"></a>';
		}
		echo '</section>';
	}
}

get_template_part( 'templates/company/address' );

if ( sl_setting( "company_google_maps_$membership" ) )
	get_template_part( 'templates/company/map' );

$html = '';
if ( get_post_meta( get_the_ID(), 'open_247', true ) )
{
	$html .= sprintf(
		'<div class="day">
					<span class="label">%s</span>
					<span class="detail"><time itemprop="openingHours" datetime="Mo-Su">%s</time></span>
				</div>',
		__( 'Monday - Sunday', '7listings' ),
		__( 'All day', '7listings' )
	);
}
else
{
	$days = array(
		'mon' => __( 'Monday', '7listings' ),
		'tue' => __( 'Tuesday', '7listings' ),
		'wed' => __( 'Wednesday', '7listings' ),
		'thu' => __( 'Thursday', '7listings' ),
		'fri' => __( 'Friday', '7listings' ),
		'sat' => __( 'Saturday', '7listings' ),
		'sun' => __( 'Sunday', '7listings' ),
	);
	$open = false;
	foreach ( $days as $k => $v )
	{
		if ( get_post_meta( get_the_ID(), "business_hours_$k", true ) )
		{
			$open = true;
			break;
		}
	}
	if ( $open )
	{
		foreach ( $days as $k => $v )
		{
			if ( ! get_post_meta( get_the_ID(), "business_hours_$k", true ) )
			{
				$html .= sprintf(
					'<div class="day">
								<span class="label">%s</span>
								<span class="detail">%s</span>
							</div>',
					esc_html( $v ), esc_html__( 'Closed', '7listings' )
				);
				continue;
			}

			$time = get_post_meta( get_the_ID(), "business_hours_{$k}_from", true ) . ' - ' . get_post_meta( get_the_ID(), "business_hours_{$k}_to", true );
			$name = substr( ucfirst( $k ), 0, 2 );

			$html .= sprintf(
				'<div class="day">
							<span class="label">%1$s</span>
							<span class="detail"><time itemprop="openingHours" datetime="%2$s %3$s">%3$s</time></span>
						</div>',
				$v, $name, $time
			);
		}
	}
}

if ( $html )
{
	echo '<section id="business-hours" class="company-meta business-hours">';
	echo '<h4>' . __( 'Business Hours', '7listings' ) . '</h4>';
	echo $html;
	echo '</section>';
}

// Brand
$tax   = 'brand';
$terms = wp_get_post_terms( get_the_ID(), $tax );
if ( ! is_wp_error( $terms ) && ! empty( $terms ) )
{
	echo '<section id="brands" class="company-meta brands">';
	echo '<h3>' . sprintf( __( '%s\'s Brands', '7listings' ), get_the_title() ) . '</h3>';
	foreach ( $terms as $term )
	{
		echo '<div itemprop="brand" itemscope itemtype="http://schema.org/Brand" class="brand">';
		$html = "<span itemprop='name' style='display:none'>$term->name</span>";
		if ( $logo = sl_get_term_meta( $term->term_id, 'thumbnail_id' ) )
		{
			list( $src ) = wp_get_attachment_image_src( $logo, 'full' );
			echo "<span itemprop='image' style='display: none'>$src</span>";
			$html = '<img class="logo" src="' . $src . '"alt="' . $term->name . '"> ' . $html;
		}
		printf( '<a href="%s" title="%s">%s</a>', get_term_link( $term, $tax ), $term->name, $html );
		echo '</div>';
	}
	echo '</section>';
}

// Products
$tax   = get_post_type() . '_product';
$terms = wp_get_post_terms( get_the_ID(), $tax );
if ( ! is_wp_error( $terms ) && ! empty( $terms ) )
{
	echo '<section id="services" class="company-meta products">';
	echo '<h3>' . sprintf( __( '%s\'s Products', '7listings' ), get_the_title() ) . '</h3>';
	echo '<ul class="sl-list custom ok-sign">';
	foreach ( $terms as $term )
	{
		printf( '<li itemprop="owns" itemscope itemtype="http://schema.org/Product"><a href="%s" title="%s" itemprop="name">%s</a></li>', get_term_link( $term, $tax ), $term->name, $term->name );
	}
	echo '</ul>';
	echo '</section>';
}

// Services
$tax   = get_post_type() . '_service';
$terms = wp_get_post_terms( get_the_ID(), $tax );
if ( ! is_wp_error( $terms ) && ! empty( $terms ) )
{
	echo '<section id="products" class="company-meta services">';
	echo '<h3>' . sprintf( __( '%s\'s Services', '7listings' ), get_the_title() ) . '</h3>';
	echo '<ul class="sl-list custom ok-sign">';
	foreach ( $terms as $term )
	{
		printf( '<li itemprop="owns" itemscope itemtype="http://schema.org/Product"><a href="%s" title="%s" itemprop="name">%s</a></li>', get_term_link( $term, $tax ), $term->name, $term->name );
	}
	echo '</ul>';
	echo '</section>';
}

peace_action( 'entry_bottom' );


edit_post_link( __( 'Edit Listing', '7listings' ), '<span class="edit-link button small">', '</span>' );

if ( current_user_can( 'manage_options' ) )
	echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=company#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
?>

<?php
if ( sl_setting( "company_comment_status_$membership" ) )
	comments_template( '', true );
?>


<?php
// Similar listings
if ( sl_setting( "company_similar_$membership" ) )
{
	$by = sl_setting( 'company_similar_by' );

	// Common query args
	$args = array(
		'post_type'      => 'company',
		'post_status'    => 'publish',
		'posts_per_page' => sl_setting( 'company_similar_display' ),
		'post__not_in'   => array( get_the_ID() )
	);
	switch ( $by )
	{
		case 'location':
			$args['meta_key']   = 'state';
			$args['meta_value'] = get_post_meta( get_the_ID(), 'state', true );
			break;
		case 'services':
			$tax               = 'company_service';
			$terms             = wp_get_post_terms( get_the_ID(), $tax );
			$args['tax_query'] = array(
				array(
					'taxonomy' => $tax,
					'terms'    => wp_list_pluck( $terms, 'term_id' ),
				),
			);
			break;
		case 'brands':
			$tax               = 'company_brand';
			$terms             = wp_get_post_terms( get_the_ID(), $tax );
			$args['tax_query'] = array(
				array(
					'taxonomy' => $tax,
					'terms'    => wp_list_pluck( $terms, 'term_id' ),
				),
			);
			break;
		default:
	}

	$query = new WP_Query( $args );
	if ( $query->have_posts() )
	{
		printf(
			'<aside id="related" class="sl-list posts companies columns-%d">
						<h3>%s</h3>',
			sl_setting( 'company_similar_columns' ),
			sl_setting( 'company_similar_title' )
		);

		$post_type  = get_post_type();
		$image_size = sl_setting( $post_type . '_similar_image_size' );
		while ( $query->have_posts() )
		{
			$query->the_post();
			?>
			<article class="post company">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
					<?php
					sl_broadcasted_thumbnail( $image_size, array(
						'alt'   => the_title_attribute( 'echo=0' ),
						'title' => the_title_attribute( 'echo=0' ),
					) );
					?>
				</a>
				<div class="details">
					<h4 class="entry-title">
						<a class="title" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h4>
					<?php
					if ( sl_setting( "{$post_type}_similar_rating" ) )
						echo Sl_Company_Helper::show_average_rating( get_the_ID(), false );
					?>
					<?php if ( sl_setting( "{$post_type}_similar_excerpt" ) ) : ?>
						<span class="entry-summary excerpt">
							<?php echo sl_excerpt( sl_setting( "{$post_type}_similar_excerpt_length" ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			</article>
		<?php
		}

		echo '</aside>'; // #related
	}
}
?>

</article>
<!-- #content -->


<?php if ( 'none' != $sidebar_layout ) : ?>
	<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
		<?php get_sidebar(); ?>
	</aside>
<?php endif; ?>

</div><!-- .container -->

<?php get_footer(); ?>
