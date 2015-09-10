<?php

/**
 * This class handles common things for module for #featured title area
 */
class Sl_Post_Featured_Title extends Sl_Core_Featured_Title
{
	/**
	 * Add hooks for featured title for singular pages
	 *
	 * @return void
	 * @since 5.0.10
	 */
	function singular()
	{
		// Show single listing featured image as background
		if ( sl_setting( $this->post_type . '_single_featured_title_image' ) )
			add_filter( self::$prefix . 'style', array( $this, 'single_background' ) );

		// Add more classes to #featured title area
		add_filter( self::$prefix . 'class', array( $this, 'single_class' ) );

		if ( sl_setting( 'post_single_meta' ) )
			add_action( self::$prefix . 'bottom', array( $this, 'single_meta' ) );
	}

	/**
	 * Add hooks for featured title for archive pages
	 *
	 * @return void
	 * @since 5.0.10
	 */
	function archive()
	{
		// Show term image
		if ( sl_setting( $this->post_type . '_archive_cat_image' ) && ! sl_setting( $this->post_type . '_archive_map' ) )
		{
			$image_type = sl_setting( $this->post_type . '_archive_cat_image_type' );
			if ( 'background' == $image_type )
				add_filter( self::$prefix . 'style', array( $this, 'archive_background' ) );
			elseif ( 'thumbnail' == $image_type )
				add_action( self::$prefix . 'top', array( $this, 'archive_logo' ) );
		}

		// Add more classes to #featured title area
		add_filter( self::$prefix . 'class', array( $this, 'archive_class' ) );

		// Archive page
		add_action( self::$prefix . 'bottom', array( $this, 'tax_description' ) );
		add_action( self::$prefix . 'bottom', array( $this, 'archive_page' ), 20 );

		// Add more classes to #featured title area
		add_filter( self::$prefix . 'class', array( $this, 'archive_class' ) );
	}

	/**
	 * Add background style for featured title area
	 *
	 * @param string $style Inline styles for featured title area
	 *
	 * @return string
	 * @since 5.0.10
	 */
	function single_background( $style )
	{
		if ( $image = sl_broadcasted_image_src( '_thumbnail_id', sl_setting( get_post_type() . '_single_featured_title_image_size' ) ) )
			$style .= 'background-image: url(' . $image . ');';

		return $style;
	}

	/**
	 * Add more classes to featured title area
	 *
	 * @param array $class
	 *
	 * @return array
	 * @since 5.0.10
	 */
	function single_class( $class )
	{
		// Add height class
		$height = sl_setting( $this->post_type . '_single_featured_title_height' );
		if ( $height && 'medium' != $height )
			$class[] = $height;

		return $class;
	}

	/**
	 * Display single post meta in featured title area
	 *
	 * @return void
	 */
	public function single_meta()
	{
		echo '<div class="entry-meta-wrapper">';
		$info = array(
			sl_listing_element( 'date' ),
			sl_listing_element( 'categories' ),
			sl_listing_element( 'comments' ),
		);
		echo implode( ' <span class="sep">|</span> ', array_filter( $info ) );
		echo sl_listing_element( 'tags' );
		echo apply_filters( 'sl_post_after_post_meta', '' );
		echo '</div>';
	}

	/**
	 * Add filter for archive page
	 * @return void
	 */
	function archive_page()
	{
		echo apply_filters( 'sl_archive_page', '' );
	}

	/**
	 * Display taxonomy description
	 *
	 * @return void
	 */
	function tax_description()
	{
		if (
			sl_setting( get_post_type() . '_archive_cat_desc' ) &&
			( is_tax() || is_category() || is_tag() ) &&
			( $desc = term_description() )
		)
		{
			echo '<h3>' . strip_tags( $desc, '<br>' ) . '</h3>';
		}
	}

}
