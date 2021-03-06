<?php
/**
 * Plugin name: WordPress.org Code Analysis
 * Description: Tools for analyzing plugin and theme code.
 * Version:     0.1
 * Author:      WordPress.org
 * Author URI:  http://wordpress.org/
 * License:     GPLv2 or later
 */

namespace WordPressDotOrg\Code_Analysis;

defined( 'WPINC' ) || die();

define( __NAMESPACE__ . '\PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_URL', plugins_url( '/', __FILE__ ) );

/**
 * Actions and filters.
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_files' );

/**
 * Load the other PHP files for the plugin.
 *
 * @return void
 */
function load_files() {
	require PLUGIN_DIR . 'includes/class-phpcs.php';
	require PLUGIN_DIR . 'admin/class-scan-metabox.php';

}

function register_admin_metabox( $post_type, $post ) {
    if ( 'plugin' !== $post_type ) {
        return;
	}
	
	// Only load the metabox if the plugin directory plugin is active
	if ( !class_exists( '\WordPressDotOrg\Plugin_Directory\Plugin_Directory' ) ) {
		return;
	}

    add_meta_box(
        'code-scanner',
        __( 'Code Scanner', 'wporg-plugins' ),
        array( __NAMESPACE__ . '\Admin\Scan_Metabox', 'display_ajax' ),
        'plugin', 'normal', 'high'
    );

    wp_enqueue_script( 'code-scan-metabox-js', plugins_url( 'admin/metabox.js', __FILE__ ), array( 'wp-util' ), 1 );
}

add_action( 'add_meta_boxes', __NAMESPACE__ . '\register_admin_metabox', 10, 2 );
add_filter( 'wp_ajax_scan-plugin', array( __NAMESPACE__ . '\Admin\Scan_Metabox', 'get_ajax_response' ) );
