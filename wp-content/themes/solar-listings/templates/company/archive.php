<?php
/**
 * The Template for displaying Company Archives
 *
 * @package    WordPress
 * @subpackage 7Listings
 */
global $settings, $wpdb, $query_string, $wp_query;

$paged 		= absint( max( 1, get_query_var( 'paged' ) ) ) - 1;
		
$s1  		= isset( $_GET['s1'] ) ? trim( $_GET['s1'] ) : '';
$s2  		= isset( $_GET['s2'] ) ? trim( $_GET['s2'] ) : '';

$post_ids = [];

if ( ! empty( $s1 ) || ! empty( $s2 ) )
{
	$post_ids 		= asq_directory_search( $s1, $s2 );
	if ( count( $post_ids ) >= 3 )
	{
		$top_companies 	= sl_get_top_company_by_rating( 3, $post_ids );
		$rates = [];

		foreach ( $top_companies as $company )
		{
			if ( $company->comment_count >= 10 && $company->vote / $company->comment_count / 6 >= 3 ) 
			{
				$rates[$company->ID] = $company->vote / $company->comment_count / 6;
				$rates[$company->ID] += $company->vote / 1000;
			}
		}
		
		arsort( $rates );
	}
}

//Set text result
$result_message = 'From ' . sl_get_number_of_company() .' Australian solar companies, the top 3 are: ';
if( ! empty( $s2 ) && count( $post_ids ) >= 3 )
{
	$city_name = $s2;
	$result_message = 'From ' . count( $post_ids ) .' solar companies in ' . $city_name . ', the top 3 are: ';
}

get_header();

get_template_part( 'templates/parts/featured-title' );
?>

<div id="main-wrapper" class="container">

	<?php
		$sidebar_layout = sl_sidebar_layout();
		$content_class = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
		$content_class = $content_class ? ' class="' . $content_class . '"' : '';
	?>

	<div class="company-solar-installer-top3">
		<span class="company-solar-installer-quote">Since 2010, we have provided <b><?php echo GFAPI::count_entries(1) * 4; ?> quotes for solar panels</b> across Australia</span>
		<h2 class="company-solar-installer"><?php echo $result_message; ?></h2>

		<div class="top3-company">
		<?php
			if ( isset( $rates ) && count( $rates ) >= 3 )
			{
				$posts = array();
				
				foreach ( $rates as $company_id => $rate )
				{
					foreach ( $top_companies as $company )
					{
						if ( $company_id == $company->ID )
							$posts[$company_id] = $company;
					}
				}
			}
			else
			{
				$posts = sl_get_top_company_by_rating();
			}

			if ( count( $posts ) > 0 ):
				$i = 1;
				foreach( $posts as $post ):
					// Todo: Remove this line below, we don't need to use this query
					$average = Sl_Company_Helper::get_average_rating( $post->ID );
					$logo = '';

					if( has_post_thumbnail( $post->ID ) )
					{
						$logo = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
						$logo = $logo[0];
					}
		?>
				<div class="top3-company-box <?php if ( $i==1 ) echo "company-top3-left"; if ( $i==3 ) echo "company-top3-right"; if ( $i==2 ) echo "company-top3-center"; $i++;?>">
					<div class="company-logo">
						<a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo $post->post_title ?>">
							<img src="<?php echo $logo; ?>">
						</a>
					</div>
					<a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo $post->post_title ?>"><h3 class="company-name"><?php echo $post->post_title; ?> is rated</h3></a>
					<?php if ( $average ) : ?>
						<div class="entry-meta rating"><?php sl_star_rating( $average, 'type=rating' ); ?></div>
					<?php else: ?>
						<div class="entry-meta rating none"><?php _e( 'No Reviews', '7listings' ); ?></div>
					<?php endif; ?>
					<h3>Based on <?php echo Sl_Company_Helper::get_no_reviews($post->ID); ?> Reviews</h3>
				</div>
			<?php endforeach;endif;?>
		</div>
	</div>

	<div id="content"<?php echo $content_class; ?>>

		<?php

		if ( ! empty( $s1 ) || ! empty( $s2 ) )
		{
			if ( count( $post_ids ) > 0 )
			{
				$keyword = '';
				if ( ! empty( $s1 ) && ! empty( $s2 ) )
				{
					$keyword = $s1 .', ' .$s2;
				} elseif( ! empty( $s1 ) ) {
					$keyword = $s1;
				} elseif ( ! empty( $s2 ) ) {
					$keyword = $s2;
				}

				echo '<h3 class="company-search-result">Search results for: ' . $keyword . '</h3>';
				
				sl_company_archive( $post_ids );
			} 
			else 
			{
				echo '<h3>Oops! The company you requested was not found</h3>';
			}
		}

		if ( current_user_can( 'manage_options' ) )
			echo '<span class="edit-link button small page-settings"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=company' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
		?>
	</div>
	<!-- #content -->

	<?php if ( 'none' != $sidebar_layout && count($post_ids) > 0) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>

