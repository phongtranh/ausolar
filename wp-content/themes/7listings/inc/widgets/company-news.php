<?php

class Sl_Widget_Company_News extends WP_Widget
{
	/**
	 * @var array Default settings for widget
	 */
	public $default = array(
		'title'          => '',
		'number'         => 5,
		'orderby'        => 'recent',
		'thumbnail'      => 1,
		'post_title'     => 1,
		'title_length'   => 0,
		'date'           => 1,
		'excerpt'        => 1,
		'excerpt_length' => 25,
		'display'        => 'list',
		'columns'        => 2,
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Company_News
	 */
	function __construct()
	{
		parent::__construct(
			'sl-company-news',
			__( '7 - Company News', '7listings' ),
			array(
				'description' => __( 'Add posts for current company, when viewing a single company.', '7listings' ),
			)
		);
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance )
	{
		extract( $args, EXTR_SKIP );

		$instance = array_merge( $this->default, $instance );

		$query_args = array(
			'posts_per_page'      => $instance['number'],
			'order'               => 'DESC',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'meta_query'          => array(
				array(
					'key'   => 'company',
					'value' => get_the_ID(),
				),
			),
		);

		switch ( $instance['orderby'] )
		{
			case 'views':
				$query_args['orderby']  = 'meta_value_num';
				$query_args['meta_key'] = 'views';
				break;
			case 'alphabetically':
				$query_args['orderby'] = 'title';
				$query_args['order']   = 'ASC';
				break;
			case 'recent':
			default:
		}
		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() )
			return;

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$title = str_replace( '[company]', get_the_title(), $title );
		if ( $title )
			echo $before_title . $title . $after_title;

		$html = '';

		$class = 'posts';
		if ( $instance['display'] == 'grid' )
		{
			$class .= ' grid';
			$class .= ' cols-' . $instance['columns'];
		}
		$html .= '<ul class="' . $class . '">';
		while ( $query->have_posts() )
		{
			$query->the_post();
			$html .= '<li class="post">';

			$title_attr = the_title_attribute( 'echo=0' );
			$html .= '<a href="' . get_permalink() . '" rel="bookmark" title="' . $title_attr . '">';

			if ( $instance['thumbnail'] )
			{
				if ( has_post_thumbnail() )
				{
					$html .= get_the_post_thumbnail( null, 'sl_thumb_tiny', array(
						'title' => $title_attr,
						'alt'   => $title_attr,
					) );
				}
				else
				{
					$html .= '<div class="thumbnail"><img src="http://placehold.it/80x80&text=Thumbnail" class="photo" title="' . $title_attr . '" alt="' . $title_attr . '"></div>';
				}
			}
			$html .= '<span class="details">';
			if ( $instance['post_title'] )
			{
				$html .= '<span class="title">';

				$title = get_the_title();
				if ( $title )
				{
					if ( $instance['title_length'] && mb_strlen( $title ) > $instance['title_length'] )
						$html .= mb_substr( $title, 0, $instance['title_length'] ) . '...';
					else
						$html .= $title;
				}
				else
				{
					the_ID();
				}

				$html .= '</span>';
			}
			if ( $instance['date'] )
				$html .= '<time class="date" datetime="' . get_the_time( 'Y-m-d' ) . '">' . get_the_date() . '</time>';

			if ( $instance['excerpt'] )
				$html .= '<span class="entry-summary excerpt">' . sl_excerpt( $instance['excerpt_length'] ) . '</span>';

			$html .= '</span>';

			$html .= '</a>';

			$html .= '</li>';
		}

		$html .= '</ul>';

		wp_reset_postdata();

		echo '<div class="widget_listings_list posts">' . $html . '</div>';

		echo $after_widget;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		$instance                   = $old_instance;
		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['number']         = absint( $new_instance['number'] );
		$instance['orderby']        = strip_tags( $new_instance['orderby'] );
		$instance['thumbnail']      = ! empty( $new_instance['thumbnail'] ) ? 1 : 0;
		$instance['post_title']     = ! empty( $new_instance['post_title'] ) ? 1 : 0;
		$instance['title_length']   = absint( $new_instance['title_length'] );
		$instance['date']           = ! empty( $new_instance['date'] ) ? 1 : 0;
		$instance['excerpt']        = ! empty( $new_instance['excerpt'] ) ? 1 : 0;
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );
		$instance['display']        = strip_tags( $new_instance['display'] );
		$instance['columns']        = absint( $new_instance['columns'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @internal param array $old_instance
	 *
	 * @return array
	 */
	function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );

		extract( $instance );
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input class="widefat widget-title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>">
			</div>
			<small><?php _e( 'Use <code>[company]</code> to display current company title', '7listings' ); ?></small>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label class="input-label"><?php _e( 'Sort By', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
					<option value="recent"<?php selected( 'recent', $orderby ); ?>><?php _e( 'Recent', '7listings' ); ?></option>
					<option value="popular"<?php selected( 'popular', $orderby ); ?>><?php _e( 'Popular', '7listings' ); ?></option>
				</select>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label class="input-label"><?php _e( 'Amount', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input class="amount" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>">
					<span class="add-on"><?php _e( 'Posts', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox_general( $this->get_field_name( 'post_title' ), $post_title ); ?>
				</span>
				<span class="input-append supplementary-input">
					<input type="number" class="amount" name="<?php echo $this->get_field_name( 'title_length' ); ?>" value="<?php echo $title_length; ?>">
					<span class="add-on"><?php _e( 'chars', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<?php
		$checkboxes = array(
			'thumbnail' => __( 'Thumbnail', '7listings' ),
			'date'      => __( 'Date', '7listings' ),
		);
		include THEME_INC . 'widgets/tpl/checkboxes.php';
		?>
		<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
	<?php
	}
}
