// via https://github.com/charliepark/faq-patrol
// extend :contains to be case-insensitive; via http://stackoverflow.com/questions/187537/
jQuery.expr[ ':' ].contains = function( a, i, m ) {
	return (
		a.textContent || a.innerText || ""
		).toUpperCase().indexOf( m[ 3 ].toUpperCase() ) >= 0;
};
jQuery( document ).ready( function( $ ) {
	$( '#plugin-search-input' ).keyup( function() {
		$val = $( this ).val();
		if ( $val.length < 2 ) {
			$( "#the-list").find("> tr" ).show();
		} else {
			$( "#the-list").find("> tr" ).hide();
			$( "#the-list").find(".plugin-title strong:contains(" + $val + ")" ).parent().parent().show();
		}
	} ).focus();
} );