<?php get_footer(); ?>

<?php
/**
 * Show company posts
 *
 * @param WP_Query $query
 * @param int      $limit
 *
 * @return void
 */
function sl_company_archive( $post_ids = array() )
{
	global $wpdb, $settings, $cached_average_reviews;
	?>
	<div class="content">
		<div id="companies-grid">

			<div class="companies">
				<?php

				$paged 		= absint( max( 1, get_query_var( 'paged' ) ) ) - 1;
				$pagination = 30;
				$limit 		= $paged * $pagination;

				$ids = implode( ',', $post_ids );

				$num_posts = $wpdb->get_var( "SELECT COUNT(0) FROM asq_posts WHERE ID IN ($ids)" );

				$q = "
					SELECT * FROM asq_posts
					WHERE ID IN($ids)
					AND post_type 	= 'company'
					AND post_status NOT IN ( 'draft', 'trash' )
					ORDER BY FIELD(ID, $ids)
					LIMIT {$limit}, {$pagination}
				";

				$posts = $wpdb->get_results( $q );
				$query = new stdclass;
				$query->max_num_pages 	= ceil( $num_posts / $pagination );
				$count = 1;

				foreach ( $posts as $post ) :
					// Companies with ratings first
					$average 	= isset( $cached_average_reviews[$post->ID] ) ? $cached_average_reviews[$post->ID] : Sl_Company_Helper::get_average_rating( $post->ID );
					
					$membership = get_user_meta( get_post_meta( $post->ID, 'user', true ), 'membership', true );
					if ( ! $membership )
						$membership = 'none';
					?>
					<article class="company-listing" data-title="<?php echo $post->post_title ?>" data-rating="<?php echo $average; ?>" data-location="<?php echo $city_name; ?>">
						<div class="company-listing-logo">
							<?php
								$logo = '';
								if ( has_post_thumbnail( $post->ID ) )
								{
									$logo = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
									$logo = $logo[0];
								}
								
								if ( ! empty( $logo ) && in_array( $membership, ['gold', 'silver'] ) ):
							?>
								<a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo $post->post_title ?>">
									<img src="<?php echo $logo; ?>" alt="<?php echo $post->post_title ?>">
								</a>
							<?php endif; ?>
						</div>
						<div class="company-listing-content">

							<div class="company-listing-content-left">
								<div class="company-title">
									<h3>
										<a class="title" href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo $post->post_title ?>">
											<?php echo $post->post_title ?>
										</a>
									</h3>
								</div>
								<?php if ( $average ) : ?>
									<div class="company-rating-phone"><?php sl_star_rating( $average, 'type=rating' ); echo '<p>' .Sl_Company_Helper::get_no_reviews($post->ID) .' reviews </p>';?></div>
									<div class="company-rating rating"><?php sl_star_rating( $average, 'type=rating' ); echo '<p>Based on ' .Sl_Company_Helper::get_no_reviews($post->ID) .' reviews </p>';?></div>
								<?php else: ?>
									<div class="company-rating rating none"><?php _e( 'No Reviews', '7listings' ); ?></div>
								<?php endif; ?>

								<?php if ( current_user_can( 'administrator' ) ) : ?>
								<span class="label label-warning"><?php echo $membership ?></span>
								<?php endif; ?>

								<div class="company-servicing">
									<p>Servicing: <?php
										if ( isset($_GET['s2']) && $_GET['s2'] !== '') 
										{
											echo $_GET['s2'] .', ';
										}
										else 
										{
											$area = get_post_meta( $post->ID, 'area', true );
											if($area !== '')
												echo $area .', ';
										}
										$state    = get_post_meta( $post->ID, 'state', true );
										if ( ! empty( $state ) )
											echo $state .', ';

										$postcode = get_post_meta( $post->ID, 'postcode', true );
										
										if ( ! empty( $postcode ) )
											echo $postcode;
									?></p>
								</div>
								<div class="company-types"><p><?php echo sl_get_type_of_company($post->ID); ?></p></div>
							</div>
							
							<div class="company-listing-content-right">
								<div class="company-phone">
									<span>
									<?php
										$phone = get_post_meta( $post->ID, 'phone', true );
										$pattern = '/(\\d{2})(\\d{4})(\\d{4})/';
										$phone = preg_replace( $pattern, '$1 $2 $3', $phone );
										echo $phone;
									?>
									</span>
								</div>
								<div class="company-add-review">
									<a class="button" href="<?php echo get_permalink( $post->ID ); ?>" ><?php _e( 'Reviews', '7listings' ); ?></a>
								</div>
							</div>
						</div>

					</article>

					<?php if ( $count % 8 === 0 && ! wp_is_mobile() && url_contains( '/solar-installers/' ) ) : ?>
						<div class="adrotate_newsroom_pc" id="adrotate_newsroom_pc">
							<span class='close-adrotate'>x</span>
							<?php echo adrotate_group( 17 ); ?>
						</div>
					<?php endif;

						$count++;
					endforeach;
					?>

					<?php if ( ! wp_is_mobile() && url_contains( '/solar-installers/' ) ) : ?>
						<div class="adrotate_newsroom_pc" id="adrotate_newsroom_pc">
							<span class='close-adrotate'>x</span>
							<?php echo adrotate_group( 18 ); ?>
						</div>
					<?php endif;?>
			</div>
			<div class="animation_image" style="display:none" align="center"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/ajax-loader.gif" /></div>
			<?php if(count($post_ids) > 30): ?><div class="load-more-company">Load more companies</div><?php endif;?>
		</div>
	</div>
	<?php
}
?>

<?php
	$company_ids = '';
	if ( count( $post_ids ) > 0 )
		$company_ids = implode( ',', $post_ids );
?>
<script type="text/javascript">
$(document).ready(function() {
	var page = 1;
	$(".load-more-company").on("click", function () {
        var ids = '<?php echo $company_ids; ?>';
		var data = {page:page, ids:ids};
		$('.animation_image').show();
		$('.load-more-company').hide();
		$.ajax({
            type: "POST",
            dataType: 'json',
            url: 'https://www.australiansolarquotes.com.au/wp-admin/admin-ajax.php?action=get_company',
            data: data,
            cache: false,
            success: function (response) {
                if (response['error_code'] === 0) {
                	html = response['posts'];
                	if(html != '') {
                		$('.companies').append(html);
	                	page++;
	                    $('.animation_image').hide();
	                    $('.load-more-company').show();
                	} else {
                		$('.load-more-company').hide();
                		$('.animation_image').hide();
                		return false;
                	}
                	
                } else {
                    console.log('fail');
                }
                
                return true;
            }
        });
        return false;
    });
});
</script>