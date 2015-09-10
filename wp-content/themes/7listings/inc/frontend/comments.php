<?php
/**
 * Comments & Reviews Markup
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

add_filter( 'gettext', 'sl_comment_to_review' );
add_filter( 'ngettext', 'sl_comment_to_review' );
add_action( 'comment_post', 'sl_add_comment_rating', 5 );

/**
 * Change word 'comment' to 'review' for listing post types
 *
 * @param string $translated
 *
 * @return string
 */
function sl_comment_to_review( $translated )
{
	$post_type     = get_post_type();
	$listing_types = (array) sl_setting( 'listing_types' );
	if ( 'post' != $post_type && in_array( $post_type, $listing_types ) )
	{
		$translated = strtr( $translated, array(
			'comment' => 'review',
			'Comment' => 'Review',
		) );
	}

	return $translated;
}

/**
 * Rating field for comments
 *
 * @param int $comment_id
 *
 * @return void
 **/
function sl_add_comment_rating( $comment_id )
{
	if ( ! isset( $_POST['rating'] ) )
		return;
	$rating = intval( $_POST['rating'] );

	if ( $rating > 5 || $rating < 0 )
		$_POST['rating'] = 0;
	update_comment_meta( $comment_id, 'rating', $rating );
}

/**
 * Review template, used for other listing types
 *
 * @param object $comment
 * @param array  $args
 * @param int    $depth
 *
 * @return void
 */
function sl_review( $comment, $args, $depth )
{
	$GLOBALS['comment'] = $comment;
	?>
	<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<article id="comment-<?php comment_ID(); ?>" class="post comment">
		<span style="display:none" itemprop="itemReviewed"><?php the_title(); ?></span>

		<?php
		/**
		 * Send 'SL_AVATAR_SINGLE' in last parameter to let Sl_Avatar module know
		 * and replace the alt text with comment author name
		 */
		echo sl_avatar( $comment, 80, '', 'SL_AVATAR_SINGLE' );
		?>

		<div class="details">
			<h4 class="author" itemprop="author"><?php comment_author(); ?></h4>

			<?php sl_star_rating( get_comment_meta( $comment->comment_ID, 'rating', true ), 'type=rating' ); ?>

			<time class="entry-meta date" itemprop="datePublished" datetime="<?php comment_date( 'c' ); ?>"><?php comment_date(); ?></time>

			<?php
			if ( $comment->comment_approved == '0' )
				echo '<p class="pending"><em>' . __( 'Your comment is awaiting approval', '7listings' ) . '</em></p>';
			?>
			<p class="entry-content" itemprop="text"><?php echo strip_tags( get_comment_text(), '<br><a><strong><em><b><i>' ); ?></p>

			<a class="comment-reply-link" href="#" data-comment_id="<?php echo $comment->comment_ID; ?>"><?php _e( 'Reply', '7listings' ); ?></a>
		</div>
	</article>
<?php
}

/**
 * Comment template, used for 'posts' only
 *
 * @param object $comment
 * @param array  $args
 * @param int    $depth
 *
 * @return void
 */
function sl_comment( $comment, $args, $depth )
{
	$GLOBALS['comment'] = $comment;
	?>
<li itemprop="comment" itemscope itemtype="http://schema.org/Comment" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<article id="comment-<?php comment_ID(); ?>" class="post comment">

		<?php
		/**
		 * Send 'SL_AVATAR_SINGLE' in last parameter to let Sl_Avatar module know
		 * and replace the alt text with comment author name
		 */
		echo sl_avatar( $comment, 80, '', 'SL_AVATAR_SINGLE' );
		?>

		<div class="details">
			<h4 class="author" itemprop="author"><?php comment_author_link(); ?></h4>

			<time class="entry-meta date" itemprop="datePublished" datetime="<?php comment_date( 'c' ); ?>"><?php comment_date(); ?></time>

			<?php
			if ( $comment->comment_approved == '0' )
				echo '<p class="pending"><em>' . __( 'Your comment is awaiting approval', '7listings' ) . '</em></p>';
			?>
			<p class="entry-content" itemprop="text"><?php echo strip_tags( get_comment_text(), '<br><a><strong><em><b><i>' ); ?></p>

			<a class="comment-reply-link" href="#" data-comment_id="<?php echo $comment->comment_ID; ?>"><?php _e( 'Reply', '7listings' ); ?></a>
		</div>
	</article>
<?php
}

/**
 * Show review list/grid
 *
 * @param  array $args       Display parameters
 * @param array  $query_args Parameters for query comments. Used in booking sidebar
 *
 * @see templates/tour/booking-sidebar.php
 *
 * @return array
 */
