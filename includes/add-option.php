<script type="text/javascript">
jQuery( function( $ ) {
    $( '.map-contact-form-7-add-option' ).each( function( index, element ) {
	var button = $( element ).next( 'button' );
	
	$( button ).on( 'click', function( e ) {
	    var select = $( element ).prev( 'select' );
	
	    $( select ).append( '<option value="' + $( element ).val() + '">' + $( element ).val() + '</option>' );
	} );
    } );
} );
</script>
