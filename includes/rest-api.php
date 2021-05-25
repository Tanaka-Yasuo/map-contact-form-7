<?php
class MAP_WPCF7_Rest {
	const min_lat = 0.1;
	const min_lng = 0.1;

	public static function textsearch(){
                $query = rawurlencode( $_GET[ 'query' ] );

                $options = MAP_WPCF7_Options::get_instance();
                $setting = $options->get_option();
                $API_KEY = $setting[ MAP_WPCF7_Options::api_key ];
                $language = $setting[ MAP_WPCF7_Options::language ];
                $region = $setting[ MAP_WPCF7_Options::region ];

                if ( $API_KEY === '' ) {
                        wp_send_json( array() );
                        return;
                }
		$referer = get_home_url();
		$context = stream_context_create(
			 array(
				'http' => array(
					'header' => array(
						"Referer: $referer\r\n" ) )
			)
		);
                $placeURL = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=${query}&language=${language}&region=${region}&fields=name,formatted_address,place_id&key=${API_KEY}";
                $response = file_get_contents( $placeURL, false, $context );

                if ( $response ) {
                        $content = json_decode(
                                $response,
                                true );
                        if ( $content[ 'status' ] == 'OK' ) {
                                wp_send_json( $content[ 'results' ] );
                                return;
                        }
                }
                wp_send_json( array() );
                return;
        }
	public static function getmarkerinfos() {
		$arg = json_decode( 
			stripslashes ( rawurldecode( $_GET[ 'query' ] ) )
		);
		$bounds = $arg->bounds;
		$form_id = $arg->form_id;
		$form = $arg->form;

		$contact_forms = WPCF7_ContactForm::find( array(
			'p' => $form_id,
		) );
                $contact_form = $contact_forms[ 0 ];
		$bounds_array = self::divide_bounds( $bounds );

		$posts = self::get_posts(
			$bounds,
			$form_id,
			$form,
			$bounds_array );
		$lat_lng = self::get_lat_lng( $bounds_array );

		$taxonomies = MAP_WPCF7_ContactForm::get_taxonomies(
			$contact_form );
		$key_values = array();
		if ( $lat_lng[ 'lat' ] >= self::min_lat
			&& $lat_lng[ 'lng' ] >= self::min_lng ) {
			$key_values = self::aggregate_posts_by_lat_lng(
				$key_values,
				$posts,
				$taxonomies,
				$lat_lng );
		} else {
			$key_values = self::aggregate_posts(
				$key_values,
				$posts,
				$taxonomies );
		}
		$markerInfos = [];
		foreach ( $key_values as $key => $value ) {
			$markerInfos[] = $value;
		}
		wp_send_json( $markerInfos );
                return;
	}
	public static function getrank() {
		$arg = json_decode( 
			stripslashes ( rawurldecode( $_GET[ 'query' ] ) )
		);
		$bounds = $arg->bounds;
		$form_id = $arg->form_id;
		$form = $arg->form;

		$contact_forms = WPCF7_ContactForm::find( array(
			'p' => $form_id,
		) );
                $contact_form = $contact_forms[ 0 ];
		$bounds_array = self::divide_bounds( $bounds );

		$posts = self::get_posts(
			$bounds,
			$form_id,
			$form,
			$bounds_array );

		$taxonomies = MAP_WPCF7_ContactForm::get_taxonomies(
			$contact_form );
		$key_values = array();
		$key_values = self::aggregate_posts(
			$key_values,
			$posts,
			$taxonomies );
		$markerInfos = [];
		foreach ( $key_values as $key => $value ) {
			$markerInfos[] = $value;
		}
		usort( $markerInfos, function( $a, $b ) {
			if ( $a[ 'count' ] < $b[ 'count' ] ) {
                        	return 1;
                    	} else if ( $a[ 'count' ] > $b[ 'count' ] ) {
                        	return -1;
                    	}
                    	return 0;
		} );
		$options = MAP_WPCF7_Options::get_instance();
		$setting = $options->get_option();
		$num_ranks = $setting[ MAP_WPCF7_Options::num_ranks ];
		if ( $num_ranks < 0 ) $num_ranks = 0;
	
		$markerInfos = array_slice( $markerInfos, 0, $num_ranks );
		wp_send_json( $markerInfos );
                return;
	}
	public static function get_posts( $bounds, $form_id, $form, $bounds_array ) {
		$tax_query = [];
		foreach ( $form as $name_value ) {
		     $tax_query = self::add_taxonomy_term( 
			$tax_query,
			$name_value->name,
			$name_value->value );
		}
		if ( count( $tax_query ) > 1 ) {
			$tax_query[ 'relation' ] = 'AND';
		}
		$meta_query = self::get_meta_query( $form_id, $bounds_array );
		$args = array(
			'tax_query' => $tax_query,
			'meta_query' => $meta_query,
			'orderby' => array(
				MAP_WPCF7_Post::meta_key_place_id => 'ASC',
				MAP_WPCF7_Post::meta_key_place_lng => 'ASC',
			),
		);
		return MAP_WPCF7_Post::find( $args );
	}
	private static function get_lat_lng( $bounds_array ) {
		$lat = $lng = 0;
		foreach ( $bounds_array as $bounds ) {
			$lat += $bounds->north - $bounds->south;
			$lng += $bounds->east - $bounds->west;
		}
		return array(
			'lat' => round( $lat / 10, self::min_lat ),
			'lng' => round( $lng / 10, self::min_lng ) );
	}
	private static function aggregate_posts_by_lat_lng( $key_values, $posts, $taxonomies, $lat_lng ) {
		foreach ( $posts as $post ) {
			$lat = get_post_meta(
				$post->ID(),
				MAP_WPCF7_Post::meta_key_place_lat,
				true );
			$lng = get_post_meta(
				$post->ID(),
				MAP_WPCF7_Post::meta_key_place_lng,
				true );
			$bounds = self::get_bounds( $lat, $lng, $lat_lng );
			$key_values = self::insert_post_by_bounds(
				$key_values,
				$post,
				$taxonomies,
				$bounds );
		}
		return $key_values;
	}
	private static function aggregate_posts( $key_values, $posts, $taxonomies ) {
		foreach ( $posts as $post ) {
			$lat = get_post_meta(
				$post->ID(),
				MAP_WPCF7_Post::meta_key_place_lat,
				true );
			$lng = get_post_meta(
				$post->ID(),
				MAP_WPCF7_Post::meta_key_place_lng,
				true );
			$key_values = self::insert_post(
				$key_values,
				$post,
				$taxonomies,
				$lat,
				$lng );
		}
		return $key_values;
	}
	private static function get_bounds( $lat, $lng, $lat_lng ) {
		return array( 
		    'south' => round(
			 $lat - $lat_lng[ 'lat' ] / 2,
			 $lat_lng[ 'lat' ] ),
		    'north' => round(
			 $lat + $lat_lng[ 'lat' ] / 2
			, $lat_lng[ 'lat' ] ),
		    'west' => round(
			 $lng - $lat_lng[ 'lng' ] / 2,
			 $lat_lng[ 'lng' ] ),
		    'east' => round(
			 $lng + $lat_lng[ 'lng' ] / 2,
			 $lat_lng[ 'lng' ] ),
		);
	}
	private static function insert_post_by_bounds( $key_values, $post, $taxonomies, $bounds ) {
		$key = $bounds[ 'south' ] . ',' . $bounds[ 'north' ] . 'x'
			. $bounds[ 'west' ] . ',' . $bounds[ 'east' ];
		if ( array_key_exists( $key, $key_values ) ) {
			$value = $key_values[ $key ];
		} else {
			$value = array(
				'lat' => ( $bounds[ 'south' ]
					+ $bounds[ 'north' ] ) / 2, 
				'lng' => ( $bounds[ 'west' ]
					+ $bounds[ 'east' ] ) / 2, 
				'name' => '',
				'count' => 0,
				'taxonomies' => array(),
			);
		}
		$key_values[ $key ] = self::statistics(
			$value,
			$post,
			$taxonomies );
		return $key_values;
	}

