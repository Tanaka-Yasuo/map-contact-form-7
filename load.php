<?php

require_once MAP_WPCF7_PLUGIN_DIR . '/includes/capabilities.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/functions.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/contact-form.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/post.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/taxonomy.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/options.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/shortcode.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/rest-api.php';
require_once MAP_WPCF7_PLUGIN_DIR . '/includes/i10n.php';

if ( is_admin() ) {
	require_once MAP_WPCF7_PLUGIN_DIR . '/admin/admin.php';
} else {
        require_once MAP_WPCF7_PLUGIN_DIR . '/includes/controller.php';
}

class MAP_WPCF7 {
	public static function load_modules() {
		self::load_module( 'place' );
	}
	protected static function load_module( $mod ) {
                $dir = MAP_WPCF7_PLUGIN_MODULES_DIR;

                if ( empty( $dir ) or ! is_dir( $dir ) ) {
                        return false;
                }

                $files = array(
                        path_join( $dir, $mod . '/' . $mod . '.php' ),
                        path_join( $dir, $mod . '.php' ),
                );

                foreach ( $files as $file ) {
                        if ( file_exists( $file ) ) {
                                include_once $file;
                                return true;
                        }
                }

                return false;
        }
}

add_action( 'plugins_loaded', function() {
        MAP_WPCF7::load_modules();

        /* Shortcodes */
	add_shortcode( 'map-contact-form-7', function( $atts, $content = null, $code = '' ) {
		$shortcode = new MAP_WPCF7_Shortcode( $atts, $content, $code );
		return $shortcode->html();
	} );
	MAP_WPCF7_Shortcode::add_action();
	map_wpcf7_load_textdomain();
}, 11, 0 );

add_action( 'init', function() {
	$post = MAP_WPCF7_Post::register_post_type();
	$taxonomy = MAP_WPCF7_Taxonomy::get_instance();
	$taxonomy->register_taxonomies();
	MAP_WPCF7_ContactForm::add_action();
/*
        wpcf7_get_request_uri();
        wpcf7_register_post_types();

        do_action( 'wpcf7_init' );
*/
}, 10, 0 );

register_activation_hook(
        __FILE__,
        function () {
                register_uninstall_hook(
                        __FILE__,
			function() {
				$options = MAP_WPCF7_Options::get_instance();
				$options->delete_option();
			} );
	} );

add_filter( 'pre_update_option_active_plugins', function( $active_plugins, $old_value ) {
	$map_contact_form_7_dirname = basename( dirname( __FILE__ ) );
	$contact_form_7_index = -1;
	$map_contact_form_7_index = -1;

	foreach ( $active_plugins as $index => $path ) {
		$dirname = dirname( $path );
		if ( $dirname == 'contact-form-7' ) {
			$contact_form_7_index = $index;
		}
		if ( $dirname == $map_contact_form_7_dirname ) {
			$map_contact_form_7_index = $index;
			$map_contact_form_7_path = $path;
		}
	}
	if ( $map_contact_form_7_index >= 0 && $contact_form_7_index >= 0 ) {
		if ( $map_contact_form_7_index < $contact_form_7_index ) {
		     	array_splice( $active_plugins, $map_contact_form_7_index, 1 );
			$active_plugins[] = $map_contact_form_7_path;
		}
	}
	return $active_plugins;
}, 10, 2 );
