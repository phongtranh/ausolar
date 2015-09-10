jQuery( function( $ )
{
    var $message = $( '#ajax-message' );

    $( '#wholesale-signup-form' ).submit( function( e )
    {
        e.preventDefault();

        $.post( SolarWholesale.ajaxUrl, {
            action: 'sl_wholesale_signup',
            nonce : SolarWholesale.nonce,
            sl_nonce_save_wholesale: SolarWholesale.nonceSave,
            data  : $( this ).serialize()
        }, function( r )
        {
            // Error when signup
            if ( !r.success )
            {
                $message.removeClass( 'alert-success' ).addClass( 'alert alert-error' ).html( r.data );
                return;
            }

            // Signup success, user paid for membership
            $message.removeClass( 'alert-error' ).addClass( 'alert alert-success' ).html( r.data.message );
            window.location = r.data.redirect;
            return;
        } );
    } );
} );