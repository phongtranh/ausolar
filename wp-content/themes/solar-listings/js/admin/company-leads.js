// bootstrap modal + transition
+function(a){"use strict";var b=function(b,c){this.options=c,this.$element=a(b),this.$backdrop=this.isShown=null,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};b.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},b.prototype.toggle=function(a){return this[this.isShown?"hide":"show"](a)},b.prototype.show=function(b){var c=this,d=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(d);if(this.isShown||d.isDefaultPrevented())return;this.isShown=!0,this.escape(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.backdrop(function(){var d=a.support.transition&&c.$element.hasClass("fade");c.$element.parent().length||c.$element.appendTo(document.body),c.$element.show().scrollTop(0),d&&c.$element[0].offsetWidth,c.$element.addClass("in").attr("aria-hidden",!1),c.enforceFocus();var e=a.Event("shown.bs.modal",{relatedTarget:b});d?c.$element.find(".modal-dialog").one(a.support.transition.end,function(){c.$element.focus().trigger(e)}).emulateTransitionEnd(300):c.$element.focus().trigger(e)})},b.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b);if(!this.isShown||b.isDefaultPrevented())return;this.isShown=!1,this.escape(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").attr("aria-hidden",!0).off("click.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one(a.support.transition.end,a.proxy(this.hideModal,this)).emulateTransitionEnd(300):this.hideModal()},b.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){this.$element[0]!==a.target&&!this.$element.has(a.target).length&&this.$element.focus()},this))},b.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.bs.modal",a.proxy(function(a){a.which==27&&this.hide()},this)):this.isShown||this.$element.off("keyup.dismiss.bs.modal")},b.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.removeBackdrop(),a.$element.trigger("hidden.bs.modal")})},b.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},b.prototype.backdrop=function(b){var c=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var d=a.support.transition&&c;this.$backdrop=a('<div class="modal-backdrop '+c+'" />').appendTo(document.body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){if(a.target!==a.currentTarget)return;this.options.backdrop=="static"?this.$element[0].focus.call(this.$element[0]):this.hide.call(this)},this)),d&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in");if(!b)return;d?this.$backdrop.one(a.support.transition.end,b).emulateTransitionEnd(150):b()}else!this.isShown&&this.$backdrop?(this.$backdrop.removeClass("in"),a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one(a.support.transition.end,b).emulateTransitionEnd(150):b()):b&&b()};var c=a.fn.modal;a.fn.modal=function(c,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},b.DEFAULTS,e.data(),typeof c=="object"&&c);f||e.data("bs.modal",f=new b(this,g)),typeof c=="string"?f[c](d):g.show&&f.show(d)})},a.fn.modal.Constructor=b,a.fn.modal.noConflict=function(){return a.fn.modal=c,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(b){var c=a(this),d=c.attr("href"),e=a(c.attr("data-target")||d&&d.replace(/.*(?=#[^\s]+$)/,"")),f=e.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(d)&&d},e.data(),c.data());c.is("a")&&b.preventDefault(),e.modal(f,this).one("hide",function(){c.is(":visible")&&c.focus()})}),a(document).on("show.bs.modal",".modal",function(){a(document.body).addClass("modal-open")}).on("hidden.bs.modal",".modal",function(){a(document.body).removeClass("modal-open")})}(jQuery),+function(a){function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(a.style[c]!==undefined)return{end:b[c]};return!1}"use strict",a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one(a.support.transition.end,function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b()})}(jQuery);

jQuery( function ( $ )
{
	// Change input_$id to input_$formid_$id
	// Added by Tan Nguyen 20140706
	// $('.ginput_container input, .ginput_container select').each(function()
	// {
	// 	var id = $(this).attr('id');
	// 	console.log(id);
	// 	if(typeof id !== 'undefined'){
	// 		segments = id.split('_');
	// 		$(this).attr('id', 'input_36_' + segments[1]);
	// 		console.log($(this).attr('id'));
	// 	}
	// });

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

	// Remove "disabled" attributes
	$modal.find( 'form' ).find( ':input' ).prop( 'disabled', false );
	console.log($lead);
	$lead.blur( function ()
	{
		$.post( ajaxurl, {
			action  : 'solar_fill_leads_info',
			id      : $lead.val(),
			_wpnonce: Solar.nonceFill
		}, function ( r )
		{
			if ( r.success )
			{
				// Form
				$name.val( r.data.name );
				//$email.val( r.data.email );
				$('input[name=input_8]').val(r.data.email);
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

	// Send log request when form submit
	$modal.on( 'submit', 'form', function ()
	{		
		$.post( ajaxurl, {
			action    : 'solar_write_history',
			lead_id   : $lead.val(),
			company_id: companyID,
			_wpnonce  : Solar.nonceLog
		} );
	} );

	// Print leads of company in admin page
	// $( '#admin-print-lead' ).on( 'click', function( e ) {
	// 	e.preventDefault();
	// 	window.print();
	// } );
} );
