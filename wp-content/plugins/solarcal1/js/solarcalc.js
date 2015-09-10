(function () {

var waitForFinalEvent = (function () {
	var timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {
			uniqueId = "Don't call this twice without a uniqueId";
		}
		if (timers[uniqueId]) {
			clearTimeout (timers[uniqueId]);
		}
		timers[uniqueId] = setTimeout(callback, ms);
	};
})();
Number.prototype.formatCurrency = String.prototype.formatCurrency = function(noCents,noSymbol) {
	var num = parseFloat(this.toString().replace(/\$|\,/g,''));
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	cents = cents<10?'0'+cents:cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) {
		num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
	}
	return (((sign)?'':'-') + (!noSymbol?'$':'') + num + (noCents?'':'.' + cents));
};
$(function () {
	var formCalc = document.getElementById('solarCalc');
	var $calc = $('#solarCalc');
	var $retailers = $calc.find('SELECT[name=retailer]');

	// Capture Form Submit and do Calculation
	$calc.on({
		submit:function (e) {
			e.preventDefault();
			updateCalcProduction();
			return false;
		}
	});

	// Switch Calculator Mode
	$calc.find('INPUT[name=calcMode]').change(function(e) {
		e.preventDefault();
		if (this.value =='simple') {
			$calc.removeClass('advancedMode');
		} else {
			$calc.addClass('advancedMode');
		}
	});
	$calc.find('INPUT[name="usage"],INPUT[name="yrs"]').change(updateCalcProduction);
	$calc.find(
			'INPUT[name=retailPrice],INPUT[name=fitPrice],INPUT[name=lastBillAmount],INPUT[name=systemPrice],INPUT[name=billInflation],INPUT[name=systemDegradation]'
	).keyup(updateCalcProduction);

	$calc.find('SELECT[name="retailer"]').change(function(e) {
		var $this = $(this);
		var $option = $this.find('OPTION:SELECTED');
		$calc.find('INPUT[name=retailPrice]').val($option.data('pay'));
		$calc.find('INPUT[name=fitPrice]').val($option.data('fit'));
		updateCalcProduction();
	});

	var $postcodeInput = $calc.find('INPUT[name=postcode]').on({
		keyup:function () { //
			var $this = $(this);
			$retailers.children().remove();
			if ($this.val().length==4) {
				jQuery.ajax({
					type : "post",
					dataType : "json",
					url : ''+templateUrl+'/wp-content/plugins/solarcal/calcolet.php',
					data : {action: "solarcalc_lookup_postcode", postcode : $this.val()},
					success: function(response) {
						if(response.type == "success") {
							$retailers.attr('disabled',false);
							$retailers.children().remove();
							$this.data({
								lat:response.lat,
								lng:response.lng
							})
							for(i in response.retailers) {
								var retailer = response.retailers[i];
								var $option = $('<option value="'+retailer.name+'">'+retailer.name+'</option>').data({pay:retailer.pay,fit:retailer.fit});
								$retailers.append($option);
								$retailers.trigger('change');
							}
						} else {
							alert("Error Occurred looking up your postcode");
						}
					}
				});
			}
		}
	});

	$calc.find('INPUT[name=size]').change(function (e) {
		var systemPrices = {
			1.4:2000,1.6:2240,2.0:2720,2.2:3200,2.4:3360,2.6:3520,2.8:3680,3.0:3840,3.2:4000,3.4:4516,3.6:4774,
			3.8:5032,4.0:5290,4.2:5632,4.4:5974,4.6:6316,4.8:6658,5.0:7000,5.2:7500,5.4:8000,5.6:8300,5.8:8640,6.0:8860,
		  6.2:9240,6.4:9750,6.8:9930,6.9:10225,7.1:10500
		};
		$this = $(this);
		$calc.find('INPUT[name=systemPrice]').val(systemPrices[$this.val()]);
		updateCalcProduction();
	});
	
	$calc.find('INPUT[name=size]').change(function (e) {
		var $this = $(this);
		var sysize = $this.val();
		$this.parents('.ssc_pitch').find('.ssc-preview SPAN').css({
			'background':'url('+templateUrl+'/wp-content/plugins/solarcal/images/systemsize'+sysize+'.png) no-repeat'
		});
	});


	$calc.find('INPUT[name=pitch]').change(function (e) {
		var $this = $(this);
		var degree = -$this.val();
		var degree2 = $this.val();
		$this.parents('.pitch').find('.preview SPAN').css({
			'background':'url('+templateUrl+'/wp-content/plugins/solarcal/images/roof_pitch_'+degree2+'.png) no-repeat'
		});
		updateCalcProduction();
	});

	$calc.find('INPUT[name="feedinPercentage"],INPUT[name="orientationslider"]').change(updateCalcProduction);

	function updateCalcProduction() {
		$calc.find('#savingsGraph .status').show();
		waitForFinalEvent(function () {
			calcProduction();
		},200,'calcProduction');
	}
	function calcProduction() {
		var $results = $calc.find('#calcResults');
		var params = {action: "solarcalc_calc"};
		$.each($calc.serializeArray(),function(_, kv) {
			params[kv.name] = kv.value;
		});
		jQuery.ajax({
			type : "post",
			dataType : "json",
			url : ''+templateUrl+'/wp-content/plugins/solarcal/calcolet.php',
			data : params,
			success: function(response) {
                console.log(response);
				if(response.type == "success") {
					// System Production
					$results.find('#productionKW,.noteYearlyProduction').html(response.solarProduction+' kWh');
					$results.find('#dayProductionKW').html(response.unitsPerDay+' kWh');
					$results.find('#feedInKW,.noteExportedProduction').html(response.solarFeedIn+' kWh');

					// Investment
					$results.find('#systemPrice,.noteSystemPrice').html('$'+$calc.find('INPUT[name=systemPrice]').val());
					$results.find('.electricitySavingYr,.noteSystemSaving').html('$'+Math.round(response.powerSavings));
					var fitPricekWh = parseInt($calc.find('INPUT[name=fitPrice]').val());
					$results.find('#noteFITRate').html(fitPricekWh+'c / kWh');

					$results.find('.paybackPeriod').html(response.payback+' yrs');
					$results.find('.returnOnInvestment').html(response.roi+'%');

					// Price Per kWh
					var retailPricekWh = parseInt($calc.find('INPUT[name=retailPrice]').val());
					$results.find('.retailerPriceKWH,.noteRetailRate').html(retailPricekWh+'c / kWh');
					$results.find('.effectivePriceKWH').html(response.effPriceKilowatt+'c / kWh');
					$results.find('.savingPriceKWH').html(Math.round(retailPricekWh-response.effPriceKilowatt)+'c / kWh');

					// Bills
					$results.find('#electricityCostsNoSolar').html(response.withoutSolarBill.formatCurrency(true));
					$results.find('#electricityCostsSolar').html(response.withSolarBill.formatCurrency(true));
					$results.find('#electricitySavings').html(response.savings.formatCurrency(true));

					var YearlyBill = parseFloat($calc.find('INPUT[name=lastBillAmount]').val());
					var unitsPerDay = YearlyBill / $calc.find('INPUT[name=retailPrice]').val();
					var billingPeriod = $calc.find('SELECT[name=billingPeriod]').val();
					switch (billingPeriod) {
						case 'quarterly': YearlyBill = YearlyBill*4;
						case 'monthly': YearlyBill = YearlyBill*12;
						default: YearlyBill = YearlyBill*6;
					}
					$results.find('.noteYearlyElectricityBill').html(YearlyBill.formatCurrency(true));
					$results.find('.noteYearlyElectricityBillSolar').html((YearlyBill-response.powerSavings).formatCurrency(true));
					$results.find('.noteAvgUnits').html((Math.round(unitsPerDay*10)/10)+'kWh');
					$calc.find('INPUT[name=feedinPercentage]').val(response.solarFeedInPercentage);
					$calc.find('OUTPUT[name="vFeedInPercentage"]').val(response.solarFeedInPercentage+'%');
					renderChart(response.chart,response.graphMin,response.graphMax);
					$calc.find('#savingsGraph .status').hide();
				} else {
				}
			}
		});

	}

	function renderChart(data,min,max) {
		var $innerChart = $calc.find('#savingsGraph .graphBody');
		var $yaxis = $calc.find('#savingsGraph .graphYAxis');
		var labels = [];
		$yaxis.children().remove();
		$yaxis.append(
			'<span>'+max+'</span>'+
			'<span>'+Math.floor(max*0.75)+'</span>'+
			'<span>'+Math.floor(max*0.50)+'</span>'+
			'<span>'+Math.floor(max*0.25)+'</span>'+
			'<span>'+min+'</span>'
		);
		var count = 1;
		for (i in data.bills ) {
			var withoutSolar = data.bills[i];
			var withSolar = data.solar[i];
			$innerChart.children(':nth-child('+count+')').children(':first-child').attr('title','Bill without Solar = $'+withoutSolar).css('height',(((withoutSolar-min)/(max-min))*100)+'%');
			$innerChart.children(':nth-child('+count+')').children(':last-child').attr('title','Bill with Solar = $'+withSolar+'(Saving $'+data.savings[i]+')').css('height',(((withSolar-min)/(max-min))*100)+'%');
			count++;
		}
	}


	if ($calc.find('INPUT[name=postcode]').val()!=='') {
		$calc.find('INPUT[name=postcode]').trigger('keyup');
	}


	var map,marker;
	$calc.find('.lookupGoogleMaps').click(function (e){
		e.preventDefault();
		$calc.find('.overlay').show();
		$calc.find('#googleMapsDlg').show();
		var googleMapLookup = function () {
				var latlng = new google.maps.LatLng($postcodeInput.data('lat'),$postcodeInput.data('lng'));
				map = new google.maps.Map(
					$('#googleMap').get(0),
					{
						zoom: 16,
						center: latlng,
						mapTypeId: google.maps.MapTypeId.SATELLITE,
						panControl: false,
						zoomControl: true,
						zoomControlOptions: {
							style: google.maps.ZoomControlStyle.SMALL
						},
						mapTypeControl: false,
						scaleControl: false,
						streetViewControl: false,
						overviewMapControl: false
					}
				);



				var $addressInput = $calc.find('INPUT[name=lookupAddress]');
				autocomplete = new google.maps.places.Autocomplete(
					$addressInput.get(0),
					{types: ['geocode'],componentRestrictions: { 'country': 'au'}}
				);

				google.maps.event.addListener(autocomplete, 'place_changed', function(e,a) {
					if (marker) marker.setMap(null);
					var place = autocomplete.getPlace();
					//var latlng = new google.maps.LatLng($postcodeInput.data('lat'),$postcodeInput.data('lng'));
					map.setZoom(21);
					map.panTo(place.geometry.location);
					marker = new google.maps.Marker({
						position: place.geometry.location,
						map:map
					});

					return false;

				});
		}
		googleMapLookup();
	});
	$calc.find('#googleMapsDlg SPAN.close,.overlay').on({
		click:function (e) {
			e.preventDefault();
			$calc.find('.overlay').hide();
			$calc.find('#googleMapsDlg').hide();
			return false;
		}
	});
	$calc.find('.overlay').on({
		click:function () {
			$calc.find('.overlay').hide();
			$calc.find('#googleMapsDlg').hide();
		}
	});


	function calcAngle(x1, x2, y1, y2,step) {
		var radian = Math.atan2(y1-y2,x1-x2);
		var calcAngle = radian*(180/Math.PI);
		if (radian>0) calcAngle = calcAngle-90;
		else calcAngle = calcAngle+270;
		if (calcAngle<0) calcAngle = 360+calcAngle;
		calcAngle = Math.round(calcAngle/step,0)*step
		return calcAngle;
	}
});