	private static function insert_post( $key_values, $post, $taxonomies, $lat, $lng ) {
		$key = get_post_meta(
			$post->ID(),
			MAP_WPCF7_Post::meta_key_place_id,
			true );
		$name = get_post_meta(
			$post->ID(),
			MAP_WPCF7_Post::meta_key_place_name,
			true );
		if ( array_key_exists( $key, $key_values ) ) {
			$value = $key_values[ $key ];
		} else {
			$value = array(
				'lat' => $lat,
				'lng' => $lng,
				'name' => rawurldecode( $name ),
				'count' => 0,
				'taxonomies' => array(),
			);
		}
		$key_values[ $key ] = self::statistics(
			$value,
			$post,
			$taxonomies );
		return $key_values;
	}
	private static function statistics( $value, $post, $taxonomies ) {
		$value[ 'count' ] += 1;
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $post->ID(), $taxonomy ); 
			$name = MAP_WPCF7_Taxonomy::resolve_name( $taxonomy );
			if ( !empty( $terms ) ) {
				if ( array_key_exists(
					$name,
					$value[ 'taxonomies' ] ) ) {
					$tax = $value[ 'taxonomies' ][ $name ];
				} else {
					$tax = array();
				}
				foreach ( $terms as $term ) {
					if  ( !array_key_exists(
						$term->name, $tax ) ) {
						$tax[ $term->name ] = 0;
					}
					$tax[ $term->name ] += 1;
				}
				$value[ 'taxonomies' ][ $name ] = $tax;
			}
		}
		return $value;
	}
	private static function divide_bounds( $bounds ) {
		if ( $bounds->west < $bounds->west ) {
			return array(
				array( 
					'south' => $bounds->south,
					'west' => 0,
					'north' => $bounds->north,
					'east' => $bounds->east,
				),
				array(
					'south' => $bounds->south,
					'west' => $bounds->west,
					'north' => $bounds->north,
					'east' => 0,
				),
			);
	     	}
	     	return array( $bounds );
	}
	private static function add_taxonomy_term( $tax_query, $name, $term ) {
		foreach ( $tax_query as &$query ) {
		    	if ( $query[ 'taxonomy' ] == $name ) {
				$query[ 'terms' ][] = $term;
				return $tax_query;
		    	}
		}
		$tax_query[] = array(
			'taxonomy' => $name,
			'field' => 'name',
			'terms' => array( $term ),
		);
		return $tax_query;
	}
	private static function get_meta_query( $form_id, $bounds_array ) {
		$meta_query = array();
		foreach ( $bounds_array as $bounds ) {
			$meta_query[] = array(
				'relation' => 'AND',
				array(
					'key' => MAP_WPCF7_Post::meta_key_form_id,
                                	'value' => $form_id,
				),
				array(
					'key' => MAP_WPCF7_Post::meta_key_place_lat,
					'value' => array(
						$bounds->south,
						$bounds->north ),
					'compare' => 'BETWEEN',
				),
				array(
					'key' => MAP_WPCF7_Post::meta_key_place_lng,
					'value' => array(
						$bounds->west,
						$bounds->east ),
					'compare' => 'BETWEEN',
				),
			);
		}
		if ( count( $meta_query ) > 1 ) {
			$meta_query[ 'relation' ] = 'OR';
		}
		return $meta_query;
	}
}
