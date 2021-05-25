<p>
	<label class="map-conatct-form-7-label">
 		<?php _e( 'Candidate Forms', 'map-contact-form-7' ); ?>
	</label>
	<br/>
	<select id="map-conatct-form-7-forms">
	</select>
	<br/>
	<button id="map-conatct-form-7-form-ids-add">
 	<?php _e( 'add', 'map-contact-form-7' ); ?>
	</button>
</p>
<p>
	<label class="map-conatct-form-7-label">
 		<?php _e( 'Target Forms', 'map-contact-form-7' ); ?>
	</label>
	<div id="map-conatct-form-7-form-ids">
	</div>
</p>
<script type="text/javascript">
    jQuery( function( $ ) {
	    function getSelectableForms( forms, formIds ) {
		var results = [];

		forms.forEach( function( form ) {
		    for ( var formId of formIds ) {
			if ( formId == form.id ) return;
		    }
		    results.push( form );
		} );
		return results;
	    }
	    function getPostForms( forms, formIds ) {
		var results = [];

		formIds.forEach( function( formId ) {
		    for ( var form of forms ) {
			if ( formId == form.id ) {
			    results.push( form );
			    return;
			}
		    }
		} );
		return results;
	    }
	    function resetPosts() {
	    	var selectableForms = getSelectableForms( forms, formIds );
		var selected = 'selected';
		var html = '';

		selectableForms.forEach( function( form ) {
		     html += '<option value="' + form.id + '" ' + selected + '>' + form.name + '</option>';
                    selected = '';

		} );
	    	$( '#map-conatct-form-7-forms' ).html( html );

	    	var postForms = getPostForms( forms, formIds );

		html = '';
		postForms.forEach( function( form ) {
		     html += '<label>' + form.name + '</label>';
                     html += '<button class="map-conatct-form-7-post-remove" style="margin: 0.5em;">';
		     html += "<?php _e( 'remove', 'map-contact-form-7' ); ?>";
                     html += '</button>';
		     html += '<input type="hidden" value="' + form.id + '" name="<?php echo self::option_name; ?>[<?php echo self::form_ids; ?>][]" >';
                     html += '</input>';
                     html += '<br/>';
		} );
	    	$( '#map-conatct-form-7-form-ids' ).html( html );
		$( 'button.map-conatct-form-7-post-remove' ).on( 'click', function(e) {
		    var val = $( e.target ).nextAll( 'input' ).val();

console.log( 'remove' );
		    formIds.splice(formIds.indexOf( val ), 1);
		    resetPosts();
		    e.preventDefault();
		} );
	    }
	    var forms = <?php echo json_encode( $forms, JSON_UNESCAPED_UNICODE ) ?>;
	    var formIds = <?php echo json_encode( $form_ids, JSON_UNESCAPED_UNICODE ) ?>;

	    forms.forEach( function( form ) {
		    form.name = decodeURIComponent( form.name );
	    } );
	    resetPosts();
	    $( '#map-conatct-form-7-form-ids-add' ).on( 'click', function(e) {
		    var val = $("#map-conatct-form-7-forms").val();

		    formIds.push( val );
		    resetPosts();
		    e.preventDefault();
	    } );
    } );
</script>
