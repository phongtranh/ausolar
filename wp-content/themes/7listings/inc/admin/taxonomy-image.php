<?php

class Sl_Taxonomy_Image
{
	/**
	 * Taxonomies which have thumbnail image
	 *
	 * @var array
	 */
	public $tax_image = array( 'location', 'category', 'post_tag' );

	/**
	 * Taxonomies which have map icon
	 *
	 * @var array
	 */
	public $tax_icon = array();

	/**
	 * Default thumbnail
	 *
	 * @var string
	 */
	public $default = '';

	/**
	 * Constructor
	 *
	 * @return Sl_Taxonomy_Image
	 */
	function __construct()
	{
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Init
	 *
	 * @return Sl_Taxonomy_Image
	 */
	function init()
	{
		$this->default   = sl_locate_url( 'images/admin/placeholder.png' );
		$this->tax_image = apply_filters( 'sl_taxonomy_image_taxonomies', $this->tax_image );
		$this->tax_icon  = apply_filters( 'sl_taxonomy_icon_taxonomies', $this->tax_icon );

		// Show field
		add_action( 'load-edit-tags.php', array( $this, 'load' ) );

		// Save field
		add_action( 'created_term', array( $this, 'save' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save' ), 10, 3 );

		// Show in column
		foreach ( $this->tax_image as $taxonomy )
		{
			add_filter( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'add_image_column' ) );
			add_filter( 'manage_' . $taxonomy . '_custom_column', array( $this, 'show_image_column' ), 10, 3 );
		}
		foreach ( $this->tax_icon as $taxonomy )
		{
			// Check taxonomy
			if ( 'brand' != $taxonomy )
			{
				add_filter( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'add_icon_column' ) );
				add_filter( 'manage_' . $taxonomy . '_custom_column', array( $this, 'show_icon_column' ), 10, 3 );
			}
		}
	}

	/**
	 * Check if on correct taxonomy page and show field
	 *
	 * @return void
	 */
	function load()
	{
		$screen = get_current_screen();
		if ( ! in_array( $screen->taxonomy, $this->tax_image ) && ! in_array( $screen->taxonomy, $this->tax_icon ) )
			return;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		// Show field
		add_action( $screen->taxonomy . '_add_form_fields', array( $this, 'add_field' ) );
		add_action( $screen->taxonomy . '_edit_form_fields', array( $this, 'edit_field' ), 10, 2 );
	}

	/**
	 * Enqueue scripts for choosing image
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_media();
		wp_enqueue_script( 'sl-choose-image' );
	}

	/**
	 * Add form field for image upload
	 *
	 * @return void
	 */
	function add_field()
	{
		wp_nonce_field( 'sl-tax-image', 'sl_tax_image_nonce', false );

		$screen = get_current_screen();
		if ( in_array( $screen->taxonomy, $this->tax_image ) ) :
			?>
			<div class="form-field">
				<label><?php _e( 'Thumbnail', '7listings' ); ?></label>
				<div style="line-height:60px;">
					<img src="<?php echo $this->default; ?>" style="float:left;margin-right:10px;width:60px;height:60px">
					<input type="hidden" name="tax_thumbnail_id">
					<a href="#" class="choose-image button"><?php _e( 'Choose Image', '7listings' ); ?></a>
					<a href="#" class="remove-image button" style="display:none"><?php _e( 'Remove Image', '7listings' ); ?></a>
					<div class="clear"></div>
				</div>
			</div>
		<?php
		endif;

		if ( in_array( $screen->taxonomy, $this->tax_icon ) ) :
			?>
			<div class="form-field">
				<label><?php _e( 'Map Marker', '7listings' ); ?></label>
				<div style="line-height:60px;">
					<img src="<?php echo $this->default; ?>" style="float:left;margin-right:10px;width:60px;height:60px">
					<input type="hidden" name="tax_icon_id">
					<a href="#" class="choose-image button"><?php _e( 'Choose Image', '7listings' ); ?></a>
					<a href="#" class="remove-image button" style="display:none"><?php _e( 'Remove Image', '7listings' ); ?></a>
					<div class="clear"></div>
				</div>
			</div>
		<?php
		endif;
	}

	/**
	 * Show form field for image upload
	 *
	 * @param object $term
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	function edit_field( $term, $taxonomy )
	{
		wp_nonce_field( 'sl-tax-image', 'sl_tax_image_nonce', false );

		if ( in_array( $taxonomy, $this->tax_image ) ) :
			$image_id = sl_get_term_meta( $term->term_id, 'thumbnail_id' );
			if ( $image_id )
				$image = wp_get_attachment_url( $image_id );
			else
				$image = $this->default;
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php _e( 'Thumbnail', '7listings' ); ?></label></th>
				<td>
					<div style="line-height:60px;">
						<img src="<?php echo $image; ?>" style="float:left;margin-right:10px;width:60px;height:60px">
						<input type="hidden" name="tax_thumbnail_id" value="<?php echo $image_id; ?>">
						<a href="#" class="choose-image button"><?php _e( 'Choose Image', '7listings' ); ?></a>
						<a href="#" class="remove-image button"<?php if ( ! $image_id )
							echo ' style="display:none"'; ?>><?php _e( 'Remove Image', '7listings' ); ?></a>
						<div class="clear"></div>
					</div>
				</td>
			</tr>
		<?php
		endif;

		if ( in_array( $taxonomy, $this->tax_icon ) ) :
			$image_id = sl_get_term_meta( $term->term_id, 'icon_id' );
			if ( $image_id )
				$image = wp_get_attachment_url( $image_id );
			else
				$image = $this->default;
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php _e( 'Map Marker', '7listings' ); ?></label></th>
				<td>
					<div style="line-height:60px;">
						<img src="<?php echo $image; ?>" style="float:left;margin-right:10px;width:60px;height:60px">
						<input type="hidden" name="tax_icon_id" value="<?php echo $image_id; ?>">
						<a href="#" class="choose-image button"><?php _e( 'Choose Image', '7listings' ); ?></a>
						<a href="#" class="remove-image button"<?php if ( ! $image_id )
							echo ' style="display:none"'; ?>><?php _e( 'Remove Image', '7listings' ); ?></a>
						<div class="clear"></div>
					</div>
				</td>
			</tr>
		<?php
		endif;
	}

	/**
	 * Save term thumbnail
	 *
	 * @param int    $term_id
	 * @param int    $tt_id
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	function save( $term_id, $tt_id, $taxonomy )
	{
		if ( empty( $_POST['sl_tax_image_nonce'] ) || ! wp_verify_nonce( $_POST['sl_tax_image_nonce'], 'sl-tax-image' ) )
			return;
		if ( ! in_array( $taxonomy, $this->tax_image ) && ! in_array( $taxonomy, $this->tax_icon ) )
			return;

		if ( isset( $_POST['tax_thumbnail_id'] ) )
			sl_update_term_meta( $term_id, 'thumbnail_id', $_POST['tax_thumbnail_id'] );
		else
			sl_delete_term_meta( $term_id, 'thumbnail_id' );

		if ( isset( $_POST['tax_icon_id'] ) )
			sl_update_term_meta( $term_id, 'icon_id', $_POST['tax_icon_id'] );
		else
			sl_delete_term_meta( $term_id, 'icon_id' );
	}

	/**
	 * Add thumbnail column for taxonomy
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	function add_image_column( $columns )
	{
		$columns['thumbnail'] = __( 'Thumbnail', '7listings' );

		return $columns;
	}

	/**
	 * Show thumbnail column for taxonomy
	 *
	 * @param $value
	 * @param $column_name
	 * @param $term_id
	 *
	 * @return array
	 */
	function show_image_column( $value, $column_name, $term_id )
	{
		if ( 'thumbnail' !== $column_name )
			return $value;

		$image = $this->default;
		if ( $image_id = sl_get_term_meta( $term_id, 'thumbnail_id' ) )
		{
			list( $image ) = wp_get_attachment_image_src( $image_id, 'sl_thumb_tiny' );
		}

		return "<img src='$image' width='60' height='60'>";
	}

	/**
	 * Add map icon column for taxonomy
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	function add_icon_column( $columns )
	{
		$columns['icon'] = __( 'Map Marker', '7listings' );

		return $columns;
	}

	/**
	 * Show map icon column for taxonomy
	 *
	 * @param $value
	 * @param $column_name
	 * @param $term_id
	 *
	 * @return array
	 */
	function show_icon_column( $value, $column_name, $term_id )
	{
		if ( 'icon' !== $column_name )
			return $value;

		$image_id = sl_get_term_meta( $term_id, 'icon_id' );
		$image    = $image_id ? wp_get_attachment_url( $image_id ) : $this->default;

		return "<img src='$image' width='60' height='60'>";
	}
}

new Sl_Taxonomy_Image;
