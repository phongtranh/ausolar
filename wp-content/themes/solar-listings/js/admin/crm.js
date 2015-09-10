// Remember put ; sign before to work if some before scripts forgot it
;(function ( jQuery, angular )
{
	'use strict';

	angular.module( 'app', [] )

		.controller( 'CrmController', function ( $scope, $window, $http, $sce )
		{
			// All Leads save here
			$scope.$leads = [];

			// Current editing lead
			$scope.$active = {};

			// Notes of current lead
			$scope.$activeNotes = [];

			// All administrator and editor
			$scope.$users = [];

			// Message to user
			$scope.$message = '';

			$scope.isLoading = false;

			$scope.$call_back_times = [];

			$scope.$customer_services = [];

			$scope.target = 'self';

			$scope.init = function ()
			{
				$scope.$leads = $window.$leads;
				$scope.$users = $window.$users;
				$scope.$call_back_times = $window.$call_back_times;
				$scope.$customer_services = $window.$customer_services;
			};

			/**
			 * Edit lead on frontend
			 * @param $lead Lead Object
			 */
			$scope.editLead = function ( $lead )
			{
				$scope.$message = '';
				$scope.$activeNotes = [];
				$scope.$active = $lead;
				$scope.loadNotes();
			};

			/**
			 * Push the active lead (current editing lead) to the server
			 * to update its information.
			 */
			$scope.updateLead = function ( $event )
			{
				// Post the lead information to update
				var $end_point = $window.ajaxurl + '?action=update_lead_45';

				jQuery( '.modal-loading' ).show();

				if ( !$scope.isLoading )
				{
					$scope.isLoading = true;

					if ( $scope.$active[11] == 0 )
						$scope.$activeNotes = [];

					$http( {
						method: 'POST',
						url   : $end_point,
						data  : $scope.$active
					} ).success( function ( data )
					{
						$scope.$activeNotes.push( {
							date   : 'just now',
							user_id: $window.$current_user_id,
							outcome: $scope.$active[4],
							note   : $scope.$active[5]
						} );

						$scope.$active[11]++;

						$scope.$active['status'] = 'processed';

						$scope.$message = data;

						if ( $scope.$active[4] == 'Interested' )
						{
							var param = {
								source    : $scope.$active[1],
								state     : $scope.$active['14.4'],
								name_first: $scope.$active['2.3'],
								name_last : $scope.$active['2.6'],
								postcode  : $scope.$active['14.5'],
								email     : $scope.$active[10],
								phone     : $scope.$active[9],
								time      : $scope.$active[12],
								street    : $scope.$active['14.1'],
								suburb    : $scope.$active['14.3']
							};

							var query_string = jQuery.param( param );

							var url = 'https://www.australiansolarquotes.com.au/internal-solar-quotes-form/?' + query_string;

							var win = window.open( url, '_blank' );
							win.focus();
						}

						if ( $scope.$active[4] == 'No answer' || $scope.$active[4] == 'Incorrect information' || $scope.$active[4] == 'Incorrect phone number' )
						{
							var message = '';
							if ( $scope.$active[4] == 'No answer' )
								message = 'noanswer';
							if ( $scope.$active[4] == 'Incorrect information' )
								message = 'incorrect';

							var params = {
								first  : $scope.$active['2.3'],
								phone  : $scope.$active[9],
								email  : $scope.$active[10],
								message: message
							};

							var query_string = jQuery.param( params );

							var url = 'https://www.australiansolarquotes.com.au/sms/?' + query_string;

							var win = window.open( url, '_blank' );
							win.focus();

							tb_remove();
						}

						jQuery( '.modal-loading' ).hide();

						tb_remove();

					} ).error( function ( data )
					{
						console.log( 'error' );
						$scope.$message = 'Error during updating lead. Please try again.';
					} );

					$scope.isLoading = false;
				}
			};

			$scope.loadNotes = function ()
			{
				var $end_point = $window.ajaxurl + '?action=get_notes_lead_45&lead_id=' + $scope.$active.id;
				if ( !$scope.isLoading )
				{
					jQuery( '.modal-loading' ).show();

					$scope.isLoading = true;

					$http( {
						method: 'GET',
						url   : $end_point
					} ).success( function ( result )
					{
						console.log(result);
						if ( !result || typeof result === 'undefined' ) result = [];

						$scope.$activeNotes = result;

						jQuery( '.modal-loading' ).hide();
					} ).error( function ( message )
					{

					} );

					$scope.isLoading = false;
				}

			};

			$scope.flush_conditional = function ()
			{
				//$scope.$active['8.1'] = $scope.$active['8.2'] = $scope.$active['8.3'] = '';
			};

			$scope.trustAsHtml = function($html) {
				return $sce.trustAsHtml($html);
			};
		} );

})( jQuery, angular );