jQuery( function ( $ )
{
	// Toggle choices based on value of select box
	$( '.toggle-choices select' ).change( function ()
	{
		var $this = $( this ),
			name = $this.attr( 'name' ),
			value = $this.val(),
			$el = $( '[data-name="' + name + '"]' );

		$el.slideUp().filter( '[data-value="' + value + '"]' ).slideDown();
	} ).trigger( 'change' );

	// Validation for edit area
	$( '#modal-edit-area' ).submit( function ()
	{
		var value = $( 'select[name="service_radius"]' ).val(),
			$postcodes = $( 'textarea[name="service_postcodes"]' ),
			$radius = $( 'input[name="leads_service_radius"]' );

		if ( 'postcodes' == value && !$postcodes.val() )
		{
			$postcodes.addClass( 'is-empty' );
			alert( 'Please enter postcodes' );
			return false;
		}
		if ( 'radius' == value && !$radius.val() )
		{
			$radius.addClass( 'is-empty' );
			alert( 'Please enter service radius' );
			return false;
		}

		return true;
	} );

	// Validation for cancel
	$( '#modal-stop' ).submit( function ()
	{
		var value = $( 'select[name="cancel_reason"]' ).val(),
			$suspendDays = $( 'input[name="suspend_days"]' ),
			$otherReason = $( 'textarea[name="other_reason"]' );

		if ( 'other' == value && !$otherReason.val() )
		{
			$otherReason.addClass( 'is-empty' );
			alert( 'Please enter your reason' );
			return false;
		}
		if ( 'too_many_temp' == value && !$suspendDays.val() )
		{
			$suspendDays.addClass( 'is-empty' );
			alert( 'Please enter how many days you want to suspend your service for' );
			return false;
		}

		return true;
	} );

	// Validation for lead types
	$( '#modal-lead-type' ).submit( function ()
	{
		var valid = !!( $( this ).find( ':checkbox:checked' ).length );
		if ( !valid )
			alert( 'Please select 1 option' );
		return valid;
	} );

	// Validation for service types
	$( '#modal-service-type' ).submit( function ()
	{
		var valid = !!( $( this ).find( ':checkbox:checked' ).length );
		if ( !valid )
			alert( 'Please select 1 option' );
		return valid;
	} );

	// Validation for assessment
	$( '#modal-assessment' ).submit( function ()
	{
		var valid = !!( $( this ).find( ':checkbox:checked' ).length );
		if ( !valid )
			alert( 'Please select 1 option' );
		return valid;
	} );

	// Autofill
	var $modal = $( '#modal-reject' ),
		$name = $( '#input_36_7' ),
		$email = $( '#input_36_8' ),
		$mobile = $( '#input_36_10' ),
		$lead = $( '#input_36_2' ),
		$header = $modal.find( '.modal-header' ),
		$headerID = $header.find( '.lead-id' ),
		$headerName = $header.find( '.name' ),
		$headerSize = $header.find( '.system-size' ),
		$headerCity = $header.find( '.city' ),
		$headerState = $header.find( '.state' );
	$lead.blur( function ()
	{
		$.post( Solar.ajaxUrl, {
			action  : 'solar_fill_leads_info',
			id      : $lead.val(),
			_wpnonce: Solar.nonceFill
		}, function ( r )
		{
			if ( r.success )
			{
				// Form
				$name.val( r.data.name );
				$email.val( r.data.email );
				$mobile.val( r.data.mobile );

				// Header
				$headerID.text( $lead.val() );
				$headerName.text( r.data.name );
				$headerSize.text( r.data.system_size );
				$headerCity.text( r.data.city );
				$headerState.text( r.data.state );
			}
			else
			{
				alert( 'Can not find lead info. Please check your lead ID or manually enter contact info.' );
			}
		}, 'json' );
	} );

	// Show link to map with service area
	var $select = $( '#input_36_3' ),
		companyID = $( '#company-id' ).val();
	$select.change( function ()
	{
		if ( $select.val() != 'The Prospect is outside your Elected Service Area' )
		{
			$select.siblings( 'p' ).remove();
			return;
		}

		var $a = $( '<a href="#" target="_blank">Please verify the service area in this map before reject the lead.</a>' );
		$a.insertAfter( $select );
		$a.attr( 'href', Solar.homeUrl + '?company_id=' + companyID + '&lead_id=' + $lead.val() );
		$a.wrap( '<p></p>' );
	} );

	// Reject single lead
	$( '#leads' ).on( 'click', '.reject-lead', function ( e )
	{
		e.preventDefault();

		var leadId = $( this ).data( 'lead_id' );
		$lead.val( leadId ).trigger( 'blur' );
	} );

	// Form submit
	$modal.find( '.modal-footer .btn-primary' ).click( function ( e )
	{
		e.preventDefault();
		$modal.find( 'form' ).trigger( 'submit' );
	} );

	$modal.on( 'submit', 'form', function ()
	{
		// Send log request when form submit
		$.post( Solar.ajaxUrl, {
			action    : 'solar_write_history',
			lead_id   : $lead.val(),
			company_id: companyID,
			_wpnonce  : Solar.nonceLog
		} );

		// Refresh page AFTER form is submitted
		var $doc = $( document );
		$doc.ajaxComplete( function ()
		{
			setTimeout( function ()
			{
				location.reload();
			}, 3000 );
		} );
	} );
} );
