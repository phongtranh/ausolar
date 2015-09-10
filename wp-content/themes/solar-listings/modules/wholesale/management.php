<?php
/**
 * This class will hold all things for wholesale management page
 */
class Sl_Wholesale_Management extends Sl_Core_Management
{
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
            'cb'       => '<input type="checkbox">',
            'image'    => __( 'Image', '7listings' ),
            'title'    => __( 'Name', '7listings' ),
            'code'     => __( 'Code', '7listings' ),
            'lead_value' => __( 'Lead Value', '7listings' ),
            'state'    => __( 'State', '7listings' ),
            'city'     => __( 'City', '7listings' ),
            'users'    => __( 'Users', '7listings' ),
            'date'     => __( 'Date', '7listings' ),
        );

        if ( class_exists( 'ThreeWP_Broadcast' ) )
            $columns['broadcasted'] = __( 'Shared', '7listings' );

        return $columns;
    }

    /**
     * Make columns sortable
     *
     * @param array $columns
     *
     * @return array
     */
    function sortable_columns( $columns )
    {
        $columns = array_merge( $columns, array(
            'state'    => 'state',
            'city'     => 'city',
            'featured' => 'featured',
        ) );
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
            case 'state':
                echo get_post_meta( $post_id, 'state', true );
                break;
            case 'city':
                echo get_post_meta( $post_id, 'city', true );
                break;
            case 'code':
                echo get_post_meta( $post_id, 'wholesale_code', true );
                break;

            case 'lead_value':
                echo get_post_meta( $post_id, 'wholesale_lead_value', true );
                break;
            case 'users':
                $user_id = get_post_meta( $post_id, 'user', true );
                if ( !$user_id )
                {
                    _e( 'No owner', '7listings' );
                }
                else
                {
                    $user = get_userdata( $user_id );
                    $name = $user->user_nicename;
                    if ( $user->user_firstname && $user->user_lastname )
                        $name = "$user->user_firstname $user->user_lastname";
                    echo $name;
                }
                break;
            default:
                parent::show( $column, $post_id );
        }
    }

    /**
     * Filter the request to just give posts for the given taxonomy, if applicable.
     *
     * @return void
     */
    function show_filters()
    {
        $featured = isset( $_GET['featured'] ) ? intval( $_GET['featured'] ) : - 1;
        echo "
			<select name='featured'>
				<option value='-1' " . selected( - 1, $featured, false ) . ">" . __( 'Show all featured', '7listings' ) . "</option>
				<option value='0' " . selected( 0, $featured, false ) . ">" . __( 'Non-featured', '7listings' ) . "</option>
				<option value='1' " . selected( 1, $featured, false ) . ">" . __( 'Featured', '7listings' ) . "</option>
			</select>
		";
    }

    /**
     * Add taxonomy filter when request posts (in screen)
     *
     * @param WP_Query $query
     *
     * @return mixed
     */
    function filter( $query )
    {
        // Sort by account
        if ( ! empty( $_GET['orderby'] ) && 'account' === $_GET['orderby'] )
        {
            $query->query_vars['orderby']  = 'meta_value_num';
            $query->query_vars['meta_key'] = 'account';
        }

        // Sort by state
        elseif ( ! empty( $_GET['orderby'] ) && 'state' === $_GET['orderby'] )
        {
            $query->query_vars['orderby']  = 'meta_value';
            $query->query_vars['meta_key'] = 'state';
        }

        // Sort by city
        elseif ( ! empty( $_GET['orderby'] ) && 'city' === $_GET['orderby'] )
        {
            $query->query_vars['orderby']  = 'meta_value';
            $query->query_vars['meta_key'] = 'city';
        }

        // Default filter
        else
        {
            parent::filter( $query );
        }
    }
}

new Sl_Wholesale_Management( 'wholesale' );
