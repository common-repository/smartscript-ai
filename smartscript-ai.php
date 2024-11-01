<?php
/*
Plugin Name: SmartScript AI
Plugin URI: https://uxandme.com/smartscript-ai
Description: Generates AI-powered content within WordPress.
Version: 1.0
Author: Gia Romano
Author URI: https://uxandme.com
Text Domain: smartscript-ai
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include necessary files
include_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/meta-box.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/ai-request.php';

// Enqueue scripts and styles
function ssai_enqueue_scripts( $hook ) {
    if ( $hook != 'post.php' && $hook != 'post-new.php' ) {
        return;
    }
    wp_enqueue_script( 'ssai-script', plugin_dir_url( __FILE__ ) . 'assets/js/smartscript-ai-script.js', array( 'jquery' ), '1.1', true );
    wp_enqueue_style( 'ssai-style', plugin_dir_url( __FILE__ ) . 'assets/css/smartscript-ai-style.css', array(), '1.0' );

    // Localize script to pass AJAX URL and nonce
    wp_localize_script( 'ssai-script', 'ssai_ajax_object', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'ssai_nonce' => wp_create_nonce( 'ssai_nonce_action' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'ssai_enqueue_scripts' );