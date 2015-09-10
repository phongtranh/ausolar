google.load( 'visualization', '1.0', { 'packages': ['corechart'] } );
google.setOnLoadCallback( solar_draw_chart_states );

function solar_draw_chart_states()
{
	var data = new google.visualization.DataTable();
	data.addColumn( 'string', 'State' );
	data.addColumn( 'number', 'Revenue' );
	data.addColumn( {type: 'string', role: 'tooltip'} );

	data.addRows( window.state_pie_chart );

	var formatter = new google.visualization.NumberFormat( {prefix: '$'} );
	formatter.format( data, 1 ); // Apply formatter to second column

	var options = {
		slices         : {
			0: { color: '#800000' },
			1: { color: '#000080' },
			2: { color: '#7ec0ee' },
			3: { color: '#000' },
			4: { color: '#f00' },
			5: { color: '#fcd116' },
			6: { color: '#106604' },
			7: { color: '#f5c52c' }
		},
		pieHole        : 0.4,
		backgroundColor: 'transparent'
	};

	var chart = new google.visualization.PieChart( document.getElementById( 'chart-states' ) );
	chart.draw( data, options );
}

jQuery('#ajax-load').hide();