// Onload Window Events
window.addEventListener('load',function(){
  // Check if Google is loaded
  if (typeof google === 'object' && typeof google.maps === 'object') {
    initializeGoogleMaps();
  } else {
  	// This function is not defined
    //loadGoogleMapsScript();
  }
});
// Callback for Google Maps on Loaded
function initializeGoogleMaps() {
	initAutoComplete('gAutoComplete2');
}

var initAutoComplete = function(id) {
	var input = document.getElementById(id);

	if ( typeof google.maps.places == 'undefined' )
		return;

	var autocomplete = new google.maps.places.Autocomplete(
		input,
		{
			types: ['geocode'],
			componentRestrictions: { 'country': 'au'}
		}
	);

	google.maps.event.addListener(autocomplete, 'place_changed', function(e,a) {
		input.setAttribute('address','');
		var place = autocomplete.getPlace();
		var parent = input.parentNode.parentNode;
		var address = place.name;
		input.setAttribute('address',address);
		for (i in place.address_components) {
			var comp = place.address_components[i];
			switch (comp.types[0]) {
				case 'locality':
					parent.querySelector('INPUT[name=suburb]').value = comp.long_name;
					break;
				case 'administrative_area_level_1':
					parent.querySelector('INPUT[name=state]').value = comp.short_name;
					break;
				case 'postal_code':
					parent.querySelector('INPUT[name=postcode]').value = comp.short_name;
					break;
			}
		}
		return false;
	});
};
})();