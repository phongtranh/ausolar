<?php
/**
 * Contain functions use for company
 * User: TaiDN
 */

/**
 * Get company ids that sorted by rating
 *
 * @return array
 */
function sl_get_company_ids_sorted_by_rating ()
{
	global $wpdb;
	$limit = absint( sl_setting( 'company_archive_num' ) );
	$query = "
		SELECT DISTINCT c.comment_post_ID
		FROM {$wpdb->comments} c LEFT JOIN {$wpdb->commentmeta} cm ON cm.comment_id = c.comment_ID
		WHERE cm.meta_key IN ( 'rating_sales', 'rating_service', 'rating_installation' ,'rating_quality' ,'rating_timelyness' , 'rating_price' ) AND c.comment_approved = '1'
		ORDER BY AVG(cm.meta_value) DESC
		LIMIT {$limit}
	";
	return $wpdb->get_col( $query );
}

/**
 * get average rating follow types of company
 *
 * @param int       $post_id
 * @param string    $type
 *
 * @return int
 */
function sl_get_rating_average_of_type( $post_id = null ,$type = 'rating_sales' )
{
	global $wpdb;

	$rating = 0;

	$result = $wpdb->get_row( $wpdb->prepare( "
		SELECT COUNT(*) AS count, SUM(meta_value) AS total
		FROM $wpdb->commentmeta as m
		LEFT JOIN $wpdb->comments as c ON m.comment_id = c.comment_ID
		WHERE
			meta_key = '%s' AND
			comment_post_ID = %d AND
			comment_approved = 1
		", $type, $post_id ), 'ARRAY_A' );

	if ( !empty( $result ) && 0 != $result['count'] )
		$rating = number_format( ( float ) $result['total'] / $result['count'], 2 );

	return $rating;
}

/**
 * Report for singular page
 * @return array Report array
 */
function sl_get_single_by_id( $company_id )
{
//	$company_id = $_GET['company_id'];

	// Turn $company_id to $company object
	$company       = get_post( $company_id );
	$company_leads = get_company_leads( $company_id, 'array' );

	$total_leads    = count( $company_leads );

	$all_rejected   = solar_get_rejected_leads( $company );

	$rejected_leads = array();
	$approved_leads = array();

	if ( ! empty( $company_leads ) )
	{
		foreach ( $company_leads as $lead )
		{
			if ( isset( $all_rejected[$lead] ) )
				$rejected_leads[$lead] = $all_rejected[$lead];
			else
				$approved_leads[$lead] = $lead;
		}
	}

	$sources = solar_get_leads_sources( $approved_leads );
	$incomes = solar_get_income( $sources );

	$reasons = array_count_values( $rejected_leads );

	return compact( 'company', 'company_leads', 'total_leads', 'rejected_leads', 'reasons', 'sources', 'incomes' );
}

/**
 * Get top company by rating
 * @param int $limit
 * @return list company
 */
function sl_get_top_company_by_rating( $limit = 3, $post_ids = array() )
{
	global $wpdb;

	$in_post_ids = '';

	if ( is_array( $post_ids ) && ! empty( $post_ids ) )
	{
		$post_ids = join( ',', $post_ids );
		
		$in_post_ids = " AND ID IN($post_ids) ";
	}

	$q = "
		SELECT
			*, (
				SELECT
					SUM(meta_value)
				FROM
					asq_commentmeta
				LEFT JOIN asq_comments ON asq_commentmeta.comment_id = asq_comments.comment_ID
				WHERE
					meta_key IN ('rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price')
				AND comment_post_ID = asq_posts.ID
				AND comment_approved = '1'
			) AS vote
		FROM
			asq_posts
		WHERE
			post_type = 'company'
		AND post_status NOT IN ('draft', 'trash')
		{$in_post_ids}
		ORDER BY
			vote DESC
		LIMIT {$limit}";
	$posts = $wpdb->get_results( $q );
	return $posts;
}

/**
 * Get list types of company
 * @param $post_id
 * @return string
 */
function sl_get_type_of_company( $post_id = null)
{
	global $wpdb;
	$terms = wp_get_post_terms( $post_id, 'company_types', array("fields" => "names") );
	return implode(", ",$terms);
}

/**
 * Get number of companys
 */
function sl_get_number_of_company()
{
	global $wpdb;
	$num_posts = $wpdb->get_var( "SELECT COUNT(0) FROM asq_posts WHERE post_type = 'company' AND post_status NOT IN ('draft', 'trash')" );
	return ( int ) $num_posts;
}

/**
* Get number of review from all company
*
*
* @return int
*/
function sl_get_no_review_of_companys()
{
	global $wpdb;

	$count = $wpdb->get_var( $wpdb->prepare( "
		SELECT COUNT(*)
		FROM $wpdb->commentmeta AS m
		LEFT JOIN $wpdb->comments AS c ON m.comment_id = c.comment_ID
		WHERE
			meta_key = %s AND
			comment_approved = 1
		", 'rating_sales') );

	return ( int ) $count;
}

function sl_get_comments_of_company( $comment, $args, $depth )
{

	$GLOBALS['comment'] = $comment;

	$names = array( 'rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price' );
	$total = 0;
	foreach ( $names as $name )
	{
		$rating = get_comment_meta( $comment->comment_ID, $name, true );
		$total += (int) $rating;
	}
	$average = $total / 6;
	?>
	<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<article id="comment-<?php comment_ID(); ?>" class="post comment">
		<span style="display:none" itemprop="itemReviewed"><?php the_title(); ?></span>

		<?php echo sl_avatar( $comment, 80 ); ?>

		<div class="details">
			<h4 class="author" itemprop="author"><?php comment_author(); ?></h4>

			<?php sl_star_rating( $average, 'type=rating' ); ?>

			<time class="entry-meta date" itemprop="datePublished" datetime="<?php comment_date( 'c' ); ?>"><?php comment_date(); ?></time>

			<?php
			if ( $comment->comment_approved == '0' )
				echo '<p class="pending"><em>' . __( 'Your review is awaiting approval', '7listings' ) . '</em></p>';
			?>
			<p class="entry-content" itemprop="text"><?php echo strip_tags( get_comment_text(), '<br><a><strong><em><b><i>' ); ?></p>

			<div class="social-box">
				<?php if(!sl_is_report_of_comment($comment->comment_ID)): ?>
				<a class="comment-reply-link" href="#" data-comment_id="<?php echo $comment->comment_ID; ?>"><?php _e( 'Reply', '7listings' ); ?></a>
				<div class="social-box-share">
					<div class="social-box-share-parent">
						<a href="#">Share</a>
						<div class="social-box-share-dropdown">
						<?php
							$url = get_permalink($comment->comment_post_ID) .'#comment-' .$comment->comment_ID;
							$title = $comment->comment_author .' rates ' .get_the_title($comment->comment_post_ID) .' ' 
										.(int) $average .' out of 5 stars on AustralianSolarQuotes.com.au';
							$description = strip_tags( get_comment_text(), '<br><a><strong><em><b><i>' );
						?>
							<?php echo facebook_share_button($url, $title, $description); ?>
							<?php echo google_share_button($url); ?>
							<?php
								$bitly = bitly_v3_shorten($url, '4e94b38adf63b963951753553e1ded7052b48348');
								if(isset($bitly['url']))
									$url = $bitly['url'];
								echo twitter_share_button($url, $title);
							?>
						</div>
					</div>
				</div>
				<a class="comment-report-link" href="#" data-comment_id="<?php echo $comment->comment_ID; ?>"><?php _e( 'Report', '7listings' ); ?></a>
				<?php else :?>
					<p class="comment-report-flag">Under review</p>
				<?php endif ;?>
			</div>
		</div>
	</article>
<?php
}

function sl_is_report_of_comment( $comment_id )
{
	global $wpdb;
	$row = $wpdb->get_row( "SELECT * FROM asq_rg_lead_meta WHERE meta_key = 'comment_id' AND form_id = 61 AND meta_value=" .$comment_id );
	
	return $row != null;
}