<?php

/**
 * This class will hold all things for slideshow management page
 */
class Sl_Slideshow_Management extends Peace_Post_Management
{
	/**
	 * Post type
	 *
	 * @var string
	 */
	var $post_type = 'slideshow';

	/**
	 * Add more action to add class to <body>
	 *
	 * @return void
	 */
	function execute()
	{
		parent::execute();

		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Add class to <body>
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function body_class( $classes )
	{
		$screen = get_current_screen();
		if ( $screen->post_type == $this->post_type )
			$classes .= $this->post_type;

		return $classes;
	}

	/**
	 * Change the columns for the edit screen
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	function columns( $columns )
	{
		$columns = array(
			'cb'        => '<input type="checkbox">',
			'title'     => 'Title',
			'image'     => 'Images',
			'shortcode' => 'Shortcode',
			'author'    => 'Author',
			'date'      => 'Date',
		);

		return $columns;
	}

	/**
	 * Show the columns for the edit screen
	 *
	 * @param string $column
	 * @param int    $post_id
	 *
	 * @return void
	 */
	function show( $column, $post_id )
	{
		switch ( $column )
		{
			case 'image':
				$photos = get_post_meta( $post_id, sl_meta_key( 'photos', $this->post_type ), false );
				if ( empty( $photos ) )
					break;

				$li_tpl = '<li><img src="%s"></li>';
				$output = '<ul>';
				foreach ( $photos as $photo )
				{
					$photo_src = wp_get_attachment_image_src( $photo );
					$photo_src = $photo_src[0];
					$output .= sprintf(
						$li_tpl,
						$photo_src
					);
				}
				$output .= '</ul>';
				echo $output;
				break;
			case 'shortcode':
				echo '<code>[slideshow id="' . $post_id . '"]</code>';
				break;
			default:
		}
	}

}

new Sl_Slideshow_Management;
