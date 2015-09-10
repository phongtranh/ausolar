<?php

class SH_User
{
	/**
	 * Run when the class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		$actions = array(
			'edit_profile_before' => 0,
		);
		foreach ( $actions as $action => $num )
		{
			if ( $num )
				add_action( "company_$action", array( __CLASS__, $action ), 10, $num );
			else
				add_action( "company_$action", array( __CLASS__, $action ) );
		}
	}

	/**
	 * Log edit user
	 *
	 * @return void
	 */
	public static function edit_profile_before()
	{
		$user = get_userdata( get_current_user_id() );
		$fields = apply_filters( 'sch_user_fields', array(
			'user_email'   => __( 'Email', 'sch' ),
			'first_name'   => __( 'First name', 'sch' ),
			'last_name'    => __( 'Last name', 'sch' ),
			'nickname'     => __( 'Nickname', 'sch' ),
			'display_name' => __( 'Display name', 'sch' ),
			'user_url'     => __( 'Website', 'sch' ),
			'description'  => __( 'Biography' ),
			'facebook'     => __( 'Facebook', 'sch' ),
			'twitter'      => __( 'Twitter', 'sch' ),
			'googleplus'   => __( 'Google Plus', 'sch' ),
			'pinterest'    => __( 'Pinterest', 'sch' ),
			'linkedin'     => __( 'Linkedin', 'sch' ),
			'instagram'    => __( 'Instagram', 'sch' ),
			'rss'          => __( 'RSS', 'sch' ),
		) );

		$description = array();
		$tpl = '<span class="label">%s:</span> <span class="detail">%s</span>';
		foreach ( $fields as $k => $v )
		{
			$new = isset( $_POST[$k] ) ? $_POST[$k] : '';
			if ( $user->$k !== $_POST[$k] )
				$description[] = sprintf( $tpl, $v, $new ? $new : __( 'None', 'sch' ) );
		}

		// Password cannot get to compare, just log
		if ( isset( $_POST['user_pass'] ) )
			$description[] = sprintf( $tpl, __( 'Password', 'sch' ), __( 'Changed', 'sch' ) );

		if ( empty( $description ) )
			return;

		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user->ID,
		) );

		if ( empty( $company ) )
		{
			$company = '';
		}
		else
		{
			$company = current( $company );
			$company = $company->ID;
		}
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'User', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => implode( '<br>', $description ),
			'object'      => $company,
			'user'        => $user->ID,
		) );
	}
}

SH_User::load();