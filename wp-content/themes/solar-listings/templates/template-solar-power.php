<?php
/**
 * Template Name: Solar Power
 */
get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	the_post();
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'entry-content';
	$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	?>

	<article id="content" <?php post_class( $content_class ); ?>>

		<div id="locations" class="entry-content">
			<h3><?php _e( 'Choose Your State', '7listings' ); ?></h3>

			<div class="row-fluid">
				<div class="span8">
					<div id="map"></div>
				</div>
				<div class="span4">
					<div id="location-buttons">
						<?php
						$buttons       = array(
							'canberra'           => __( 'ACT - Canberra', '7listings' ),
							'new-south-wales'    => __( 'New South Wales', '7listings' ),
							'northern-territory' => __( 'Northern Territory', '7listings' ),
							'queensland'         => __( 'Queensland', '7listings' ),
							'south-australia'    => __( 'South Australia', '7listings' ),
							'tasmania'           => __( 'Tasmania', '7listings' ),
							'victoria'           => __( 'Victoria', '7listings' ),
							'western-australia'  => __( 'Western Australia', '7listings' ),
						);
						global $post;
						$current_state = trim( $post->post_name, '/' );
						$current_state = trim( str_replace( 'solar-power', '', $current_state ), '/' );

						printf(
							'<a href="%s" class="button %s">%s</a>',
							home_url( '/solar-power/' ),
							'' == $current_state ? ' active' : '',
							__( 'All States', '7listings' )
						);

						foreach ( $buttons as $k => $v )
						{
							printf(
								'<a href="%s%s/" class="button %s">%s</a>',
								home_url( '/solar-power/' ),
								$k,
								$k == $current_state ? ' active' : '',
								$v
							);
						}
						?>
					</div>
				</div>
			</div>
		</div>

		<?php peace_action( 'entry_top' ); ?>

		<?php the_content( __( 'Continue reading &rarr;', '7listings' ) ); ?>
		<?php
		wp_link_pages( array(
			'before' => '<p class="pages">' . __( 'Pages:', '7listings' ),
			'after'  => '</p>',
		) );
		?>

		<?php peace_action( 'entry_bottom' ); ?>

		<?php
		if ( sl_setting( 'comments_page' ) )
		{
			if ( comments_open() || ( get_post_meta( get_the_ID(), 'show_old_comments', true ) && get_comments_number() ) )
				comments_template();
		}
		?>

	</article>

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>
<?php get_footer(); ?>
