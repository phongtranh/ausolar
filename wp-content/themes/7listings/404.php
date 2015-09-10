<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<article id="content" class="entry-content full">
		<?php
		global $wp;

		$link = isset( $_SERVER['HTTPS'] ) && 'on' == $_SERVER['HTTPS'] ? 'https://' : 'http://';
		if ( 80 != $_SERVER['SERVER_PORT'] )
		{
			$link .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		}
		else
		{
			$link .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}

		$search_term = str_replace( HOME_URL, '', $link );
		$search_term = preg_split( '#[^a-z ]+#i', $search_term );
		$search_term = array_unique( array_filter( $search_term ) );

		$search_term = array_diff( $search_term, $wp->public_query_vars );
		$search_term = array_pop( $search_term );

		$post_types   = sl_setting( 'listing_types' );
		$post_types[] = 'post';
		$post_types[] = 'page';
		$post_types   = array_unique( $post_types );

		$query = new WP_Query( array(
			's'              => $search_term,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'post_type'      => $post_types,
		) );
		if ( $query->have_posts() )
		{
			echo '<h3>' . __( 'Maybe the page has moved here:', '7listings' ) . '</h3>';

			echo '<ul id="suggestions">';
			while ( $query->have_posts() )
			{
				$query->the_post();
				?>
				<li>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				</li>
			<?php
			}
			echo '</ul>';

			wp_reset_postdata();
		}
		?>
	</article>
	<!-- #post-0 -->
</div>

<?php get_footer(); ?>
