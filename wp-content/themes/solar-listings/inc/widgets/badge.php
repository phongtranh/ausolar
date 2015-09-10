<?php
namespace ASQ\Widget;

class Badge extends \WP_Widget
{
	public $defaults = array();

	public function __construct()
	{
		$widget_options = array(
			'classname'   => 'solar-company-badge',
			'description' => esc_html__( 'Give you option to control the output of company badge', '7listings' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 300,
			'height' => 450
		);


		/* Set up the defaults. */
		$this->defaults = array(
			'title'             => esc_attr__( 'Company Badge', '7listings' ),
			'description'       => '',
			'large_image_url'   => 'https://www.australiansolarquotes.com.au/wp-content/uploads/banners/ASQ-Banner-LG.gif',
			'medium_image_url'  => 'https://www.australiansolarquotes.com.au/wp-content/uploads/banners/ASQ-Banner-MD.gif',
			'allow_decide_size' => true,
			'utm_string'        => ''
		);

		/* Create the widget. */
		parent::__construct(
			'solar-company-badge',
			__( 'Company Badge', '7listings' ),
			$widget_options,
			$control_options
		);
	}

	public function widget( $sidebar, $instance )
	{
		$args = wp_parse_args( $instance, $this->defaults );

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if ( !empty( $args['title'] ) )
			echo $sidebar['before_title'] . apply_filters( 'widget_title', $args['title'], $instance, $this->id_base ) . $sidebar['after_title'];

		if ( !empty( $args['description'] ) )
			echo "<p>{$args['description']}<p>";

		$company_name = '';

		if ( is_user_logged_in() && current_user_can( 'company_owner' ) )
		{
			$company = get_posts( array(
				'post_type'      => 'company',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'meta_key'       => 'user',
				'meta_value'     => get_current_user_id(),
			) );

			if ( !empty( $company ) )
				$company_name = $company[0]->post_name . '/';
		}

		$utm_string = !empty ( $args['utm_string'] ) ? '?' . $args['utm_string'] : '';

		$large_image_url    = $args['large_image_url'];
		$medium_image_url   = $args['medium_image_url'];
		?>

		<a class="solar-badge badge-medium hide" href="https://www.australiansolarquotes.com.au/solar-installers/<?php echo $company_name . $utm_string ?>" target="_blank">
			<img src="<?php echo $medium_image_url ?>" alt="Australian Solar Quotes Installer Reviews" border="0" />
		</a>

		<a class="solar-badge badge-large hide" href="https://www.australiansolarquotes.com.au/solar-installers/<?php echo $company_name . $utm_string ?>" target="_blank">
			<img src="<?php echo $large_image_url ?>" alt="Australian Solar Quotes Installer Reviews" border="0" />
		</a>

		<div class="clearfix clear" style="margin: 20px 0"></div>

		<select class="form-control select-badge hide">
			<option disabled selected>Badge Size</option>
			<option value="badge-medium">Medium</option>
			<option value="badge-large">Large</option>
		</select>

		<div class="textarea badge-large hide">
			<label>Copy this code</label>
			<textarea rows="4" style="width: 100%"><a class="solar-badge badge-large" href="https://www.australiansolarquotes.com.au/solar-installers/<?php echo $company_name . $utm_string ?>" target="_blank"><img src="<?php echo $large_image_url ?>" alt="Australian Solar Quotes Installer Reviews" border="0" /></a></textarea>
		</div>

		<div class="textarea badge-medium hide">
			<label>Copy this code</label>
			<textarea rows="4" style="width: 100%"><a class="solar-badge badge-medium" href="https://www.australiansolarquotes.com.au/solar-installers/<?php echo $company_name . $utm_string ?>" target="_blank"><img src="<?php echo $medium_image_url ?>" alt="Australian Solar Quotes Installer Reviews" border="0" /></a></textarea>
		</div>

		<div class="clear clearfix"></div>

		<button type="button" class="button get-badge">Get a badge</button>

		<script type="text/javascript">
			( function( $ )
			{
				jQuery( '.get-badge' ).click( function()
				{
					$('.solar-badge, .textarea ' ).hide();

					$( '.select-badge, .badge-medium' ).fadeIn(200);

					$( '.select-badge' ).change(function(){

						$('.solar-badge, .textarea ' ).hide();

						$('.' + $(this).val() ).fadeIn(200);
					} );
				} );
			} )(jQuery);
		</script>

		<?php
		/* Close the sidebar's widget wrapper. */
		echo $sidebar['after_widget'];
	}

	public function show()
	{
		?>


		<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = $new_instance;

		// Validation here
		$instance['allow_decide_size'] = isset( $new_instance['allow_decide_size'] ) ? 1 : 0;

		return $instance;
	}

	public function form( $instance )
	{
		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>

		<div class="form-control">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', '7listings' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" placeholder="<?php echo esc_attr( $this->defaults['title'] ); ?>" />
		</div>

		<div class="form-control">
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description:', '7listings' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" placeholder="<?php echo esc_attr( $this->defaults['description'] ); ?>"><?php echo esc_attr( $instance['description'] ); ?></textarea>
		</div>

		<div class="form-control">
			<label for="<?php echo $this->get_field_id( 'utm_string' ); ?>"><?php _e( 'UTM String:', '7listings' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'utm_string' ); ?>" name="<?php echo $this->get_field_name( 'utm_string' ); ?>" value="<?php echo esc_attr( $instance['utm_string'] ); ?>" />
		</div>

		<div class="clearfix"></div>

		<img src="<?php echo $instance['large_image_url']; ?>" />

		<div class="form-control">
			<label for="<?php echo $this->get_field_id( 'large_image_url' ); ?>"><?php _e( 'Large Image URL:', '7listings' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'large_image_url' ); ?>" name="<?php echo $this->get_field_name( 'large_image_url' ); ?>" value="<?php echo esc_attr( $instance['large_image_url'] ); ?>" />
		</div>

		<div class="clearfix"></div>

		<img src="<?php echo $instance['medium_image_url']; ?>" />

		<div class="form-control">
			<label for="<?php echo $this->get_field_id( 'medium_image_url' ); ?>"><?php _e( 'Medium Image URL:', '7listings' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'medium_image_url' ); ?>" name="<?php echo $this->get_field_name( 'medium_image_url' ); ?>" value="<?php echo esc_attr( $instance['medium_image_url'] ); ?>" />
		</div>

		<div class="clearfix"></div>

		<div class="form-control">
			<label for="<?php echo $this->get_field_id( 'allow_decide_size' ); ?>"><?php _e( 'Allow users decide their size:', '7listings' ); ?></label>
			<input type="checkbox" <?php checked( $instance['allow_decide_size'], true ); ?> id="<?php echo $this->get_field_id( 'allow_decide_size' ); ?>" name="<?php echo $this->get_field_name( 'allow_decide_size' ); ?>" />
		</div>
	<?php
	}
}