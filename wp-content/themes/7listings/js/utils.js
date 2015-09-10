var rw_utils = {
	// Validate email address
	is_email: function( email )
	{
 		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

		return reg.test( email ) != false;
	},

	// Get credit card type
	get_cc_type: function( number )
	{
		var result = 'unknown';

		// MasterCard
		if ( /^5[1-5]/.test( number ) )
			result = 'mastercard';
		// Visa
		else if ( /^4/.test( number ) )
			result = 'visa';

		// AmEx
		else if ( /^3[47]/.test( number ) )
			result = 'amex';

		return result;
	},

	is_mobile: function()
	{
		return navigator.userAgent.match( /(iPhone|iPod|iPad|Android|BlackBerry)/i );
	}
};

var rw_date = {
	// Get date object from a string
	get_obj: function( d )
	{
		d = d.split( '/' );
		d = new Date( d[2], parseInt( d[1] ) - 1, d[0] );
		return d;
	},

	// Convert a date string from European to USA format
	toUSA: function( d )
	{
		var dmy = d.split('/');

		return dmy[1] + '/' + dmy[0] + '/' + dmy[2];
	},

	obj_to_euro: function( d )
	{
		return ( '0' + d.getDate() ).slice( -2 ) + '/' + ( '0' + ( d.getMonth() + 1 ) ).slice( -2 ) + '/' + d.getFullYear();
	},

	// Get number of different days
	days_diff: function( d1, d2 )
	{
		d1 = new Date( d1 );
		d2 = new Date( d2 );

		return Math.ceil( ( d2 - d1 ) / 86400000 );
	}
};
