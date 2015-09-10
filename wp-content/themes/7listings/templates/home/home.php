<?php get_header(); ?>

<div class="hentry">
	<div class="entry-title" style="display:none"><?php bloginfo( 'name' ); ?></div>

	<?php
	$author = get_bloginfo( 'name' );
	printf(
		__( '<time class="updated entry-date" datetime="%s" style="display:none">%s</time><span class="author vcard" style="display:none"><a class="url fn" href="%s" title="%s" rel="author">%s</a></span>', '7listings' ),
		esc_attr( date( 'c' ) ),
		esc_html( date( 'd/m/Y H:i:s' ) ),
		sl_setting( 'googleplus' ) ? sl_setting( 'googleplus' ) : esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', '7listings' ), $author ) ),
		esc_html( $author )
	);
	?>

	<div id="main-wrapper" class="entry-content home-sections">

		<?php
		require THEME_TPL . 'home/home-modules.php';
		foreach ( sl_setting( 'homepage_order' ) as $id )
		{
			if ( sl_setting( "homepage_{$id}_active" ) )
				sl_homepage_show_module( $id );
		}

		if ( current_user_can( 'manage_options' ) )
			echo '<div class="container"><span class="edit-link button small"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=homepage' ) . '">' . __( 'Edit Page', '7listings' ) . '</a></span></div>';
		?>

	</div>

</div>

<?php get_footer(); ?>
