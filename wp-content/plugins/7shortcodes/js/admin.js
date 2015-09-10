// bootstrap-modal.js v2.3.2
!function(t){"use strict";var e=function(e,i){this.options=i,this.$element=t(e).delegate('[data-dismiss="modal"]',"click.dismiss.modal",t.proxy(this.hide,this)),this.options.remote&&this.$element.find(".modal-body").load(this.options.remote)};e.prototype={constructor:e,toggle:function(){return this[this.isShown?"hide":"show"]()},show:function(){var e=this,i=t.Event("show");this.$element.trigger(i),this.isShown||i.isDefaultPrevented()||(this.isShown=!0,this.escape(),this.backdrop(function(){var i=t.support.transition&&e.$element.hasClass("fade");e.$element.parent().length||e.$element.appendTo(document.body),e.$element.show(),i&&e.$element[0].offsetWidth,e.$element.addClass("in").attr("aria-hidden",!1),e.enforceFocus(),i?e.$element.one(t.support.transition.end,function(){e.$element.focus().trigger("shown")}):e.$element.focus().trigger("shown")}))},hide:function(e){e&&e.preventDefault();e=t.Event("hide"),this.$element.trigger(e),this.isShown&&!e.isDefaultPrevented()&&(this.isShown=!1,this.escape(),t(document).off("focusin.modal"),this.$element.removeClass("in").attr("aria-hidden",!0),t.support.transition&&this.$element.hasClass("fade")?this.hideWithTransition():this.hideModal())},enforceFocus:function(){var e=this;t(document).on("focusin.modal",function(t){e.$element[0]===t.target||e.$element.has(t.target).length||e.$element.focus()})},escape:function(){var t=this;this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.modal",function(e){27==e.which&&t.hide()}):this.isShown||this.$element.off("keyup.dismiss.modal")},hideWithTransition:function(){var e=this,i=setTimeout(function(){e.$element.off(t.support.transition.end),e.hideModal()},500);this.$element.one(t.support.transition.end,function(){clearTimeout(i),e.hideModal()})},hideModal:function(){var t=this;this.$element.hide(),this.backdrop(function(){t.removeBackdrop(),t.$element.trigger("hidden")})},removeBackdrop:function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},backdrop:function(e){var i=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var o=t.support.transition&&i;if(this.$backdrop=t('<div class="modal-backdrop '+i+'" />').appendTo(document.body),this.$backdrop.click("static"==this.options.backdrop?t.proxy(this.$element[0].focus,this.$element[0]):t.proxy(this.hide,this)),o&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!e)return;o?this.$backdrop.one(t.support.transition.end,e):e()}else!this.isShown&&this.$backdrop?(this.$backdrop.removeClass("in"),t.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one(t.support.transition.end,e):e()):e&&e()}};var i=t.fn.modal;t.fn.modal=function(i){return this.each(function(){var o=t(this),n=o.data("modal"),s=t.extend({},t.fn.modal.defaults,o.data(),"object"==typeof i&&i);n||o.data("modal",n=new e(this,s)),"string"==typeof i?n[i]():s.show&&n.show()})},t.fn.modal.defaults={backdrop:!0,keyboard:!0,show:!0},t.fn.modal.Constructor=e,t.fn.modal.noConflict=function(){return t.fn.modal=i,this},t(document).on("click.modal.data-api",'[data-toggle="modal"]',function(e){var i=t(this),o=i.attr("href"),n=t(i.attr("data-target")||o&&o.replace(/.*(?=#[^\s]+$)/,"")),s=n.data("modal")?"toggle":t.extend({remote:!/#/.test(o)&&o},n.data(),i.data());e.preventDefault(),n.modal(s).one("hide",function(){i.focus()})})}(window.jQuery);

var slsApp = angular.module( 'sls', ['ngSanitize'] );

// Color picker directive
slsApp.directive( 'colorpicker', function()
{
	return {
		restrict: 'A',
		require : 'ngModel',
		link : function ( scope, element, attrs, ngModelCtrl )
		{
			jQuery( function()
			{
				element.wpColorPicker( {
					change: function( e, ui )
					{
						ngModelCtrl.$setViewValue( ui.color.toString() );
						scope.$apply();
					}
				} );
			} );
		}
	};
} );

// Controller for which has icons only
function SlsIcon( $scope )
{
	for ( var i = 0, l = Sls.icons.length, icons = []; i < l; i++ )
	{
		icons.push( {value: Sls.icons[i]} );
	}
	$scope.icons = icons;
}

// Block controller for tabs, accordions
function SlsBlock( $scope )
{
	$scope.blocks = [{title: '', content: ''}];
	$scope.icon = 'angle-down';
	$scope.icons = [];
	var icons = ['angle-down', 'double-angle-down', 'chevron-down', 'caret-down', 'plus', 'plus-sign'];
	for ( var i = 0, l = icons.length; i < l; i++ )
	{
		$scope.icons.push( {value: icons[i]} );
	}

	$scope.add = function()
	{
		$scope.blocks.push( {title: '', content: ''} );
	};
}

// Controller for map
function SlsMap( $scope, $filter )
{
	$scope.controls = Sls.mapControls;
	$scope.maps = [{address: '', latitude: '',longtitude: '',marker_title: '',info_window:''}];
	$scope.selected = function()
	{
		var checked = $filter( 'filter' )( $scope.controls, {checked: true} ),
			s = [];

		for ( var i = 0, l = checked.length; i < l; i++ )
		{
			s.push( checked[i].value );
		}
		return s.join( ',' );
	};
	$scope.addmarker = function()
	{
		$scope.maps.push( {address: '', latitude: '',longtitude: '',marker_title: '',info_window:''} );
	};
}

// Controller for icon shortcode
function SlsIconShortcode( $scope )
{
	for ( var i = 0, l = Sls.icons.length, icons = []; i < l; i++ )
	{
		icons.push( {value: Sls.icons[i]} );
	}
	$scope.icons = icons;

	var stack_icons = ['circle', 'circle-blank', 'certificate', 'stop', 'box-out', 'sign-blank', 'check-empty', 'heart', 'star', 'star-empty', 'ban-circle', 'cloud', 'folder-close', 'close-alt'];
	for ( i = 0, l = stack_icons.length, icons = []; i < l; i++ )
	{
		icons.push( {value: stack_icons[i]} );
	}
	$scope.stack_icons = icons;
}

// Common jQuery action
jQuery( function( $ )
{
	var $wrap = $( 'body' ),
		d = document;

	// Insert shortcode to editor
	$wrap.on( 'click', '.sls-insert', function( e )
	{
		e.preventDefault();
		var $t = $( this ),
			$popup = $t.closest( '.sls-popup' ),
			$el = $popup.find( $t.attr( 'data-type' ) == 'shortcode' ? '.sls-shortcode' : '.sls-css' ),
			code;

		if ( $el.filter( ':visible' ).length )
			$el = $el.filter( ':visible' );
		code = $t.data( 'format' ) == 'text' ? $el.text() : $el.html();

		console.log( $t.attr( 'data-type' ) );
		console.log( $t.attr( 'data-type' ) );

		code = code.replace(/ng-[a-z]+/gi, '' )             // Angular attributes
			.replace( /[a-z0-9-_]+:;/gi, '' )               // Empty inline styles
			.replace( / [a-z0-9-_]+=['"]['"]/gi, '' )       // Empty attributes
			.replace( /^\s+|\s+$/g, '' )                    // Trim spaces
//			.replace( /<\/?div.*?>/g, '' )                  // All <div>
			.replace( /<!--.*?-->/g, '' )                   // Comments
			.replace( /&lt;/g, '<' )                        // Convert back HTML entities
			.replace( /&gt;/g, '>' );

		// Remove empty icon
		code = code.replace( '<i class="icon-"></i>', '' );

		window.send_to_editor( code );
		$popup.modal( 'hide' );
	} );

	$( '.toggle-icons ' ).each( function()
	{
		$( this ).find( '.icon-single:first' ).addClass( 'active' );
	} );

	// Insert custom list
	$wrap.on( 'click', '.sls-insert-custom-list', function()
	{
		var icon = $( '#sls-custom_list-icon' ).val(),
			iconColor = $( '.sls-color-schemes .active input' ).val(),
			textColor = $( '#sls-custom_list-text-color' ).val(),
			ed = tinyMCE.activeEditor;

		ed.execCommand( 'insertunorderedlist', false );

		var el = ed.selection.getNode();
		if ( el.tagName.toLowerCase() == 'li' )
			el = el.parentNode;
		el.className = 'sl-list custom';

		if ( icon )
			el.className += ' ' + icon;
		if ( iconColor )
			el.className += ' ' + iconColor;
		if ( textColor )
			el.style.color = textColor;

		$( this ).closest( '.sls-popup' ).modal( 'hide' );
		return false;
	} );
} );
