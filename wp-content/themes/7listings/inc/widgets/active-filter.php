<?php

/**
 * Layered navigation filters widget
 */
class Sl_Active_Filters extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array( 'title' => '' );
	/**
	 * Widget constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'sl-listing-layered-nav-filter',
			__( '7 - Active Filters', '7listings' ),
			array(
				'classname'   => 'widget_layered_nav_filters',
				'description' => __( 'Display active filters so users can see and deactivate them.', '7listings' ),
			)
		);
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		if ( ! is_archive() || is_post_type_archive( 'product' ) || is_tax( get_object_taxonomies( 'product' ) ) )
			return;

		// Price
		$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : 0;
		$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : 0;

		$taxonomies      = array( 'tour_type', 'type', 'rental_type', 'category', 'features', 'location','brand', 'company_product', 'company_service' );
		$taxonomy_filter = array();
		foreach ( $taxonomies as $taxonomy )
		{
			if ( isset( $_GET["filter_{$taxonomy}"] ) )
				$taxonomy_filter[] = $_GET["filter_{$taxonomy}"];
		}

		if (  0 < count( $taxonomy_filter  ) || 0 < $min_price || 0 < $max_price )
		{
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) )
			{
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			echo '<ul>';

			// Attributes
			foreach ( $_GET as $key => $_chosen_attributes )
			{
				if( 'min_price' == $key || 'max_price' == $key || 'post_type' == $key )
					continue;

				$taxonomy_filter = str_replace( 'filter_', '', $key );
				$_chosen_attributes  = explode( ',', $_chosen_attributes );
				if ( ! is_null( $_chosen_attributes ) )
				{
					foreach ( $_chosen_attributes as $term_id )
					{
						$term = get_term( $term_id, $taxonomy_filter );

						$new_filter = array_map( 'absint', $_chosen_attributes );

						$new_filter = array_diff( $new_filter, array( $term_id ) );

						if ( 'start' == $key )
						{
							$link = remove_query_arg( 'start' );
							$name = __( 'Alphabet: ', '7listings' ) . ucfirst( $term_id );
						}
						elseif ( 'filter_brand' == $key )
						{
							$link = remove_query_arg( 'filter_brand' );
							$term = get_term_by('slug', $term_id, 'brand');
							$name = $term->name;
						}
						else
						{
							$link = remove_query_arg( "filter_{$taxonomy_filter}" );
							$name = $term->name;
						}

						if ( sizeof( $_chosen_attributes ) > 1 )
						{
							$link = add_query_arg( "filter_{$taxonomy_filter}", implode( ',', $new_filter ), $link );
						}

						$link = str_replace( '%2C', ',', $link );

						echo '<li class="chosen"><a title="' . __( 'Remove filter', '7listings' ) . '" href="' . esc_url( $link ) . '">' . $name . '</a></li>';
					}
				}
			}

			if ( $min_price )
			{
				$link = remove_query_arg( 'min_price' );
				echo '<li class="chosen"><a title="' . __( 'Remove filter', '7listings' ) . '" href="' . esc_url( $link ) . '">' . __( 'Min', '7listings' ) . ' ' . Sl_Currency::format( $min_price, 'type=plain' ) . '</a></li>';
			}

			if ( $max_price )
			{
				$link = remove_query_arg( 'max_price' );
				echo '<li class="chosen"><a title="' . __( 'Remove filter', '7listings' ) . '" href="' . esc_url( $link ) . '">' . __( 'Max', '7listings' ) . ' ' . Sl_Currency::format( $max_price, 'type=plain' ) . '</a></li>';
			}

			echo '</ul>';
			echo $args['after_widget'];
		}

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );

		include THEME_INC . 'widgets/tpl/title.php';
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
