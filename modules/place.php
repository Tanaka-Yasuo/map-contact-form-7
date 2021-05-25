<?php
/**
** A base module for [place]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'map_wpcf7_add_form_tag_place', 10, 0 );

function map_wpcf7_add_form_tag_place() {
	wpcf7_add_form_tag( array( 'place', 'place*' ),
		'map_wpcf7_place_form_tag_handler',
		array(
			'name-attr' => true,
			'selectable-values' => true,
			'multiple-controls-container' => true,
		)
	);
	add_action( 'wp_ajax_textsearch', array( 'MAP_WPCF7_Rest', 'textsearch' ) );
	add_action( 'wp_ajax_nopriv_textsearch', array( 'MAP_WPCF7_Rest', 'textsearch' ) );
}


function map_wpcf7_place_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
	wp_enqueue_script( 'map-contact-form-7-place' );

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$reserved_query = '';

	if ( count( $tag->values ) != 0 ) {
		$reserved_query = $tag->values[ 0 ];
	}

/*
	$label_first = $tag->has_option( 'label_first' );
	$use_label_element = $tag->has_option( 'use_label_element' );
	$exclusive = $tag->has_option( 'exclusive' );
	$free_text = $tag->has_option( 'free_text' );
	$multiple = false;

	if ( 'checkbox' == $tag->basetype ) {
		$multiple = ! $exclusive;
	} else { // radio
		$exclusive = false;
	}
*/
	$class .= ' map-wpcf7-place';

/*
	if ( $exclusive ) {
		$class .= ' wpcf7-exclusive-checkbox';
	}
*/

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	if ( $validation_error ) {
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	}

/*
	$tabindex = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( false !== $tabindex ) {
		$tabindex = (int) $tabindex;
	}
*/

	$html = '';

/*
	$count = 0;

	if ( $data = (array) $tag->get_data_option() ) {
		if ( $free_text ) {
			$tag->values = array_merge(
				array_slice( $tag->values, 0, -1 ),
				array_values( $data ),
				array_slice( $tag->values, -1 ) );
			$tag->labels = array_merge(
				array_slice( $tag->labels, 0, -1 ),
				array_values( $data ),
				array_slice( $tag->labels, -1 ) );
		} else {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}
	}

	$values = $tag->values;
	$labels = $tag->labels;

	$default_choice = $tag->get_default_option( null, array(
		'multiple' => $multiple,
	) );

	$hangover = wpcf7_get_hangover( $tag->name, $multiple ? array() : '' );

	foreach ( $values as $key => $value ) {
		if ( $hangover ) {
			$checked = in_array( $value, (array) $hangover, true );
		} else {
			$checked = in_array( $value, (array) $default_choice, true );
		}

		if ( isset( $labels[$key] ) ) {
			$label = $labels[$key];
		} else {
			$label = $value;
		}

		$item_atts = array(
			'type' => $tag->basetype,
			'name' => $tag->name . ( $multiple ? '[]' : '' ),
			'value' => $value,
			'checked' => $checked ? 'checked' : '',
			'tabindex' => false !== $tabindex ? $tabindex : '',
		);

		$item_atts = wpcf7_format_atts( $item_atts );

		if ( $label_first ) { // put label first, input last
			$item = sprintf(
				'<span class="wpcf7-list-item-label">%1$s</span><input %2$s />',
				esc_html( $label ), $item_atts
			);
		} else {
			$item = sprintf(
				'<input %2$s /><span class="wpcf7-list-item-label">%1$s</span>',
				esc_html( $label ), $item_atts
			);
		}

		if ( $use_label_element ) {
			$item = '<label>' . $item . '</label>';
		}

		if ( false !== $tabindex
		and 0 < $tabindex ) {
			$tabindex += 1;
		}

		$class = 'wpcf7-list-item';
		$count += 1;

		if ( 1 == $count ) {
			$class .= ' first';
		}

		if ( count( $values ) == $count ) { // last round
			$class .= ' last';

			if ( $free_text ) {
				$free_text_name = $tag->name . '_free_text';

				$free_text_atts = array(
					'name' => $free_text_name,
					'class' => 'wpcf7-free-text',
					'tabindex' => false !== $tabindex ? $tabindex : '',
				);

				if ( wpcf7_is_posted()
				and isset( $_POST[$free_text_name] ) ) {
					$free_text_atts['value'] = wp_unslash(
						$_POST[$free_text_name] );
				}

				$free_text_atts = wpcf7_format_atts( $free_text_atts );

				$item .= sprintf( ' <input type="text" %s />', $free_text_atts );

				$class .= ' has-free-text';
			}
		}

		$item = '<span class="' . esc_attr( $class ) . '">' . $item . '</span>';
		$html .= $item;
	}
*/
	$html = '<input class="' . esc_attr( $class ) . '" placeholder="' . esc_html( __( 'Input keywords', 'map-contact-form-7' ) ) . '" data-reserved-query="' . $reserved_query . '">';
	$html .= '<br/>';

	$atts = array();
        $atts['class'] = esc_attr( $class );	
        $atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html .= sprintf( '<select %1$s>', $atts );
	$html .= '</select>';

	$html .= '<script>';
	$html .= 'var placeAjaxUrl = \'${admin_url( \'admin-ajax.php\')};';
	$html .= '</script>';
	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error
	);

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_place',
	'map_wpcf7_place_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_place*',
	'map_wpcf7_place_validation_filter', 10, 2 );

function map_wpcf7_place_validation_filter( $result, $tag ) {
	$name = $tag->name;
	$is_required = $tag->is_required() || 'place' == $tag->type;
	$value = isset( $_POST[$name] ) ? (array) $_POST[$name] : array();

	if ( $is_required and empty( $value ) ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}


/* Tag generator */

add_action( 'wpcf7_admin_init',
	'map_wpcf7_add_tag_generator_place', 30, 0 );

function map_wpcf7_add_tag_generator_place() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'place', __( 'place', 'map-contact-form-7' ),
		'map_wpcf7_tag_generator_place' );
}

function map_wpcf7_tag_generator_place( $contact_form, $args = '' ) {
	$description = __( "Generate a form-tag for a place.", 'map-contact-form-7' );
/*
	$desc_link = wpcf7_link( __( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', 'contact-form-7' ), __( 'Checkboxes, radio buttons and menus', 'contact-form-7' ) );
*/

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ) ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><?php echo esc_html( __( 'Reserved Words', 'map-contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Reserved Words', 'map-contact-form-7' ) ); ?></legend>
		<input name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-reserved-query' ); ?>"></input>
		</fieldset>
	</td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="place" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

</div>
<?php
}
