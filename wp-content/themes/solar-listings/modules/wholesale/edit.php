<?php

class Solar_Wholesale_Edit extends Sl_Core_Edit
{

    public function enqueue_scripts()
    {
        wp_enqueue_script( 'sl-admin' );
        wp_enqueue_script( 'sl-utils' );
    }

    public function add()
    {
        parent::add();

        add_meta_box(
            'sl-wholesale-owner',
            __( 'Wholesale Owner', '7listings' ),
            array( $this, 'box_owner' ),
            $this->post_type,
            'side'
        );

        add_meta_box(
            'sl-wholesale-code',
            __( 'Wholesale Code', '7listings'),
            array( $this, 'box_code' ),
            $this->post_type,
            'side'
        );

        add_meta_box(
            'sl-wholesale-lead-value',
            __( 'Wholesale Lead Value', '7listings'),
            array( $this, 'box_lead_value' ),
            $this->post_type,
            'side'
        );
    }

    public function box_owner()
    {
        locate_template( array(
            "inc/admin/tabs/{$this->post_type}/owner.php",
            'inc/admin/tabs/parts/owner.php',
        ), true );
    }

    public function box_code()
    {
        require __DIR__ . "/meta-code.php";
    }

    // Only admin can access this meta box
    public function box_lead_value()
    {
        require __DIR__ . "/meta-lead-value.php";
    }

    public function render()
    {
        $company_dir = THEME_TABS . "company/";

        wp_nonce_field( "save-post-{$this->post_type}", "sl_nonce_save_{$this->post_type}" );

        $tabs = array( 'location.php', 'contact.php', 'payment.php' );

        echo '<div class="tabs"><ul>';

        echo '
			<li>' . __( 'Location', '7listings' ) . '</li>
			<li>' . __( 'Contact Info', '7listings' ) . '</li>
			<li>' . __( 'Contacts', '7listings' ) . '</li>
		';

        do_action( 'wholesale_edit_tab' );

        echo '</ul>';

        // Load tab content
        foreach ( $tabs as $tab )
        {
            echo '<div>';
            include $company_dir . $tab;
            echo '</div>';
        }

        do_action( 'wholesale_edit_tab_content' );

        echo '</div>';
    }

    public function save( $post_id )
    {
        parent::save( $post_id );
    }

    public function save_post( $post_id )
    {
        if ( $logo = peace_handle_upload( sl_meta_key( 'logo', $this->post_type ) ) )
            set_post_thumbnail( $post_id, $logo );

        // Text and Select fields
        $fields = array(
            'account',
            'facebook', 'twitter', 'googleplus', 'pinterest', 'linkedin', 'instagram', 'rss',
            'location_marker',

            'invoice_name',
            'invoice_email',
            'invoice_phone',

            'service_radius',
            'service_postcodes',
            'leads_service_radius',
            'wholesale_code',
            'wholesale_lead_value'
        );
        $days = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
        foreach ( $days as $day )
        {
            $fields[] = "business_hours_{$day}_from";
            $fields[] = "business_hours_{$day}_to";
        }
        foreach ( $fields as $field )
        {
            if ( isset( $_POST[$field] ) )
                update_post_meta( $post_id, $field, $_POST[$field] );
        }

        // Update user only when not empty value is passed
        if ( !empty( $_POST['user'] ) )
            update_post_meta( $post_id, 'user', $_POST['user'] );

        // Checkboxes
        $checkboxes = array( 'open_247', 'featured', 'location_marker', 'operating' );
        foreach ( $days as $day )
        {
            $checkboxes[] = "business_hours_$day";
        }
        if ( is_admin() )
            $checkboxes[] = 'service_area';

        foreach ( $checkboxes as $cb )
        {
            $value = empty( $_POST[$cb] ) ? 0 : 1;
            update_post_meta( $post_id, $cb, $value );
        }

        // Locations
        $locations = array( 'state', 'city', 'area' );
        $term_ids = array();
        foreach ( $locations as $location )
        {
            if ( empty( $_POST[$location] ) )
            {
                delete_post_meta( $post_id, $location );
                continue;
            }

            $value = $_POST[$location];

            // State is passed as ID, must change to name (in admin only, in front end - passed as name
            if ( 'state' == $location && is_admin() && is_numeric( $value ) )
            {
                $state = get_term( $value, 'location', ARRAY_A );
                $value = empty( $state ) || is_wp_error( $state ) ? '' : $state['name'];
                $term = $state;
            }
            else
            {
                // Insert term if not exists
                $term = term_exists( $value, 'location' );
                if ( 0 === $term || null === $term )
                    $term = wp_insert_term( $value, 'location' );
            }

            update_post_meta( $post_id, $location, $value );
            if ( ! empty( $term ) && ! is_wp_error( $term ) )
                $term_ids[] = $term['term_id'];
        }
        wp_set_post_terms( $post_id, $term_ids, 'location', false ); // false to replace all current taxonomies

        do_action( "{$this->post_type}_save_post", $post_id );
    }
}

new Solar_Wholesale_Edit( 'wholesale' );