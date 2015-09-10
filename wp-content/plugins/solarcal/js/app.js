/**
 * Solar Saving Calculator App
 * 
 * @package ASQ
 * @author Tan Nguyen <tan@fitwp.com>
 */

;( function( $, angular )
{
	'use strict';

	// Define the Calculator Module
	var app = angular.module( 'Calculator', [] );

	app.controller( 'CalculatorController', function( $scope, $http, $window, $sce )
	{
		// Default variables to bind to form. The app will watch it to render the output.
		// And also listening from the form and binding to this variable.
		$scope.data = {
			mode 	: 'simple',
			postcode: 2000,
			retailer: 'ActewAGL(ACT)',
			last_bill_amount: 250,
			billing_period  : 'bimonthly',
			system_price : 4000,
			bill_inflation : 5,
			system_degradation : 0.5,
			feedin_percentage : 26,
			usage : 'midday',
			system_size: 3,
			orientation: 0,
			pitch: 40,
			retail_price: 18.304,
			fit_price: 7.5,
			years: 10
		};

		// The output. Should define empty and zero.
		$scope.output = {
		  	without_solar_bill: 0,
		  	with_solar_bill: 0,
			savings: 0,
		  	graph_max: 0,
		  	graph_min: 0,
		  	chart:{
		  		bills: [],
		  		solar: [],
		  		savings: []
		  	},
		  	eff_price_kilowatt: 0,
		  	solar_production: 0,
		  	solar_feedin_percentage: 0,
		  	solar_feedin: 0,
		  	power_savings: 0,
		  	payback: 0,
		  	roi: 0,
		  	units_per_day: 0
		};

		/**
		 * Listen form input data change then render the result
		 */
		$scope.$watch( 'data', function()
		{
			return $scope.render();
		}, true );

		/**
		 * When system size change, update system price also
		 * 
		 * @return void
		 */
		$scope.onSystemSizeChangeEventHandler = function()
		{
			var size = $scope.data.system_size;

			if ( typeof $scope.priceMatch[size] != 'undefined' )
				$scope.data.system_price = $scope.priceMatch[size];
		};

		/**
		 * When retailer change, update retailer price and fit price also
		 * 
		 * @return void
		 */
		$scope.onRetailerChangeEventHandler = function()
		{
			angular.forEach( $scope.retailers, function( retailer )
			{
				if ( retailer.name == $scope.data.retailer )
				{
					$scope.data.retail_price 	= retailer.pay;
					$scope.data.fit_price 		= retailer.fit;
				}
			} );
		};
		
		$scope.render = function()
		{
			$scope.output.solar_feedin_percentage = $scope.data.feedin_percentage;

			if (  $scope.data.usage == 'allday')
			{
				if( $scope.data.feedin_percentage >= 59 )
					$scope.output.solar_feedin_percentage = 87;
				else
					$scope.output.solar_feedin_percentage = $scope.data.feedin_percentage * 1.5;
			}
			else if( $scope.data.usage == 'evenings')
			{	
					$scope.output.solar_feedin_percentage = $scope.data.feedin_percentage * .5;
			}


			var $billinflation 		= $scope.data.bill_inflation;
			var $systemdegradation 	= $scope.data.system_degradation;
			var $lastbill 			= $scope.data.last_bill_amount;
			var $retailprice 		= $scope.data.retail_price;

			var billing_periods = {
				quarterly : 4,
				bimonthly : 6,
				monthly : 12
			};

			var $valuefordevide = billing_periods[$scope.data.billing_period];

			var $orien_value = $scope.calculateOrientation();

			$scope.output.solar_production = $scope.data.system_size * $orien_value * 365;

			$scope.output.units_per_day = $scope.output.solar_production / 365;

			$scope.output.solar_feedin =  $scope.output.solar_feedin_percentage * $scope.output.solar_production / 100;

			$scope.output.without_solar_bill = $lastbill * $valuefordevide;

			$scope.output.power_savings = ( ( $scope.output.solar_production - $scope.output.solar_feedin ) * ( $retailprice / 100 ) ) + ( $scope.output.solar_feedin * ( $scope.data.fit_price /100 ) );

			$scope.output.with_solar_bill = $scope.output.without_solar_bill -  $scope.output.power_savings;

			var $priceinsent = $retailprice / 100 ;

			var $averebwos = $scope.output.without_solar_bill / $priceinsent ;

			$scope.output.eff_price_kilowatt = ( $scope.output.with_solar_bill / $averebwos) * 100 ;

			$scope.output.payback = $scope.data.system_price /  $scope.output.power_savings;

			$scope.output.roi = ( $scope.output.power_savings / $scope.data.system_price) * 100;

			$scope.output.chart.bills[0] 	= $scope.output.without_solar_bill;
			$scope.output.chart.solar[0] 	=  $scope.output.with_solar_bill;
			$scope.output.chart.savings[0] 	=  $scope.output.power_savings;

			for ( var i = 1; i <= 19; i++ ) 
			{
				$scope.output.chart.bills[i] 	= $scope.output.chart.bills[i-1] + ( $scope.output.chart.bills[i-1] * $billinflation/100 );
				$scope.output.chart.solar[i] 	= $scope.output.chart.solar[i-1] + ( $scope.output.chart.solar[i-1] * $billinflation/100 ) + ( $scope.output.chart.solar[i-1] * $systemdegradation / 100 ); 
				$scope.output.chart.savings[i] 	= $scope.output.chart.savings[i-1] + ( $scope.output.chart.savings[i-1] * $billinflation/100 ) - ( $scope.output.chart.savings[i-1]*$systemdegradation/100 ); 
			}

			$scope.output.without_solar_bill 	= 0;
			$scope.output.with_solar_bill 	= 0;
			$scope.output.savings 			= 0;

			for ( var i = 0; i < $scope.data.years; i++ )
			{
				$scope.output.without_solar_bill 	+= $scope.output.chart.bills[i];
				$scope.output.with_solar_bill 	+= $scope.output.chart.solar[i];
				$scope.output.savings 			+= $scope.output.chart.savings[i];
			}

			$scope.output.graph_max = $scope.output.chart.bills[19] + ( $scope.output.chart.bills[19] * 20 / 100 );
			$scope.output.graph_min = 0;

			if( $scope.output.chart.bills[0] > 1000 )
				$scope.output.graph_min = 100;
			else if( $scope.output.chart.bills[0] > 2000)
				$scope.output.graph_min = 200;
			else if( $scope.output.chart.bills[0] > 2500 )
				$scope.output.graph_min = 300;
			else if( $scope.output.chart.bills[0] > 3500 )
				$scope.output.graph_min = 400;
			else if( $scope.output.chart.bills[0] > 6000 )
				$scope.output.graph_min = 500;

		};

		$scope.calculateOrientation = function()
		{
			if( $scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'NE' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'E' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'W' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'S' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'WN' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'SW' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'E' && $scope.data.pitch == 10 
				|| $scope.orientations[$scope.data.orientation] == 'W' && $scope.data.pitch == 10 )
			{
				
					return (4.18 * 88) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 10 
				|| $scope.orientations[$scope.data.orientation] == 'WN' && $scope.data.pitch == 10)
			{
				
					return (4.18 * 94) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 20 
				|| $scope.orientations[$scope.data.orientation] == 'WN' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 95) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 30)
			{
				
					return (4.18 * 100) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 99) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 50
				|| $scope.orientations[$scope.data.orientation] == 'WN' && $scope.data.pitch == 30)
			{
				
					return (4.18 * 96) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'NE' && $scope.data.pitch == 10 
				|| $scope.orientations[$scope.data.orientation] == 'NE' && $scope.data.pitch == 20 
				|| $scope.orientations[$scope.data.orientation] == 'WN' && $scope.data.pitch == 20 
				|| $scope.orientations[$scope.data.orientation] == 'WN' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 92) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'NE' && $scope.data.pitch == 30)
			{
				
					return (4.18 * 93) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'NE' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 91) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SE' && $scope.data.pitch == 0 
				|| $scope.orientations[$scope.data.orientation] == 'E' && $scope.data.pitch == 20)
			{
				
					return (4.18 * 86) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'W' && $scope.data.pitch == 20 
				|| $scope.orientations[$scope.data.orientation] == 'E' && $scope.data.pitch == 30 
				|| $scope.orientations[$scope.data.orientation] == 'NE' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 87) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'E' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 76) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SE' && $scope.data.pitch == 20 
				|| $scope.orientations[$scope.data.orientation] == 'S' && $scope.data.pitch == 20)
			{
				
					return (4.18 * 74) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SE' && $scope.data.pitch == 10
				|| $scope.orientations[$scope.data.orientation] == 'W' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 81) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SE' && $scope.data.pitch == 30)
			{
				
					return (4.18 * 67) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SE' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 60) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SE' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 53) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'S' && $scope.data.pitch == 30
				|| $scope.orientations[$scope.data.orientation] == 'SW' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 65) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'S' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 56) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'S' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 48) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SW' && $scope.data.pitch == 10)
			{
				
					return (4.18 * 84) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SW' && $scope.data.pitch == 20)
			{
				
					return (4.18 * 78) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SW' && $scope.data.pitch == 30)
			{
				
					return (4.18 * 75) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'SW' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 59) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'W' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 77) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'N' && $scope.data.pitch == 50)
			{
				
					return (4.18 * 97) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'E' && $scope.data.pitch == 40)
			{
				
					return (4.18 * 80) / 100;
			}
			else if($scope.orientations[$scope.data.orientation] == 'S' && $scope.data.pitch == 10)
			{
				
					return (4.18 * 82) / 100;
			}
			else
			{
				return 'There is an ERROR!';
			}
		};

	} );
	
	app.run( function( $rootScope )
	{
		$rootScope.orientations = ['N','NE','E','SE','S','SW','W','WN','N'];

		$rootScope.priceMatch = {
			1.4:2000,1.6:2240,2.0:2720,2.2:3200,2.4:3360,2.6:3520,2.8:3680,3.0:3840,3.2:4000,3.4:4516,3.6:4774,
			3.8:5032,4.0:5290,4.2:5632,4.4:5974,4.6:6316,4.8:6658,5.0:7000,5.2:7500,5.4:8000,5.6:8300,5.8:8640,6.0:8860,
		  6.2:9240,6.4:9750,6.8:9930,6.9:10225,7.1:10500
		};

		$rootScope.retailers = [
			{
		       name : 'ActewAGL(ACT)',
		       pay : 18.304,
		       fit : 7.5,
    		},
	    	{
		       name : 'AGL Energy(NSW)',
		       pay : 29.66,
		       fit: 8,
	    	},
    		{
	       		name : 'AGL Energy(SA)',
	       		pay : 30.48,
	       		fit: 8,
	    	},
   			{
		       name : 'AGL Energy(VIC)',
		       pay : 28.4,
		       fit: 8,
    		},
    		{
		       name : 'AGL Energy(QLD)',
		       pay : 25.37,
		       fit: 8,
    		},
    		{
		       name : 'Energy Australia(QLD)',
		       pay : 25.37,
		       fit: 6,
    		},
		    {
		       name : 'Energy Australia(NSW)',
		       pay : 34,
		       fit: 5.1,
		    	},
		    {
		       name : 'Energy Australia(VIC)',
		       pay : 34,
		       fit: 8,
		    	},
		    {
		       name : 'Energy Australia(SA)',
		       pay : 35,
		       fit: 7.6,
		    	},
		    {
		       name : 'Momentum Energy(SA)',
		       pay : 35,
		       fit: 7.6,
		    	},
		    {
		       name : 'Alinta Energy(VIC)',
		       pay : 34,
		       fit: 8,
		    	},
			{
		       name : 'Alinta Energy(SA)',
		       pay : 41,
		       fit: 7.6,
		    	},
			{
		       name : 'Diamond Energy(SA)',
		       pay : 8,
		       fit: 8,
		    	},
			{
		       name : 'Simply Energy(VIC)',
		       pay : 35,
		       fit: 8,
		    	},
			{
		       name : 'Simply Energy(SA)',
		       pay : 35.96,
		       fit: 7.6,
		    	},
			{
		       name : 'Synergy(WA)',
		       pay : 8.8529,
		       fit: 8.8529,
		    	},
			{
		       name : 'Horizon Power(WA)',
		       pay : 50,
		       fit: 50,
		    	},
			{
		       name : 'Other',
		       pay : 1,
		       fit: 0,
		    }
		];
	} );
} )( jQuery, angular );