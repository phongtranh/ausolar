<?php
/**
 * Template Name: Listings
 */
get_header();

$paged = get_query_var( 'paged' );

if ( ! ( $paged ) )
	$paged = 1;

$companies = get_posts( array( 
	'post_type' 		=> 'company',
	'post_status' 		=> 'publish',
	'posts_per_page' 	=> 50,
	'paged'				=> $paged,
	'offset'			=> ( $paged - 1 ) * 50
) );

$count = $wpdb->get_var( "SELECT COUNT(0) FROM asq_posts WHERE post_type = 'company' AND post_status = 'publish'" );
$max_num_pages = ceil( $count / 50 );
?>
<div id="main-wrapper" class="container">
	
	<?php
		$sidebar_layout = sl_sidebar_layout();
		$content_class = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
		$content_class = $content_class ? ' class="' . $content_class . '"' : '';
	?>
	
	<div id="content"<?php echo $content_class; ?>>
		<h1><?php the_title(); ?></h1>
		<ul class="company-archives">
		<?php foreach ( $companies as $company ) : ?>
			<li><a href="<?php echo get_permalink( $company->ID ); ?>" title="<?php echo $company->post_title ?>"><?php echo $company->post_title ?></a></li>
		<?php endforeach; ?>
		</ul>

		<?php
		$query = new stdclass;
		$query->max_num_pages 	= $max_num_pages;

		peace_numeric_pagination( $query );
		?>
	</div>
	
	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>
</div>

<?php get_footer(); ?>