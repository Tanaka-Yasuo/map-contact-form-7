jQuery( function( $ ) {
	$( 'input.map-wpcf7-place' ).each( function( index, element ) {
		var timeoutId;
		
		$( element ).on( 'keyup', function() {
			if ( timeoutId ) {
				clearTimeout( timeoutId );
			}
			timeoutId = undefined;

			var select = $( element ).nextAll( 'select' );

			select.prop( 'disabled', true );
			select.html( '' );
			timeoutId = setTimeout( function() {
				var query = $( element ).val();

				query += ' ' + $( element ).attr( 'data-reserved-query' );

				var jqXHR = $.ajax({
					type: 'GET',
					url: mapContactForm7Ajax.url,
					dataType: 'json',
					data: {
						action: 'textsearch',
						query: query,
					},
				})
				.done( function( data, textStatus, jqXHR ) {
					var html = '';
					var selected = 'selected';

					for ( key in data ) {
						var facility = data[ key ];

                    				html += '<option value="' + facility.place_id + ',' + encodeURIComponent( facility.name )+ ',' + facility.geometry.location.lat + ',' + facility.geometry.location.lng + '" ' + selected + '>' + facility.name + '</option>';
                    				selected = '';
					}
                  			select.html( html );
                  			select.prop( 'disabled', false );
				})
				.fail( function( jqXHR, textStatus, errorThrown ) {
                  			select.next( 'select' ).prop( 'disabled', false );
				});
			},
			2 * 1000 );
		} );
	} );
} );

