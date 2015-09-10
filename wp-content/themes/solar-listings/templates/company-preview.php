<?php
/**
 * Template Name: Company preview
 */
if ( !is_page('login') && !is_user_logged_in()){ 
    auth_redirect(); 
} 
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

		<?php
				if(isset($_GET['n']))
				{
					$company = get_page_by_path($_GET['n'], OBJECT ,'company');
					if($company == null)
					{
						return false;
					}
				} else {
					return false;
				}				

				comment_form( array(
					'title_reply'          => '',
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'label_submit'  => __( 'Submit Review', '7listings' ),
					'logged_in_as'  => '',
					'comment_field' => '
						<h2>Review ' .$company->post_title .' now.</h2>
						<hr class="light">
						<div class="row">
						<div class="span4">
						<div class="comment-rates">
							<span class="detailed-rating">
								<label for="rating_sales">' . __( 'Sales Rep', '7listings' ) .'</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_sales" class="rating-select hidden">
									<option value="">'.__('Rate...', '7listings').'</option>
									<option value="5">'.__('Perfect', '7listings').'</option>
									<option value="4">'.__('Good', '7listings').'</option>
									<option value="3">'.__('Average', '7listings').'</option>
									<option value="2">'.__('Not that bad', '7listings').'</option>
									<option value="1">'.__('Very Poor', '7listings').'</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_service">' . __( 'Service', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_service" class="rating-select hidden">
									<option value="">'.__('Rate...', '7listings').'</option>
									<option value="5">'.__('Perfect', '7listings').'</option>
									<option value="4">'.__('Good', '7listings').'</option>
									<option value="3">'.__('Average', '7listings').'</option>
									<option value="2">'.__('Not that bad', '7listings').'</option>
									<option value="1">'.__('Very Poor', '7listings').'</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_installation">' . __( 'Installation', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_installation" class="rating-select hidden">
									<option value="">'.__('Rate...', '7listings').'</option>
									<option value="5">'.__('Perfect', '7listings').'</option>
									<option value="4">'.__('Good', '7listings').'</option>
									<option value="3">'.__('Average', '7listings').'</option>
									<option value="2">'.__('Not that bad', '7listings').'</option>
									<option value="1">'.__('Very Poor', '7listings').'</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_quality">' . __( 'Quality Of System', '7listings' ) .'</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_quality" class="rating-select hidden">
									<option value="">'.__('Rate...', '7listings').'</option>
									<option value="5">'.__('Perfect', '7listings').'</option>
									<option value="4">'.__('Good', '7listings').'</option>
									<option value="3">'.__('Average', '7listings').'</option>
									<option value="2">'.__('Not that bad', '7listings').'</option>
									<option value="1">'.__('Very Poor', '7listings').'</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_timelyness">' . __( 'Timelyness', '7listings' ) .'</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_timelyness" class="rating-select hidden">
									<option value="">'.__('Rate...', '7listings').'</option>
									<option value="5">'.__('Perfect', '7listings').'</option>
									<option value="4">'.__('Good', '7listings').'</option>
									<option value="3">'.__('Average', '7listings').'</option>
									<option value="2">'.__('Not that bad', '7listings').'</option>
									<option value="1">'.__('Very Poor', '7listings').'</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_price">' . __( 'Price', '7listings' ) .'</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_price" class="rating-select hidden">
									<option value="">'.__('Rate...', '7listings').'</option>
									<option value="5">'.__('Perfect', '7listings').'</option>
									<option value="4">'.__('Good', '7listings').'</option>
									<option value="3">'.__('Average', '7listings').'</option>
									<option value="2">'.__('Not that bad', '7listings').'</option>
									<option value="1">'.__('Very Poor', '7listings').'</option>
								</select>
							</span>
						</div>
						</div>
						<div class="span3">
						<div class="comment-questions">
							<p>
								<label>' . __( 'What size system did you purchase?', '7listings' ) . '</label>
								<select name="size_system">
									<option value="">' . __( 'Select', '7listings' ) . '</option>
									<option value="1.5kW">1.5kW</option>
									<option value="2kW">2kW</option>
									<option value="2.5kW">2.5kW</option>
									<option value="3kW">3kW</option>
									<option value="4kW">4kW</option>
									<option value="5kW">5kW</option>
									<option value="more than 5kW">' . __( 'more than 5kW', '7listings' ) . '</option>
								</select>
							</p>
							<p>
								<label>' . __( 'How much did you spend?', '7listings' ) . '</label>
								<select name="spend">
									<option value="">Select</option>
									<option value="less than $2,500">less than $2,500</option>
									<option value="$2,500 - $4,999">$2,500 - $4,999</option>
									<option value="$5,000 - $9,999">$5,000 - $9,999</option>
									<option value="$10,000 - $14,999">$10,000 - $14,999</option>
									<option value="more than $15,000">more than $15,000</option>
								</select>
							</p>
						</div>
						<div class="vertical comment-form-location">
							<div class="suburb-city">
								<label>' . __( 'City/Suburb', '7listings' ) . '</label>
								<input type="text" name="suburb" id="comment-suburb">
							</div>
							<div class="state">
								<label>' . __( 'State', '7listings' ) . '</label>
								<select name="state" id="comment-state">
									<option>ACT - Canberra</option>
									<option>New South Wales</option>
									<option>Northern Territory</option>
									<option>Queensland</option>
									<option>South Australia</option>
									<option>Tasmania</option>
									<option>Victoria</option>
									<option>Western Australia</option>
								</select>
							</div>
						</div>
						</div>
						</div>
						<div class="row">
							<div class="span7">
								<label for="comment">' . __( 'Your Review', '7listings' ) . '</label>
								<textarea aria-required="true" id="comment" name="comment" cols="45" rows="8" style="width:100%;"></textarea>
							</div>
						</div>'
				), $company->ID);

				?>
				<input type="submit" class="button primary" id="comment-form-submit" value="Submit">
				
				<a href=""></a>

	</article>

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>
<?php get_footer(); ?>
<script type="text/javascript">

