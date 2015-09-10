jQuery( function ( $ )
{
	var selected = SolarArchive.currentState ? 'AU-' + SolarArchive.currentState.toUpperCase() : '';

	$( '#map' ).vectorMap( {
		map            : 'au_mill_en',
		backgroundColor: 'transparent',
		zoomOnScroll   : false,
		regionStyle    : {
			initial: {
				fill: '#CCC'
			},
			hover  : {
				fill: '#FFA500'
			},
			selected: {
				fill: '#FFA500'
			}
		},
		onRegionClick  : function ( e, code )
		{
			var url;
			switch ( code )
			{
				case 'AU-VIC':
					url = 'victoria';
					break;
				default:
					url = code.replace( 'AU-', '' ).toLowerCase();
			}
			url = 'http://www.australiansolarquotes.com.au/solar-installers/area/' + url;
			location.href = url;
		},
		selectedRegions: selected
	} );
} );
