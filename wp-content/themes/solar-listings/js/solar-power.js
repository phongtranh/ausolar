jQuery( function ( $ )
{
	function getState()
	{
		return location.pathname.replace( '/solar-power/', '' ).replace( /\//g, '' );
	}

	$( '#map' ).vectorMap( {
		map            : 'au_mill_en',
		backgroundColor: 'transparent',
		zoomOnScroll   : false,
		regionStyle    : {
			initial : {
				fill: '#CCC'
			},
			hover   : {
				fill: '#FFA500'
			},
			selected: {
				fill: '#FFA500'
			}
		},
		onRegionClick  : function ( e, code )
		{
			var pages = {
				act     : 'canberra',
				nsw     : 'new-south-wales',
				nt      : 'northern-territory',
				qld     : 'queensland',
				sa      : 'south-australia',
				tas     : 'tasmania',
				victoria: 'victoria',
				wa      : 'western-australia'
			};

			var url;
			switch ( code )
			{
				case 'AU-VIC':
					url = 'victoria';
					break;
				default:
					url = code.replace( 'AU-', '' ).toLowerCase();
			}
			url = 'http://www.australiansolarquotes.com.au/solar-power/' + pages[url];
			location.href = url;
		},
		selectedRegions: getState()
	} );
} );
