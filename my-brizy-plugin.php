<?php
/**
 * Plugin Name: My Brizy Plugin
 * Description: Adds custom routes to return head and body HTML content for a given post ID.
 * Version: 1.0.0
 * Author: MeisamDev
 * License: GPL2
 */

 // Activation and deactivation hooks
register_activation_hook( __FILE__, 'my_brizy_plugin_activate' );
register_deactivation_hook( __FILE__, 'my_brizy_plugin_deactivate' );

/**
 * Plugin activation callback.
 * Adds rewrite rules and flushes rewrite rules.
 */
function my_brizy_plugin_activate() {
    my_brizy_plugin_add_rewrite_rules();
    flush_rewrite_rules();
}

/**
 * Plugin deactivation callback.
 * Flushes rewrite rules.
 */
function my_brizy_plugin_deactivate() {
    flush_rewrite_rules();
}

// Add rewrite rules on init
add_action( 'init', 'my_brizy_plugin_add_rewrite_rules' );

/**
 * Adds custom rewrite rules and tags.
 */
function my_brizy_plugin_add_rewrite_rules() {
    // Add rewrite rules for head and body routes
    add_rewrite_rule( '^brizy/head/([0-9]+)/?$', 'index.php?brizy_route=head&post_id=$matches[1]', 'top' );
    add_rewrite_rule( '^brizy/body/([0-9]+)/?$', 'index.php?brizy_route=body&post_id=$matches[1]', 'top' );
    
    // Add query vars
    add_rewrite_tag( '%brizy_route%', '([^&]+)' );
    add_rewrite_tag( '%post_id%', '([0-9]+)' );
}
