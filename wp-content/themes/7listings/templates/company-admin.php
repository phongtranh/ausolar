<?php
/**
 * Template Name: Company Admin
 */
?>

<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<div class="row">

		<?php
		the_post();
		$sidebar_layout = sl_sidebar_layout();
		$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
		$content_class  = $content_class ? ' class="' . $content_class . '"' : '';
		?>

		<div id="content"<?php echo $content_class; ?>>

			<article <?php post_class(); ?>>
				<?php peace_action( 'entry_top' ); ?>

				<div class="entry-content">
					<?php if ( is_user_logged_in() ) : ?>
						<?php the_content( __( 'Continue reading &rarr;', '7listings' ) ); ?>
						<?php
						wp_link_pages( array(
							'before' => '<p class="pages">' . __( 'Pages:', '7listings' ),
							'after'  => '</p>',
						) );
						?>
					<?php else : ?>
						<?php get_template_part( 'templates/company/user-admin/form-login' ); ?>
					<?php endif; ?>
				</div>

				<?php peace_action( 'entry_bottom' ); ?>
			</article>

			<?php
			if ( is_user_logged_in() && sl_setting( 'comments_page' ) )
			{
				if ( comments_open() || ( get_post_meta( get_the_ID(), 'show_old_comments', true ) && get_comments_number() ) )
					comments_template();
			}
			?>

		</div>

		<?php if ( 'none' != $sidebar_layout ) : ?>
			<div class="<?php echo $sidebar_layout ?>" id="sidebar">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>
	</div>

</div>

<?php get_footer(); ?>
