<?php
/**
 * The Template for displaying PRODUCT ARCHIVES
 * Grid Layout
 *
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'sl-list archive posts products';
	if ( 'list' == sl_setting( 'product_archive_layout' ) )
		$content_class .= ' list';
	else
		$content_class .= ' columns-' . sl_setting( 'product_archive_columns' );
	$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	$content_class = $content_class ? ' class="' . $content_class . '"' : '';
	?>

	<div id="content"<?php echo $content_class; ?>>

		<?php do_action( 'woocommerce_before_shop_loop' ); ?>

		<?php
		// Display product categories
		$shop_page_display  = get_option( 'woocommerce_shop_page_display' );
		$category_display   = get_option( 'woocommerce_category_archive_display' );

		if ( ( $shop_page_display && is_post_type_archive() ) || ( $category_display && is_tax() ) )
		{
			get_template_part( 'templates/product/categories' );
		}
		?>

		<?php
		if ( ( 'subcategories' != $shop_page_display && is_post_type_archive() )
			|| ( 'subcategories' != $category_display && is_tax() )
		)
		{
			woocommerce_product_loop_start();

			while ( have_posts() ) : the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile; // end of the loop.

			woocommerce_product_loop_end();

			do_action( 'woocommerce_after_shop_loop' );
		}
		?>

	</div>
	<!-- #content -->

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

	<?php
	if ( current_user_can( 'manage_options' ) )
		echo '<span class="edit-link button small page-settings"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=product' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
	?>

</div><!-- #main-wrapper -->

<?php get_footer(); ?>
