<?php

class SH_Company
{
	/**
	 * Description for log taxonomies
	 *
	 * @var array
	 */
	public static $log_taxonomies = array();

	/**
	 * Run when the class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		$actions = array(
			'save_post_before' => 1,
			'signup'           => 3,
		);
		foreach ( $actions as $action => $num )
		{
			add_action( "company_$action", array( __CLASS__, $action ), 10, $num );
		}

		// Log taxonomies edit
		add_action( 'wp_insert_post_data', array( __CLASS__, 'log_taxonomies' ), 10, 2 );
	}

	/**
	 * Log edit company
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public static function save_post_before( $post_id )
	{
		$post = get_post( $post_id );

		// Fields to log
		$post_fields = apply_filters( 'sch_company_post_fields', array(
			'post_title'   => __( 'Company name', 'sch' ),
			'post_content' => __( 'Company description', 'sch' ),
		) );
		$meta = apply_filters( 'sch_company_meta', array(
			'range'                   => __( 'Range', 'sch' ),
			'address'                 => __( 'Address', 'sch' ),
			'address2'                => __( 'Address 2', 'sch' ),
			'city'                    => __( 'City', 'sch' ),
			'state'                   => __( 'State', 'sch' ),
			'postcode'                => __( 'Postcode', 'sch' ),
			'country'                 => __( 'Country', 'sch' ),
			'website'                 => __( 'Website', 'sch' ),
			'email'                   => __( 'Email', 'sch' ),
			'phone'                   => __( 'Phone', 'sch' ),

			'business_hours_mon'      => __( 'Business Hours Monday', 'sch' ),
			'business_hours_mon_from' => __( 'Business Hours Monday From', 'sch' ),
			'business_hours_mon_to'   => __( 'Business Hours Monday To', 'sch' ),
			'business_hours_tue'      => __( 'Business Hours Tuesday', 'sch' ),
			'business_hours_tue_from' => __( 'Business Hours Tuesday From', 'sch' ),
			'business_hours_tue_to'   => __( 'Business Hours Tuesday To', 'sch' ),
			'business_hours_wed'      => __( 'Business Hours Wednesday', 'sch' ),
			'business_hours_wed_from' => __( 'Business Hours Wednesday From', 'sch' ),
			'business_hours_wed_to'   => __( 'Business Hours Wednesday To', 'sch' ),
			'business_hours_thu'      => __( 'Business Hours Thursday', 'sch' ),
			'business_hours_thu_from' => __( 'Business Hours Thursday From', 'sch' ),
			'business_hours_thu_to'   => __( 'Business Hours Thursday To', 'sch' ),
			'business_hours_fri'      => __( 'Business Hours Friday', 'sch' ),
			'business_hours_fri_from' => __( 'Business Hours Friday From', 'sch' ),
			'business_hours_fri_to'   => __( 'Business Hours Friday To', 'sch' ),
			'business_hours_sat'      => __( 'Business Hours Saturday', 'sch' ),
			'business_hours_sat_from' => __( 'Business Hours Saturday From', 'sch' ),
			'business_hours_sat_to'   => __( 'Business Hours Saturday To', 'sch' ),
			'business_hours_sun'      => __( 'Business Hours Sunday', 'sch' ),
			'business_hours_sun_from' => __( 'Business Hours Sunday From', 'sch' ),
			'business_hours_sun_to'   => __( 'Business Hours Sunday To', 'sch' ),

			'facebook'                => __( 'Facebook', 'sch' ),
			'twitter'                 => __( 'Twitter', 'sch' ),
			'googleplus'              => __( 'Google Plus', 'sch' ),
			'pinterest'               => __( 'Pinterest', 'sch' ),
			'linkedin'                => __( 'Linkedin', 'sch' ),
			'instagram'               => __( 'Instagram', 'sch' ),
			'rss'                     => __( 'RSS', 'sch' ),
		) );
		$uploads = apply_filters( 'sch_company_upload', array(
			'company_logo' => __( 'Logo', 'sch' ),
		) );

		// Check all fields to log
		$description = array();
		$tpl = '<span class="label">%s:</span> <span class="detail">%s</span>';
		foreach ( $post_fields as $k => $v )
		{
			$new = isset( $_POST[$k] ) ? $_POST[$k] : '';
			if ( $new != $post->$k )
				$description[] = sprintf( $tpl, $v, $new ? $new : __( 'None', 'sch' ) );
		}
		foreach ( $meta as $k => $v )
		{
			$new = isset( $_POST[$k] ) ? $_POST[$k] : '';
			$prev = get_post_meta( $post_id, $k, true );
			$value = $new;
			if ( !$value )
				$value = __( 'None', 'sch' );
			elseif ( 1 == $value )
				$value = __( 'Yes', 'sch' );
			if ( $prev !== $new )
				$description[] = sprintf( $tpl, $v, $value );
		}
		foreach ( $uploads as $k => $v )
		{
			if ( !empty( $_FILES[$k] ) && !empty( $_FILES[$k]['name'] ) )
				$description[] = "<span class='label'>$v</span>";
		}

		// Log edit taxonomies
		if ( !empty( self::$log_taxonomies ) )
			$description = array_merge( $description, self::$log_taxonomies );
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Company', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => implode( '<br>', $description ),
			'object'      => $post_id,
			'user'        => get_post_meta( $post_id, 'user', true ),
		) );
	}

	/**
	 * Log taxonomies edit when edit company
	 *
	 * @param array $data
	 * @param array $postarr
	 *
	 * @return array
	 */
	public static function log_taxonomies( $data, $postarr )
	{
		if ( !isset( $postarr['ID'] ) )
			return $data;
		$taxonomies = apply_filters( 'sch_company_taxonomies', array(
			'products' => array(
				'label'    => __( 'Products', 'sch' ),
				'taxonomy' => 'company_product',
			),
			'services' => array(
				'label'    => __( 'Services', 'sch' ),
				'taxonomy' => 'company_service',
			),
			'brands'   => array(
				'label'    => __( 'Brands', 'sch' ),
				'taxonomy' => 'brand',
			),
		) );
		$tpl = '<span class="label">%s:</span> <span class="detail">%s</span>';
		foreach ( $taxonomies as $k => $v )
		{
			$new = isset( $_POST[$k] ) ? $_POST[$k] : array();
			$all = get_terms( $v['taxonomy'], 'hide_empty=0' );

			$old = wp_get_post_terms( $postarr['ID'], $v['taxonomy'] );
			$old = wp_list_pluck( $old, 'term_id' );
			if ( !count( array_diff( $old, $new ) ) && !count( array_diff( $new, $old ) ) )
				continue;
			$new_names = array();
			foreach ( $all as $term )
			{
				if ( !in_array( $term->term_id, $new ) )
					continue;
				$new_names[] = $term->name;
			}
			if ( !empty( $new_names ) )
				self::$log_taxonomies[] = sprintf( $tpl, $v['label'], implode( ', ', $new_names ) );
			else
				self::$log_taxonomies[] = sprintf( $tpl, $v['label'], __( 'None', 'sch' ) );
		}
		return $data;
	}

	/**
	 * Log company signup
	 *
	 * @param object $user_data
	 * @param int    $post_id
	 * @param array  $data
	 */
	public static function signup( $user_data, $post_id, $data )
	{
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Company', 'sch' ),
			'action'      => __( 'Signup', 'sch' ),
			'description' => $data['post_title'],
			'object'      => $post_id,
			'user'        => $user_data->ID,
		) );
	}
}

SH_Company::load();