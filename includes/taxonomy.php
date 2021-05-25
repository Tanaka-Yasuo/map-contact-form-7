<?php

class MAP_WPCF7_Taxonomy {
	private static $instance;

	private function __construct() {
	}
	public static function get_instance() {
                if ( empty( self::$instance ) ) {
                        self::$instance = new self;
                }

                return self::$instance;
        }
	public static function get_name( $id, $tag ) {
		return ( $id . '-' . $tag[ 'name' ] . '-map-conatct-form-7' );
	}
	public static function resolve_name( $taxonomy ) {
		$matches = array();
		$pattern = "/\d+-([\w-]+)-map-conatct-form-7/";
		if ( preg_match( $pattern, $taxonomy, $matches ) == 1 ) {
			return $matches[ 1 ];
		}
		return $taxonomy;
	}
	public function register_taxonomies() {
		if ( !class_exists( 'WPCF7_FormTagsManager' ) ) {
			return;
		}
		$manager = WPCF7_FormTagsManager::get_instance();
		$options = MAP_WPCF7_Options::get_instance();

		$setting = $options->get_option();
		$form_ids = $setting[ MAP_WPCF7_Options::form_ids ];

		$contact_forms = WPCF7_ContactForm::find();
		foreach ( $contact_forms as $contact_form ) {
			if ( !MAP_WPCF7_ContactForm::in_form_ids(
				$form_ids,
				$contact_form ) ) {
				continue;
			}
			$content = $manager->normalize(
				$contact_form->prop( 'form' ) );
			$tags = $manager->scan( $content );
			$this->register_taxonomies_from_tags(
				$contact_form->id(), $tags );
		}
	}
	private function register_taxonomies_from_tags( $id, $tags ) {
		foreach ( $tags as $tag ) {
			switch ( $tag[ 'type' ] ) {
			case 'radio':
				break;
			default:
				continue 2;
			}
			if ( empty( $tag[ 'name' ] ) ) continue;
			
			$name = $this->get_name( $id , $tag );
			register_taxonomy(
				$name,
				MAP_WPCF7_Post::post_type,
				array(
					'hierarchical'      => false,
					'show_ui'           => false,
					'show_in_nav_menus' => false,
					'public'            => false,
					'label'             => $name
				)
			);
			foreach ( $tag[ 'raw_values' ] as $raw_value ) {
				wp_insert_term(
					$raw_value,
					$name );
			}
		}
	}
}

