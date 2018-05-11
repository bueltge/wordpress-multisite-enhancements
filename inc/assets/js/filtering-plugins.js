// via https://github.com/charliepark/faq-patrol
// extend :contains to be case-insensitive; via http://stackoverflow.com/questions/187537/
jQuery( document ).ready( function( $ ) {
	'use strict';

	$.expr[ ':' ].contains = function( a, i, m ) {
		return (
				a.textContent || a.innerText || ''
			).toUpperCase().indexOf( m[ 3 ].toUpperCase() ) >= 0;
	};

	$( '#plugin-search-input' ).keyup( function() {
		var val = $( this ).val();
		var selector = '#the-list';
		if ( val.length < 2 ) {
			$( selector ).find( '> tr' ).show();
		} else {
			$( selector ).find( '> tr' ).hide();
			$( selector ).find( '.plugin-title strong:contains(' + val + ')' ).parent().parent().show();
		}
	} ).focus();
} );
