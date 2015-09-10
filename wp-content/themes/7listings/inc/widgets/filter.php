<?php

class Sl_Widget_Filter extends WP_Widget
{
	/**
	 * @var array Default values of widget
	 */
	public $default = array(
		'post_type'                  => 'tour',
		'title'                      => '',
		'filter_by'                  => '',
		'order_by'                   => 'name',
		'dropdown'                   => 0,
		'count'                      => 1,
		'hierarchical'               => 1,
		'show_children_only'         => 1,
		'query_type'                 => 'and',

		'company_filter_by'          => '',
		'company_order_by'           => 'name',
		'company_dropdown'           => 0,
		'company_count'              => 1,
		'company_hierarchical'       => 1,
		'company_show_children_only' => 1,
		'company_query_type'         => 'and',
	);

	/**
	 * Widget constructor
	 */
	function __construct()
	{
		parent::__construct(
			'sl-listing-filter',
			__( '7 - Filter', '7listings' ),
			array(
				'classname'   => 'widget_filter',
				'description' => __( 'Add a filter which lets you narrow down displayed listings.', '7listings' ),
			)
		);

		if ( is_active_widget( false, false, $this->id_base ) )
		{
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 100 );
		}
	}

	/**
	 * Save form
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		$instance['post_type'] = strip_tags( $new_instance['post_type'] );
		$instance['title']     = strip_tags( $new_instance['title'] );

		$instance['filter_by']          = strip_tags( $new_instance['filter_by'] );
		$instance['order_by']           = strip_tags( $new_instance['order_by'] );
		$instance['dropdown']           = strip_tags( $new_instance['dropdown'] );
		$instance['count']              = strip_tags( $new_instance['count'] );
		$instance['hierarchical']       = strip_tags( $new_instance['hierarchical'] );
		$instance['show_children_only'] = strip_tags( $new_instance['show_children_only'] );
		$instance['query_type']         = strip_tags( $new_instance['query_type'] );

		$instance['company_filter_by']          = strip_tags( $new_instance['company_filter_by'] );
		$instance['company_order_by']           = strip_tags( $new_instance['company_order_by'] );
		$instance['company_dropdown']           = strip_tags( $new_instance['company_dropdown'] );
		$instance['company_count']              = strip_tags( $new_instance['company_count'] );
		$instance['company_hierarchical']       = strip_tags( $new_instance['company_hierarchical'] );
		$instance['company_show_children_only'] = strip_tags( $new_instance['company_show_children_only'] );
		$instance['company_query_type']         = strip_tags( $new_instance['company_query_type'] );

		return $instance;
	}

	/**
	 * Display widget form in admin
	 * @param array $instance Widget configuration
	 * @return void
	 */
	function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );

		// Get available post types
		$post_types = array( 'tour','accommodation', 'rental', 'company' );

		foreach ( $post_types as $k => $post_type )
		{
			if ( ! post_type_exists( $post_type ) )
			{
				unset( $post_types[$k] );
			}
		}
		$field_post_type = esc_attr( $this->get_field_name( 'post_type' ) );
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings toggle-choices<?php echo 1 < count( $post_types ) ? '' : ' hidden'; ?>">
			<div class="sl-label">
				<label>
					<?php _e( 'Post Type', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input post-type-input">
				<?php
				$i = 209;
				foreach ( $post_types as $post_type )
				{
					if ( 'company' == $post_type )
						$post_type = 'companie';
					elseif ( 'post' == $post_type )
						$post_type = 'wp-post';

					printf( ' <input type="radio" id="%ss" name="%s" value="%s" tabindex="%s" %s>
 									<label for="%ss" class="%ss icon" title="Select %ss">
 									%s</label>',
						$post_type,
						$field_post_type,
						$post_type,
						$i,
						$post_type == $instance['post_type'] ? 'checked="checked"' : '',
						$post_type,
						$post_type,
						ucfirst( $post_type ),
						ucfirst( $post_type )
					);
					$i ++;
				}
				?>
			</div>
		</div>
		<div class="sl-sub-settings filter-parameters" data-name="<?php echo $field_post_type; ?>" data-value="companies">
			<div class="sl-settings toggle-choices">
				<div class="sl-label">
					<label>
						<?php _e( 'Filter', '7listings' ); ?>
						<?php echo do_shortcode( '[tooltip content="' . __( 'Select filter criteria', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
					</label>
				</div>
				<div class="sl-input">
					<select name="<?php echo $this->get_field_name( 'company_filter_by' ); ?>">
						<?php
						$types = array(
							'alphabet'        => __( 'Alphabet', '7listings' ),
							'brand'           => __( 'Brand', '7listings' ),
							'company_product' => __( 'Products', '7listings' ),
							'company_service' => __( 'Services', '7listings' ),
							'location'        => __( 'Location', '7listings' )
						);
						foreach ( $types as $key => $type )
						{
							$selected = selected( $key, $instance['company_filter_by'], false );
							echo "<option value='$key' $selected>$type</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class="sl-sub-settings filter-parameters" data-name="<?php echo esc_attr( $this->get_field_name( 'company_filter_by' ) ); ?>" data-value="brand,company_product,company_service,location">
				<div class="sl-settings">
					<div class="sl-label">
						<label>
							<?php _e( 'Sorting', '7listings' ); ?>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting order', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
						</label>
					</div>
					<div class="sl-input">
						<select name="<?php echo esc_attr( $this->get_field_name( 'company_order_by' ) ); ?>">
							<?php
							$display_types = array(
								'name'  => __( 'Alphabetical', '7listings' ),
								'count' => __( 'Amount', '7listings' )
							);
							foreach ( $display_types as $key => $display_type )
							{
								$selected = selected( $key, $instance['company_order_by'], false );
								echo "<option value='$key' $selected>$display_type</option>";
							}
							?>
						</select>
					</div>
				</div>

				<?php
				$checkboxes = array(
					'company_dropdown'           => array(
						'title'   => __( 'Dropdown', '7listings' ),
						'tooltip' => __( 'Display taxonomies in a dropdown instead of a list', '7listings' ),
					),
					'company_count'              => array(
						'title'   => __( 'Count', '7listings' ),
						'tooltip' => __( 'Display amount of listings for taxonomy', '7listings' ),
					),
					'company_hierarchical'       => array(
						'title'   => __( 'Hierarchy', '7listings' ),
						'tooltip' => __( 'Show parent and child hierarchy', '7listings' ),
					),
					'company_show_children_only' => array(
						'title'   => __( 'Only show children', '7listings' ),
						'tooltip' => __( 'Only show child taxonomies of current selection', '7listings' ),
					)
				);

				foreach ( $checkboxes as $k => $v )
				{
					?>
					<div class="sl-settings">
						<div class="sl-label">
							<label>
								<?php echo $v['title']; ?>
								<?php echo do_shortcode( '[tooltip content="' . $v['tooltip'] . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
							</label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox_general( $this->get_field_name( $k ), $instance[$k] ); ?>
						</div>
					</div>
				<?php
				}
				?>
				<div class="sl-settings">
					<div class="sl-label">
						<label>
							<?php _e( 'Query type', '7listings' ); ?>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Select query type:<br><b>AND</b> – If a user selects two attributes, only products with match both attributes will be returned<br><br><b>OR</b> – If a user selects two attributes, products which match either attribute will be returned', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
						</label>
					</div>
					<div class="sl-input">
						<select name="<?php echo esc_attr( $this->get_field_name( 'company_query_type' ) ); ?>">
							<?php
							$query_types = array(
								'and' => __( 'AND', '7listings' ),
								'or'  => __( 'OR', '7listings' )
							);
							foreach ( $query_types as $key => $query_type )
							{
								$selected = selected( $key, $instance['company_query_type'], false );
								echo "<option value='$key' $selected>$query_type</option>";
							}
							?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="sl-sub-settings filter-parameters" data-name="<?php echo $field_post_type; ?>" data-value="accommodations,tours,rentals">
			<div class="sl-settings toggle-choices">
				<div class="sl-label">
					<label>
						<?php _e( 'Filter', '7listings' ); ?>
						<?php echo do_shortcode( '[tooltip content="' . __( 'Select filter criteria', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
					</label>
				</div>
				<div class="sl-input">
					<select name="<?php echo $this->get_field_name( 'filter_by' ); ?>">
						<?php
						$types = array(
							'alphabet' => __( 'Alphabet', '7listings' ),
							'price'    => __( 'Price', '7listings' ),
							'category' => __( 'Category', '7listings' ),
							'feature'  => __( 'Feature', '7listings' ),
							'location' => __( 'Location', '7listings' ),
						);
						foreach ( $types as $key => $type )
						{
							$selected = selected( $key, $instance['filter_by'], false );
							echo "<option value='$key' $selected>$type</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class="sl-sub-settings filter-parameters" data-name="<?php echo esc_attr( $this->get_field_name( 'filter_by' ) ); ?>" data-value="category,feature,location">
				<div class="sl-settings">
					<div class="sl-label">
						<label>
							<?php _e( 'Sorting', '7listings' ); ?>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting order', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
						</label>
					</div>
					<div class="sl-input">
						<select name="<?php echo esc_attr( $this->get_field_name( 'order_by' ) ); ?>">
							<?php
							$display_types = array(
								'name'  => __( 'Alphabetical', '7listings' ),
								'count' => __( 'Amount', '7listings' )
							);
							foreach ( $display_types as $key => $display_type )
							{
								$selected = selected( $key, $instance['order_by'], false );
								echo "<option value='$key' $selected>$display_type</option>";
							}
							?>
						</select>
					</div>
				</div>

				<?php
				$checkboxes = array(
					'dropdown'           => array(
						'title'   => __( 'Dropdown', '7listings' ),
						'tooltip' => __( 'Display taxonomies in a dropdown instead of a list', '7listings' ),
					),
					'count'              => array(
						'title'   => __( 'Count', '7listings' ),
						'tooltip' => __( 'Display amount of listings for taxonomy', '7listings' ),
					),
					'hierarchical'       => array(
						'title'   => __( 'Hierarchy', '7listings' ),
						'tooltip' => __( 'Show parent and child hierarchy', '7listings' ),
					),
					'show_children_only' => array(
						'title'   => __( 'Only show children', '7listings' ),
						'tooltip' => __( 'Only show child taxonomies of current selection', '7listings' ),
					)
				);

				foreach ( $checkboxes as $k => $v )
				{
					?>
					<div class="sl-settings">
						<div class="sl-label">
							<label>
								<?php echo $v['title']; ?>
								<?php echo do_shortcode( '[tooltip content="' . $v['tooltip'] . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
							</label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox_general( $this->get_field_name( $k ), $instance[$k] ); ?>
						</div>
					</div>
				<?php
				}
				?>
				<div class="sl-settings">
					<div class="sl-label">
						<label>
							<?php _e( 'Query type', '7listings' ); ?>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Select query type:<br><b>AND</b> – If a user selects two attributes, only products with match both attributes will be returned<br><br><b>OR</b> – If a user selects two attributes, products which match either attribute will be returned', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
						</label>
					</div>
					<div class="sl-input">
						<select name="<?php echo esc_attr( $this->get_field_name( 'query_type' ) ); ?>">
							<?php
							$query_types = array(
								'and' => __( 'AND', '7listings' ),
								'or'  => __( 'OR', '7listings' )
							);
							foreach ( $query_types as $key => $query_type )
							{
								$selected = selected( $key, $instance['query_type'], false );
								echo "<option value='$key' $selected>$query_type</option>";
							}
							?>
						</select>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Display widget in the frontend
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
		global $wpdb, $sl_archive_posts, $sl_archive_query;

		$instance  = array_merge( $this->default, $instance );
		$post_type = $instance['post_type'];

		$displayed_posts = ! empty( $sl_archive_posts ) ? $sl_archive_posts : ( ! empty( $sl_archive_query->posts ) ? $sl_archive_query->posts : array() );

		// Convert widget parameters for company to the same parameters as other custom post types

		if ( 'companie' == $post_type )
		{
			$post_type = 'company';
			$new_instance = array();
			foreach ( $instance as $k => $v )
			{
				if ( 0 === strpos( $k, 'company_' ) )
				{
					$k                = str_replace( 'company_', '', $k );
					$new_instance[$k] = $v;
				}
			}
			$instance = array_merge( $instance, $new_instance );
		}

		$filter_by = $instance['filter_by'];

		// Filter with alphabet
		if ( 'alphabet' == $filter_by )
		{
			extract( $args, EXTR_SKIP );

			$instance = array_merge( $this->default, $instance );

			echo str_replace( 'widget_filter', 'widget_filter widget_alphabet_filter', $args['before_widget'] );

			if ( ! empty( $instance['title'] ) )
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];

			$base_url = is_singular( $post_type ) ? get_post_type_archive_link( $post_type ) : remove_query_arg( 'start' );
			if ( isset( $_GET['start'] ) && preg_match( '#^[a-z]$#', $_GET['start'] ) )
				$start = $_GET['start'];

			echo '<ul>';

			// All
			$class = empty( $start ) ? ' class="selected"' : '';
			echo "<li$class><a href='$base_url'>" . __( 'All', '7listings' ) . '</a></li>';

			$chars = array();

			if ( ! is_singular( $post_type ) )
			{
				// Get first letters of all queried posts
				foreach ( (array) $displayed_posts as $p )
				{
					$chars[] = strtolower( substr( trim( $p->post_title ), 0, 1 ) );
				}
			}
			else
			{
				$states    = wp_get_post_terms( get_the_ID(), 'location' );
				$states    = wp_list_pluck( $states, 'term_id' );
				$companies = get_posts( array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'location',
							'field'    => 'id',
							'terms'    => $states,
						),
					),
				) );
				$titles    = wp_list_pluck( $companies, 'post_title' );
				foreach ( $titles as $title )
				{
					$chars[] = strtolower( substr( trim( $title ), 0, 1 ) );
				}
			}
			$chars = array_unique( $chars );
			sort( $chars );

			foreach ( $chars as $char )
			{
				$url   = add_query_arg( 'start', $char, $base_url );
				$class = ! empty( $start ) && $char == $start ? ' class="selected"' : '';
				$label = strtoupper( $char );
				echo "<li$class><a href='$url'>$label</a></li>";
			}

			echo '</ul>';

			echo $args['after_widget'];

			wp_reset_postdata();
		}
		if ( ! is_archive() )
		{
			return;
		}

		$arr_filters = array( 'category', 'feature', 'location', 'brand', 'company_product', 'company_service' );

		// Filter with taxonomy
		if ( in_array( $filter_by, $arr_filters ) )
		{
			$dropdown   = $instance['dropdown'];
			$count      = $instance['count'];
			$query_type = $instance['query_type'];

			// Get taxonomy to filter by
			$taxonomy = $filter_by;
			//var_dump($filter_by);
			if ( 'category' == $filter_by )
			{
				$taxonomy = sl_meta_key( 'tax_type', $post_type );
			}
			elseif ( 'feature' == $filter_by )
			{
				$taxonomy = sl_meta_key( 'tax_feature', $post_type );
			}

			$term_args = array(
				'title_li'     => '',
				'echo'         => 0,
				'orderby'      => $instance['order_by'],
				'order'        => 'count' == $instance['order_by'] ? 'DESC' : 'ASC',
				'post_type'    => $post_type,
				'hierarchical' => $instance['hierarchical'],
				'show_count'   => $count,
				'taxonomy'     => $taxonomy,
			);

			if ( $instance['show_children_only'] && isset( $_GET["filter_{$taxonomy}"] ) )
			{
				$term_args['child_of'] = $_GET["filter_{$taxonomy}"];
			}

			// Show list  with select box
			if ( $dropdown )
			{
				$selected = '';
				if ( isset( $_GET["filter_{$taxonomy}"] ) )
					$selected = $_GET["filter_{$taxonomy}"];


				$term_args = array_merge( $term_args, array(
					'value'        => 'brand' != $filter_by ? 'id' : 'slug',
					'selected'     => $selected,
					'walker'       => new Sl_Walker_Taxonomy_Dropdown
				) );

				$cats = wp_list_categories( $term_args );;

				if ( ! strpos( $cats, 'No categories' ) )
				{
					self::before_widget( $args, $instance, 'widget_layered_nav' );

					echo '<select class="dropdown_layered_nav_' . $taxonomy . '">';

					$option_first = '';

					// Get name option empty with taxonomy filter
					if ( isset( $_GET["filter_{$taxonomy}"] ) )
					{
						$id           = str_replace( 'filter_', '', $_GET["filter_{$taxonomy}"] );
						$term         = get_term( $id, $taxonomy );
						$option_first = $term->name;
					}
					printf( '<option value="">%s</option>',
						$option_first ? __( "Taxonomy's children ", '7listings' ) . ucfirst( $option_first ) : __( 'Any ', '7listings' ) . ucfirst( str_replace( '_', ' ', $filter_by ) )
					);
					echo $cats;
					echo '</select>';

					echo $args['after_widget'];
				}


				$link = get_post_type_archive_link( $instance['post_type'] );

				if ( 'or' == $query_type )
					$link = add_query_arg( 'query_type_' . $taxonomy, 'or', $link );

				foreach ( $_GET as $key => $value )
				{
					if ( 'filter_' . $taxonomy == $key )
						continue;

					$link = add_query_arg( $key, $value, $link );
				}

				$link = add_query_arg( 'filter_' . $taxonomy . '=', '', $link );

				?>
				<script>
					jQuery( function ( $ )
					{
						$( '.dropdown_layered_nav_<?php echo $taxonomy; ?>' ).change( function ()
						{
							location.href = '<?php echo $link; ?>' + $( this ).val();
						} );
					} );
				</script>
			<?php
			}
			else
			{
				$query_type = 'or' == $query_type ? '&query_type_' . $taxonomy . '=or' : '';

				$term_args = array_merge( $term_args, array(
					'query_type' => $query_type,
					'walker'     => new Sl_Walker_Taxonomy_List
				) );
				$cats      = wp_list_categories( $term_args );

				if ( ! strpos( $cats, 'No categories' ) )
				{
					self::before_widget( $args, $instance, 'widget_layered_nav' );
					echo '<ul>';
					echo $cats;
					echo '</ul>';
					echo $args['after_widget'];
				}
			}

		}

		/*
		 * Filter with price
		 *
		 * If page is category then it is not working
		 */
		if ( 'price' == $filter_by && ! is_category() )
		{

			$post_type = $instance['post_type'];

			// Get list of post with  this post type
			$list_post = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND post_status = 'publish' " );
			$prices    = array();

			foreach ( $list_post as $id )
			{
				$resources = get_post_meta( $id, sl_meta_key( 'booking', $post_type ), true );

				// Don't show price if there's no booking resource
				if ( empty( $resources ) )
					continue;

				$price = 0;
				$class = 'Sl_' . ucfirst( $instance['post_type'] ) . '_Helper';
				foreach ( $resources as $resource )
				{
					$resource_price = call_user_func( array( $class, 'get_resource_price' ), $resource );
					if ( false !== $resource_price )
					{
						$price = $resource_price;
					}
				}
				$prices[] = $price;
			}

			if ( count( $prices ) <= 1 )
				return;

			$min = min( $prices );
			$max = max( $prices );

			$min_price = isset( $_GET['min_price'] ) ? $_GET['min_price'] : intval( $min );
			$max_price = isset( $_GET['max_price'] ) ? $_GET['max_price'] : intval( $max );

			$new_filter = '';

			foreach ( $_GET as $key => $value )
			{
				if ( 'min_price' == $key || 'max_price' == $key || ( 'post_type' == $key && 'tour' == $value ) )
					continue;

				$new_filter .= '<input type="hidden"  name="' . $key . '" value="' . $value . '">';
			}

			self::before_widget( $args, $instance, 'widget_price_filter' );

			echo '<form method="get" action="">
			<div class="price_slider_wrapper">
				<div class="price_slider" style="display:none;"></div>
				<div class="price_slider_amount">
					<input type="text" id="min_price" name="min_price" value="' . $min_price . '" data-min="' . intval( $min ) . '" placeholder="Min price" />
					<input type="text" id="max_price" name="max_price" value="' . $max_price . '" data-max="' . intval( $max ) . '" placeholder="Max price" />
					' . $new_filter . '
					<button type="submit" class="button">' . __( 'Filter', '7listings' ) . '</button>
					<div class="price_label" style="display:none;">
						' . __( 'Price:', '7listings' ) . ' <span class="from"></span> &mdash; <span class="to"></span>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			</form>';

			echo $args['after_widget'];
		}
	}

	/**
	 * Show markup and title widget
	 *
	 * @param $args
	 * @param $instance
	 * @param $class
	 */
	public static function before_widget( $args, $instance, $class )
	{
		echo str_replace( 'widget_filter', "widget_filter {$class}", $args['before_widget'] );
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts()
	{
		wp_enqueue_script( 'sl-price-slider', THEME_JS . 'price-slider.js', array( 'sl', 'jquery-ui-slider' ), '', true );
		wp_localize_script( 'sl-price-slider', 'SlPriceSlide', array(
			'symbol'    => Sl_Currency::symbol(),
			'position'  => sl_setting( 'currency_position' ),
			'min_price' => isset( $_REQUEST['min_price'] ) ? $_REQUEST['min_price'] : '',
			'max_price' => isset( $_REQUEST['max_price'] ) ? $_REQUEST['max_price'] : '',
		) );
	}

}