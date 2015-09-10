<?php
/**
 * The Template for MY COMPANY POSTS
 *
 * @package WordPress
 * @subpackage 7Listings
 */
 
if ( is_user_logged_in() )
{
	global $settings;
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
	{
		get_template_part( 'templates/company/user-admin/no-company' );
		return;
	}
	$company = current( $company );
}
else
{
	get_template_part( 'templates/company/user-admin/form-login' );
	return;
}
?>
<div id="company-admin">
<h2><?php _e( 'Write News Article', '7listings' ); ?></h2>
<form action="" method="post" enctype="multipart/form-data" class="company-form">

	<?php wp_nonce_field( 'add-company-post' ) ?>

	<?php
	global $errors;
	if ( !empty( $errors ) )
		echo '<div class="alert alert-error">' . implode( '<br>', $errors ) . '</div>';
	elseif ( isset( $_GET['updated'] ) )
		echo '<div class="alert alert-success">' . __( 'News has been added to company!', '7listings' ) . '</div>';
	?>

	<input type="hidden" name="company_id" value="<?php echo $company->ID; ?>">

	<div class="row-fluid">
		<div class="span9">
			<label><?php _e( 'Title', '7listings' ); ?> <span class="required">*</span></label>
			<input type="text" name="post_title" required class="span6 title"> <br>
			<label class="description-label"><?php _e( 'Content', '7listings' ); ?> <span class="required">*</span></label>
			<textarea name="post_content" rows="5" class="input-xxlarge"></textarea>
		</div>
		<div class="span3">
			<label><?php _e( 'Featured Image', '7listings' ); ?></label>
			<input type="file" name="thumbnail" onchange="preview();" id="thumbnail">
			<img id="thumbnail-preview" class="photo" src="">
			<script>
			function preview()
			{
				var reader = new FileReader();
				reader.readAsDataURL( document.getElementById( 'thumbnail' ).files[0] );
				reader.onload = function ( e )
				{
					document.getElementById( 'thumbnail-preview' ).src = e.target.result;
				};
			}
			</script>
		</div>
	</div>

	<div class="submit">
		<input type="submit" name="submit" class="button booking large" value="<?php _e( 'Publish', '7listings' ); ?>">
	</div>
</form>

<h2><?php _e( 'My news articles', '7listings' ); ?></h2>
<?php
// List company news
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'author'         => get_current_user_id(),
);

$query = new WP_Query( $args );
if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
	?>
	<article <?php post_class( 'row-fluid' ); ?>>

		<div class="span3">
			<?php
			if ( has_post_thumbnail() )
				the_post_thumbnail( 'four-columns', array(
					'alt'   => the_title_attribute( 'echo=0' ),
					'title' => the_title_attribute( 'echo=0' ),
				) );
			?>
		</div><!-- .span3 -->

		<div class="span9">
			<header class="entry-header">
				<h3 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', '7listings' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h3>
                <?php /*?><?php edit_post_link( __( '', '7listings' ), '<span class="edit-link button small ic-only">', '</span>' ); ?><?php */?>
				<div class="entry-meta">
					<?php sl_post_meta(); ?>
				</div>
			</header>

			<div class="entry-summary">
				<?php
				the_excerpt();
				if ( $settings['post_archive_readmore'] )
				{
					printf(
						'<a href="%s" title="%s" rel="bookmark"%s>%s</a>',
						get_permalink(),
						sprintf( __( 'Permalink to %s', '7listings' ), the_title_attribute( 'echo=0' ) ),
						$settings['post_archive_readmore_type'] == 'button' ? ' class="button"' : '',
						$settings['post_archive_readmore_text']
					);
				}
				?>
			</div>

		</div>

	</article>
	<?php
endwhile;

echo '</div>'; // #company-admin

wp_reset_postdata();
endif;
