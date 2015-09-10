<?php
/**
 * The Template for displaying SINGLE PRODUCTS
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */
get_header(); ?>

<?php the_post(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
	?>

	<article id="content" <?php post_class( $content_class ); ?>>

		<?php
		peace_action( 'entry_top' );

		do_action( 'woocommerce_before_single_product' );

		sl_photo_slider();

		global $product;
		if ( sl_setting( 'product_attributes' ) )
			$product->list_attributes();

		do_action( 'woocommerce_before_single_product_summary' );
		do_action( 'woocommerce_single_product_summary' );
		do_action( 'woocommerce_after_single_product_summary' );

		echo '<div id="description" class="entry-content" itemprop="description">';
		the_content();
		echo '</div>';

		sl_video( get_post_meta( get_the_ID(), 'movies', true ) );

		do_action( 'woocommerce_after_single_product' );

		peace_action( 'entry_bottom' );

		edit_post_link( __( 'Edit Product', '7listings' ), '<span class="edit-link button small">', '</span>' );
		if ( current_user_can( 'manage_options' ) )
			echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=product#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';

		if ( sl_setting( get_post_type() . '_comment_status' ) )
			comments_template( '', true );

		// Remove default WooCommerce function to display thumbnail
		// Use our own function to display in correct size
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( 'Sl_Product_Frontend', 'related_thumbnail' ) );

		$sells = sl_setting( 'product_upsells' ) || sl_setting( 'product_related' );

		// If Upsells or Crosssells is checked
		if ( $sells )
		{
			if ( !sl_setting( 'product_sells_price' ) )
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			if ( !sl_setting( 'product_sells_rating' ) )
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			if ( !sl_setting( 'product_sells_button' ) )
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			if ( sl_setting( 'product_sells_excerpt_enable' ) )
				add_action( 'woocommerce_after_shop_loop_item', array( 'Sl_Product_Frontend', 'product_excerpt' ) );
		}

		if ( sl_setting( 'product_upsells' ) )
		{
			echo '<aside id="upsells" class="sl-list posts products columns-' . sl_setting( 'product_similar_columns' ) . '">';
			woocommerce_upsell_display( sl_setting( 'product_sells_amount' ), sl_setting( 'product_similar_columns' ) );
			echo '</aside>';
		}

		if ( sl_setting( 'product_related' ) )
		{
			echo '<aside id="related" class="sl-list posts products columns-' . sl_setting( 'product_similar_columns' ) . '">';
			woocommerce_related_products( sl_setting( 'product_sells_amount' ), sl_setting( 'product_similar_columns' ) );
			echo '</aside>';
		}

		// Restore hooks
		if ( $sells )
		{
			if ( !sl_setting( 'product_sells_price' ) )
				add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			if ( !sl_setting( 'product_sells_rating' ) )
				add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			if ( !sl_setting( 'product_sells_button' ) )
				add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}

		remove_action( 'woocommerce_before_shop_loop_item_title', array( 'Sl_Product_Frontend', 'related_thumbnail' ) );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		?>

	</article>

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div><!-- #main-wrapper -->

<?php get_footer(); ?>
