<?php

class MAP_WPCF7_Post {
	const post_type = 'map-conatct-form-7';
	const meta_key_form_id = 'form_id';
	const meta_key_place_id = 'place_id';
	const meta_key_place_name = 'name';
	const meta_key_place_lat = 'lat';
	const meta_key_place_lng = 'lng';
	const meta_key_place = 'place';

	private $post;

	private function __construct( $post ) {
		$this->post = $post;
	}
	public static function register_post_type() {
		register_post_type(
			self::post_type,
			array(
				'label' => 'map-conatct-form-7',
				'public' => false,
			)
		);
	}
	public static function insert_post( $conatct_form, $place, $posted_data, $taxonomies ) {
		$post_id = wp_insert_post(
                        array(
                                'post_content' => json_encode( $posted_data, JSON_UNESCAPED_UNICODE ),
                                'post_type' => 'map-conatct-form-7'
                        )
                );
                foreach ( $taxonomies as $taxonomy ) {
                        $result = wp_set_post_terms(
                                $post_id,
                                $taxonomy[ 'terms' ],
                                $taxonomy[ 'name' ] );
                }
                update_post_meta(
                        $post_id,
                        self::meta_key_form_id,
                        $conatct_form->id() );
                update_post_meta(
                        $post_id,
                        self::meta_key_place_id,
                        $place[ 0 ] );
                update_post_meta(
                        $post_id,
                        self::meta_key_place_name,
                        $place[ 1 ] );
                update_post_meta(
                        $post_id,
                        self::meta_key_place_lat,
                        $place[ 2 ] );
                update_post_meta(
                        $post_id,
                        self::meta_key_place_lng,
                        $place[ 3 ] );
	}
	public static function find( $args = '' ) {
		$defaults = array(
                        'post_status' => 'any',
                        'posts_per_page' => -1,
                        'offset' => 0,
                        'orderby' => 'ID',
                        'order' => 'ASC',
                );

                $args = wp_parse_args( $args, $defaults );

                $args['post_type'] = self::post_type;

                $q = new WP_Query();

		$objs = array();

		foreach ( $q->query( $args ) as $post ) {
			$objs[] = new self( $post );
		}

		return $objs;
	}
	public static function get_object_taxonomies( $output = 'names' ) {
		return get_object_taxonomies( self::post_type, $output );
	}
	public function post() {
		return $this->post;
	}
	public function id() {
		return $this->post->ID;
	}
	public function post_content() {
		return $this->post->post_content;
	}
}

