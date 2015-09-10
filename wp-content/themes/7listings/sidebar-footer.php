<?php
/* The footer widget area is triggered if any of the areas
 * have widgets. So let's check that first.
 *
 * If none of the sidebars have widgets, then let's bail early.
 */
if (
	! is_active_sidebar( 'footer-1' ) &&
	! is_active_sidebar( 'footer-2' ) &&
	! is_active_sidebar( 'footer-3' )
)
	return;
// If we get this far, we have widgets. Let do this.
?>
<div id="supplementary" class="row">
	<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<div class="widget-area span4" role="complementary">
			<?php dynamic_sidebar( 'footer-1' ); ?>
		</div>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
		<div class="widget-area span4" role="complementary">
			<?php dynamic_sidebar( 'footer-2' ); ?>
		</div>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
		<div class="widget-area span4" role="complementary">
			<?php dynamic_sidebar( 'footer-3' ); ?>
		</div>
	<?php endif; ?>
</div>