jQuery( function( $ )
{
	// Don't add stars and event handler for product single page
	// Let WooCommerce do that
	if ( !Sl || !Sl.hasOwnProperty( 'post_type' ) || 'product' != Sl.post_type )
	{
		// Star ratings
		$( '.stars' ).each( function()
		{
			var $this = $( this ),
				$select = $this.next( 'select' );

			$this.on( 'click', 'a', function()
			{
				var $star = $( this );
				$select.val( $star.text() );
				$star.addClass( 'active' ).siblings().removeClass( 'active' );
				return false;
			} );
		} );
	}
	else
	{
		$( '.add-review a' ).click( removeSpan );
		$( '.span6' ).fitVids();
	}

	/**
	 * Remove wrapping span around stars for product
	 * @return void
	 */
	function removeSpan()
	{
		$( '.stars > span' ).each( function()
		{
			$( this ).replaceWith( this.innerHTML );
		} );
	}

	/*
	$( '#review-form-submit' ).on( 'click', function(e)
	{
		// Validation
		var fields = ['author', 'email', 'comment', 'rating'],
			$field;
		for ( var i = 0, len = fields.length; i < len; i++ )
		{
			$field = $( '#' + fields[i] );
			if ( $field.length && !$field.val() )
			{
				$( '.error-' + fields[i] ).removeClass( 'hidden');
				alert('test');
				return false;
			}
			else
			{
				$( '.error-' + fields[i] ).addClass( 'hidden');
			}
		}

		var $email = $( '#email' );
		if ( $email.length && !rw_utils.is_email( $email.val() ) )
		{
			alert( 'Invalid email address' );
			$email.focus();
			return false;
		}
	} );*/
} );

</script>