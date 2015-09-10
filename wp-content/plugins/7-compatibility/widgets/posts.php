<?php

class Sl_Widget_Posts extends Sl_Widget_Compatibility_List
{
	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Posts
	 */
	function __construct()
	{
		$this->post_type               = 'post';
		$this->checkboxes              = array(
			'date' => __( 'Date', '7listings' ),
		);
		$this->default['cat']          = array();
		$this->default['post_title']   = 1;
		$this->default['title_length'] = 15;
		$this->default['date']         = 1;

		$this->default['more_listings_text'] = __( 'See more posts', '7listings' );
		self::remove_atts( $this->default, array( 'type', 'location', 'hierarchy', 'display_order', 'rating', 'price', 'booking' ) );
		parent::__construct(
			'sl-posts',
			__( '7 - Posts/News', '7listings' ),
			array(
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
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
		$instance                 = parent::update( $new_instance, $old_instance );
		$instance['cat']          = $new_instance['cat'];
		$instance['date']         = empty( $new_instance['date'] ) ? 0 : 1;
		$instance['post_title']   = empty( $new_instance['date'] ) ? 0 : 1;
		$instance['title_length'] = absint( $new_instance['title_length'] );
		self::remove_atts( $instance, array( 'type', 'location', 'hierarchy', 'display_order', 'rating', 'price', 'booking' ) );

		return $instance;
	}

	/**
	 * Show admin form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	function form( $instance )
	{
		$checkboxes = $this->checkboxes;
		$instance   = array_merge( $this->default, $instance );
		?>
		<div class="sl-admin-widget">
			<p>
				<label><?php _e( 'Title', '7listings' ); ?></label>
				<input class="widefat widget-title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>">
			</p>
			<hr class="light">
			<p>
				<label><?php _e( 'Categories', '7listings' ); ?></label><br>
				<?php
				$cat        = is_array( $instance['cat'] ) ? $instance['cat'] : array();
				$categories = get_categories( 'orderby=name&hide_empty=0' );
				$html       = array();
				$html[]     = sprintf(
					'<label><input type="checkbox" name="%s[]" value="%s"%s> %s</label>',
					$this->get_field_name( 'cat' ),
					- 1,
					checked( in_array( - 1, $cat ), 1, false ),
					__( 'All', '7listings' )
				);

				foreach ( $categories as $category )
				{
					$html[] = sprintf(
						'<label><input type="checkbox" name="%s[]" value="%s"%s> %s</label>',
						$this->get_field_name( 'cat' ),
						$category->term_id,
						checked( in_array( $category->term_id, $cat ), 1, false ),
						$category->name
					);
				}
				echo implode( '<br>', $html );
				?>
			</p>
			<p>
				<label class="input-label"><?php _e( 'Sort By', '7listings' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
					<?php
					Sl_Form::options( $instance['orderby'], array(
						'date'  => __( 'Recent', '7listings' ),
						'views' => __( 'Popular', '7listings' ),
					) );
					?>
				</select>
			</p>
			<hr class="light">
			<p>
				<label class="input-label"><?php _e( 'Amount', '7listings' ); ?></label>
				<span class="input-append">
					<input class="amount" type="number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo absint( $instance['number'] ); ?>">
					<span class="add-on"><?php _e( 'listings', '7listings' ); ?></span>
				</span>
			</p>
			<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>
			<hr class="light">
			<?php include THEME_INC . 'widgets/tpl/thumbnail.php'; ?>
			<p>
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox_general( $this->get_field_name( 'post_title' ), $instance['post_title'] ); ?>
					<label><?php _e( 'Title', '7listings' ); ?></label>
				</span>

				<span class="input-append supplementary-input">
					<input type="number" class="amount" name="<?php echo $this->get_field_name( 'title_length' ); ?>" value="<?php echo $instance['title_length']; ?>">
					<span class="add-on"><?php _e( 'chars', '7listings' ); ?></span>
				</span>
			</p>
			<?php include THEME_INC . 'widgets/tpl/checkboxes.php'; ?>
			<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
			<p class="checkbox-toggle">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'more_listings' ), $instance['more_listings'] ); ?>
				<label><?php _e( 'See more posts', '7listings' ); ?></label>
			</p>
			<div>
				<p>
					<label><?php _e( 'Text', '7listings' ); ?></label><br>
					<input type="text" name="<?php echo $this->get_field_name( 'more_listings_text' ); ?>" value="<?php echo $instance['more_listings_text']; ?>">
				</p>
				<p>
					<label><?php _e( 'Style', '7listings' ); ?></label>
					<select class="input-small" name="<?php echo $this->get_field_name( 'more_listings_style' ); ?>">
						<option value="button"<?php selected( 'button', $instance['more_listings_style'] ); ?>><?php _e( 'Button', '7listings' ); ?></option>
						<option value="text"<?php selected( 'text', $instance['more_listings_style'] ); ?>><?php _e( 'Text', '7listings' ); ?></option>
					</select>
				</p>
			</div>
		</div>
	<?php
	}
}
