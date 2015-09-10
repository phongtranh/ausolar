(function ()
{
	function callback( map, latLng, marker )
	{
		var circleOptions = {
			strokeColor  : SlCompanyRadius.strokeColor,
			strokeOpacity: parseFloat( SlCompanyRadius.strokeOpacity ),
			strokeWeight : 2,
			fillColor    : SlCompanyRadius.fillColor,
			fillOpacity  : parseFloat( SlCompanyRadius.fillOpacity ),
			map          : map,
			center       : latLng,
			radius       : parseFloat( SlCompanyRadius.radius ) * 1000
		};
		var circle = new google.maps.Circle( circleOptions );
	}

	window.SlCompanyRadiusCallback = callback;
})();
