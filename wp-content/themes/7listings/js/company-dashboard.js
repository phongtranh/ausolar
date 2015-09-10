(function ()
{
	google.load( 'visualization', '1.0', { 'packages': ['corechart'] } );

	google.setOnLoadCallback( drawChart );

	/**
	 * Callback that creates and populates a data table,
	 * instantiates the pie chart, passes in the data and
	 * draws it.
	 */
	function drawChart()
	{
		// Create the data table.
		var chart = new google.visualization.LineChart( document.getElementById( 'views-chart' ) ),
			options = {
				// axisTitlesPosition: 'none',
				// legend: {position: 'none'},
				backgroundColor: { fill: 'transparent' },
				chartArea      : { top: 5, left: 0, bottom: 5, width: '100%', height: '80%' },
				colors         : ['#91BD09'],
				lineWidth      : 4,
				pointSize      : 8,
				vAxis          : { textPosition: 'in', viewWindowMode: 'pretty' }
				//	extra parameters:
				//	https://google-developers.appspot.com/chart/interactive/docs/gallery/columnchart
			},
			data = new google.visualization.DataTable(),
			rows = [],
			key, day, views, total = 0, i = 0, label, j = 1,
			months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

		for ( key in SlCompany.views )
		{
			if ( SlCompany.views.hasOwnProperty( key ) )
				i = 1 - i;
		}

		data.addColumn( 'string', 'Date' );
		data.addColumn( 'number', 'Views' );
		data.addColumn( { type: 'string', role: 'tooltip' } );
		for ( key in SlCompany.views )
		{
			if ( !SlCompany.views.hasOwnProperty( key ) )
				continue;

			day = key.replace( /views_/, '' );
			day = day.split( '_' );

			// Remove leading zero from day and month
			day[2] = parseInt( day[2] );
			day[1] = parseInt( day[1] );

			views = parseInt( SlCompany.views[key] );

			total += views;
			label = '';
			if ( j == i )
				label = day[2] + months[day[1]];
			j = 1 - j;
			rows.push( [label, views, views + " views\n" + day[2] + ' ' + months[day[1]] + ' ' + day[0]] );
		}

		document.getElementById( 'total-views' ).innerHTML = total;

		data.addRows( rows );
		chart.draw( data, options );
	}
})();
