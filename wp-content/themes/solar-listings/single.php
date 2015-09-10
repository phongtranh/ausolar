<?php get_header(); ?>
<?php the_post(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
		$sidebar_layout = sl_sidebar_layout();
		$content_class  = 'sl-single';
		$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	?>

	<article id="content" <?php post_class( $content_class ); ?>>

		<?php
		peace_action( 'entry_top' );

		/**
		 * Get image for news article
		 * It is required from Google Structure Markup
		 * @link https://developers.google.com/structured-data/rich-snippets/articles
		 */
		$thumb = sl_broadcasted_image_src( '_thumbnail_id', 'full' );
		echo "<img class='hidden' itemprop='image' src='$thumb' alt='thumbnail'>";

		echo '<div id="description" class="entry-content" itemprop="articleBody">';
		the_content();
		echo '</div>';

		wp_link_pages( array(
			'before' => '<p class="pages">' . __( 'Pages:', '7listings' ),
			'after'  => '</p>',
		) );

		if ( comments_open() && sl_setting( 'post_comment_status' ) )
			echo '<span class="comments-link"><a href="#comment-form" data-toggle="modal">' . __( 'Add Comment', '7listings' ) . '</a></span>';

		edit_post_link( __( 'Edit Post', '7listings' ), '<span class="edit-link button small">', '</span>' );
		if ( current_user_can( 'manage_options' ) )
			echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=post#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';

		if(is_single()) {author_detail_box();}

		if (
			( comments_open() || get_comments_number() ) &&
			( sl_setting( 'post_comment_status' ) || sl_setting( 'post_ping_status' ) )
		)
		{
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

<?php
	function author_detail_box()
	{
?>
	<section id="blog-author-details">
		<h3><?php _e( 'The Author', '7listings' ); ?></h3>

		<?php echo sl_avatar( get_the_author_meta( 'ID' ), 200 ); ?>

		<div class="details">
			<h2 class="entry-meta author name" id="author-name">
				<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?>"><?php the_author(); ?></a>
			</h2>
			<p class="entry-content bio" id="author-bio"><?php the_author_meta( 'description' ); ?></p>
			<?php author_social_links(); ?>
		</div>
	</section>
<?php
	}

	function author_social_links()
	{
		$classes = 'author';
		if ( sl_setting( 'design_social_icon_color_scheme' ) )
			$classes .= ' ' . sl_setting( 'design_social_icon_color_scheme' );
		$shortcode = '[social_links class="' . $classes . '"';
		$networks  = array(
			'facebook',
			'twitter',
			'googleplus',
			'pinterest',
			'linkedin',
			'instagram',
			'rss',
		);
		$has_link  = false;
		foreach ( $networks as $network )
		{
			if ( $link = get_the_author_meta( $network ) )
			{
				$shortcode .= ' ' . $network . '="' . $link . '"';
				$has_link = true;
			}
		}
		$shortcode .= ']';
		if ( $has_link )
			echo do_shortcode( $shortcode );
	}
?>
