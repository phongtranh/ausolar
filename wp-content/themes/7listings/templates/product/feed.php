<?php
header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
$more = 1;

echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>'; ?>

<rss version="2.0"
	 xmlns:content="http://purl.org/rss/1.0/modules/content/"
	 xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	 xmlns:dc="http://purl.org/dc/elements/1.1/"
	 xmlns:atom="http://www.w3.org/2005/Atom"
	 xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	 xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	 xmlns:g="http://base.google.com/ns/1.0"
	<?php do_action( 'rss2_ns' ); ?>
	>

	<channel>
		<title><?php bloginfo( 'name' );
			echo ' - ';
			bloginfo( 'description' ); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml">
			<link><?php bloginfo_rss( 'url' ) ?></link>
			<description><?php bloginfo_rss( 'description' ) ?></description>
			<lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
			<language><?php bloginfo_rss( 'language' ); ?></language>
			<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
			<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
			<?php do_action( 'rss2_head' ); ?>
			<?php
			$query = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => - 1 ) );
			while ( $query->have_posts() ) : $query->the_post();
				global $product;
				?>
				<item>
					<title><?php the_title_rss() ?></title>
					<link><?php the_permalink_rss() ?></link>
					<g:image_link><?php echo wp_get_attachment_url( get_post_thumbnail_id() ) ?></g:image_link>
					<g:price><?php echo $product->price ?></g:price>
					<g:condition><?php echo sl_setting( 'product_feed_condition' ); ?></g:condition>
					<g:tax>
						<g:country></g:country>
						<g:region></g:region>
						<g:rate>0</g:rate>
						<g:tax_ship></g:tax_ship>
					</g:tax>
					<g:shipping>
						<g:country></g:country>
						<g:region></g:region>
						<g:service></g:service>
						<g:price>0</g:price>
					</g:shipping>
					<g:id><?php the_ID(); ?></g:id>
					<g:mpn><?php echo strtoupper( preg_replace( '/[^a-z0-9]/', '', get_bloginfo( 'name' ) ) ) . get_the_ID(); ?></g:mpn>
					<g:availability><?php
						$availability = $product->get_availability();
						switch ( $availability['class'] )
						{
							case 'out-of-stock':
								$value = 'out of stock';
								break;
							case 'available-on-backorder':
								$value = 'available for order';
								break;
							default:
								$value = 'in stock';
						}
						echo $value; ?></g:availability>
					<g:brand><?php
						$brand = sl_setting( 'product_feed_brand' );
						if ( empty( $brand ) )
						{
							$terms = get_the_terms( get_the_ID(), 'brand' );
							if ( is_array( $terms ) )
							{
								$term  = current( $terms );
								$brand = $term->name;
							}
						}
						echo esc_html( $brand );
						?></g:brand>
					<g:product_type><?php
						$terms = get_the_terms( get_the_ID(), 'product_cat' );
						if ( is_array( $terms ) )
						{
							$term = current( $terms );
							echo esc_html( $term->name );
						}
						?></g:product_type>
					<g:google_product_category><?php echo esc_html( sl_setting( 'product_feed_google_product_category' ) ); ?></g:google_product_category>
					<g:shipping_weight><?php echo $product->get_weight(); ?></g:shipping_weight>
					<g:mpn><?php echo $product->get_sku(); ?></g:mpn>
					<?php if ( get_option( 'rss_use_excerpt' ) ) : ?>
						<description><![CDATA[<?php the_content_feed(); ?>]]></description>
					<?php else : ?>
						<description><![CDATA[<?php the_content_feed(); ?>]]></description>
					<?php endif; ?>
					<?php rss_enclosure(); ?>
					<?php do_action( 'rss2_item' ); ?>
				</item>
			<?php endwhile; ?>
	</channel>
</rss>
