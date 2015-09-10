<?php
$marketing = get_post_meta( get_the_ID(), 'marketing', true );

$marketing = @unserialize( $marketing );

if ( isset( $marketing['news_posts'] ) )
	$news_posts = $marketing['news_posts'];

if ( isset( $marketing['social_shares'] ) )
	$social_shares = $marketing['social_shares'];

?>
<div class="row-fluid">
	<div class="span12">
		<h3>Content Creation &amp; Publication</h3>
		
		<?php 
		if ( isset( $news_posts ) ):
		foreach ( $news_posts as $index => $news_post ): ?>
		<div class="form-group">
			<label><?php _e( 'News Post', '7listings' ) ?> <?php echo $index + 1 ?></label>
			<input type="date" name="news_posts[date][]" value="<?php echo $news_post['date'] ?>" />
			<input type="text" name="news_posts[url][]" value="<?php echo $news_post['url'] ?>" />  
		</div>
		<?php endforeach; endif; ?>

		<div class="items-unsaved" id="news-post">
			<div class="form-group item-unsaved">
				<label><?php _e( 'News Post', '7listings' ) ?></label>
				<input type="date" name="news_posts[date][]" value="<?php echo date('Y-m-d'); ?>" />
				<input type="text" name="news_posts[url][]" />  
			</div>
		</div>

		<input type="button" class="add-item" data-target="#news-post" value="Add News Post" />
		
		<hr />
		
		<h3>Social Media Sharing</h3>
		<?php 
		if ( isset( $social_shares ) ):
		foreach ( $social_shares as $index => $social_shares ): ?>
		<div class="form-group">
			<label><?php _e('Social Shares', '7listings' ) ?> <?php echo $index + 1 ?></label>
			<input type="date" name="social_shares[date][]" value="<?php echo $social_shares['date'] ?>" />
			<input type="text" name="social_shares[url][]" value="<?php echo $social_shares['url'] ?>" />  
		</div>
		<?php endforeach; endif; ?>

		<div class="items-unsaved" id="social-shares">
			<div class="form-group item-unsaved">
				<label><?php _e( 'Social Share', '7listings' ) ?></label>
				<input type="date" name="social_shares[date][]" value="<?php echo date('Y-m-d'); ?>" />
				<input type="text" name="social_shares[url][]" />  
			</div>
		</div>

		<input type="button" class="add-item" data-target="#social-shares" value="Add Social Shares" />
		
	</div>
</div>

<script type="text/javascript">
jQuery( function( $ )
{
	$( '.add-item' ).click( function()
	{
		$target = $( this ).data( 'target' );

		$( $target ).append( $( $target ).find( '.item-unsaved' ).first().html() );
	} );
} );
</script>

<style type="text/css">
	.items-unsaved{ opacity: .6; }
</style>