<?php
/**
 * Plugin Name: contact-7-formにmap情報を追加
 * Plugin URI: https://localohost/
 * Description: make places of contace-7-form
 * Version: 1.0.0
 * Author: YasuoTanaka
 * Author URI: https://crowdworks.jp/
 * Text Domain: map-contact-form-7
 * Domain Path: /languages/
 */
defined( 'ABSPATH' ) || exit;

define( 'MAP_WPCF7_VERSION', '0.0.1' );

define( 'MAP_WPCF7_TEXT_DOMAIN', 'map-contact-form-7' );

define( 'MAP_WPCF7_PLUGIN', __FILE__ );

define( 'MAP_WPCF7_PLUGIN_BASENAME', plugin_basename( MAP_WPCF7_PLUGIN ) );

define( 'MAP_WPCF7_PLUGIN_NAME', trim( dirname( MAP_WPCF7_PLUGIN_BASENAME ), '/' ) );

define( 'MAP_WPCF7_PLUGIN_DIR', untrailingslashit( dirname( MAP_WPCF7_PLUGIN ) ) );

define( 'MAP_WPCF7_PLUGIN_MODULES_DIR', MAP_WPCF7_PLUGIN_DIR . '/modules' );

if ( ! defined( 'MAP_WPCF7_ADMIN_READ_CAPABILITY' ) ) {
        define( 'MAP_WPCF7_ADMIN_READ_CAPABILITY', 'edit_posts' );
}

if ( ! defined( 'MAP_WPCF7_ADMIN_READ_WRITE_CAPABILITY' ) ) {
        define( 'MAP_WPCF7_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );
}

require_once MAP_WPCF7_PLUGIN_DIR . '/load.php';