function sl_review_list( $args = array(), $query_args = array() )
{
	$args = array_merge( array(
		'title'          => '',
		'number'         => 4,
		'type'           => array( - 1 ),
		'avatar'         => 1,
		'avatar_size'    => 80,
		'name'           => 1,
		'post_title'     => 1,
		'rating'         => 1,
		'date'           => 1,
		'excerpt'        => 1,
		'excerpt_length' => 25,
		'display'        => 'list',
		'columns'        => 2,
	), $args );

	$query_args = array_merge( array(
		'number' => $args['number'],
		'type'   => 'comment',
		'status' => 'approve',
	), $query_args );
	if ( ! in_array( - 1, (array) $args['type'] ) )
		$query_args['post_type'] = $args['type'];

	$comments = get_comments( $query_args );

	$reviews = array(
		'nums'  => 0,
		'html'  => ''
	);

	if ( empty( $comments ) )
		return $reviews;

	$reviews['nums']    = count( $comments );
	$html               = '';

	foreach ( $comments as $comment )
	{
		$comment_id   = $comment->comment_ID;
		$post_id      = $comment->comment_post_ID;
		$comment_link = get_comment_link( $comment );

		$html .= '<article class="post review">';

		if ( $args['avatar'] )
		{
			/**
			 * Show avatar
			 * - If user has gravatar, use gravatar and set alt/title to user name
			 * - If user does not have gravatar, use post thumbnail and set alt/title to "%post_title% review by %name%"
			 *
			 * Note that we set 'alt' to an unique string which will be replaced later
			 * Setting 'alt' to 'SL_AVATAR_LIST' to let Sl_Avatar know to not replace it
			 *
			 * @see Sl_Avatar::add_title_alt()
			 */
			$default = sl_broadcasted_image_src( '_thumbnail_id', 'sl_thumb_tiny', $post_id );
			$avatar  = sl_avatar( $comment, $args['avatar_size'], $default, 'SL_AVATAR_LIST' );

			$sl_avatar = new Sl_Avatar;
			$alt       = $sl_avatar->get_alt( $comment_id, $post_id );
			$avatar    = str_replace( "alt='SL_AVATAR_LIST'", 'alt="' . $alt . '" title="' . $alt . '"', $avatar );

			$html .= '<a href="' . $comment_link . '" title="' . the_title_attribute( "echo=0&post=$post_id" ) . '">' . $avatar . '</a>';
		}

		$html .= '<span class="details">';

		if ( $args['name'] )
		{
			$author = get_comment_author( $comment_id );
			$html .= "<h4 class='entry-title'><a href='$comment_link' class='author vcard'>$author</a></h4>";
		}

		if ( $args['rating'] )
		{
			$comment_post = get_post( $post_id );

			$average = get_comment_meta( $comment_id, 'rating', true );
			if ( 'company' == get_post_type( $comment_post ) )
			{
				$names = array( 'rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price' );
				$total = 0;
				foreach ( $names as $name )
				{
					$rating = get_comment_meta( $comment_id, $name, true );
					$total += (int) $rating;
				}
				$average = round( $total / 6 );
			}

			$html .= sl_star_rating( $average, 'type=flat&echo=0' );
		}
		if ( $args['date'] )
			$html .= '<time class="entry-meta entry-date date" datetime="' . get_comment_date( 'c', $comment_id ) . '">' . get_comment_date( '', $comment_id ) . '</time>';
		if ( $args['post_title'] )
			$html .= '<h5 class="entry-meta post-title">' . get_the_title( $post_id ) . '</h5>';
		if ( $args['excerpt'] )
		{
			$excerpt = strip_tags( get_comment_text( $comment_id ) );
			$excerpt = wp_trim_words( $excerpt, $args['excerpt_length'], sl_setting( 'excerpt_more' ) );
			$html .= '<span class="entry-summary excerpt">' . $excerpt . '</span>';
		}

		$html .= '</span>'; // .details

		$html .= '</article>';
	}

	$class = 'sl-list posts reviews';
	$class .= 'grid' == $args['display'] ? ' columns-' . $args['columns'] : ' list';

	$reviews['html'] = "<aside class='$class'>$html</aside>";

	return $reviews;
}

/**
 * Get avatar, support retina displays
 *
 * @param int|string  $id      User ID or email
 * @param string      $size    Avatar size
 * @param string      $default URL to default avatar
 * @param string|bool $alt     Alt text for avatar
 *
 * @return string
 */
function sl_avatar( $id, $size, $default = '', $alt = false )
{
	if ( isset( $_COOKIE['retina'] ) )
		$size *= 2;

	return '<figure class="thumbnail">' . get_avatar( $id, $size, $default, $alt ) . '</figure>';
}

/**
 * Get comment fields
 * @return array List of comment fields
 */
function sl_comment_fields()
{
	$commenter = wp_get_current_commenter();
	$fields = array(
		'author' => sprintf( '
			<p class="error error-author hidden">%s</p>
			<p>
				<label for="author">%s</label>
				<input id="author" name="author" type="text" value="%s" class="name" autocomplete="name">
			</p>',
			__( 'Please enter your name', '7listings' ),
			__( 'Name', '7listings' ),
			esc_attr( $commenter['comment_author'] )
		),
		'email'  => sprintf( '
			<p class="error error-email hidden">%s</p>
			<p>
				<label for="email">%s</label>
				<input id="email" name="email" type="email" value="%s" class="email" autocomplete="email">
			</p>',
			__( 'Please enter your email', '7listings' ),
			__( 'Email', '7listings' ),
			esc_attr( $commenter['comment_author_email'] )
	) );
	if ( sl_setting( 'comments_website' ) )
	{
		$fields['url'] = sprintf( '
			<p>
				<label for="url">%s</label>
				<input id="url" name="url" type="text" value="%s" class="website" autocomplete="website">
			</p>',
			__( 'Website', '7listings' ),
			esc_attr( $commenter['comment_author_url'] )
		);
	}

	return $fields;
}
