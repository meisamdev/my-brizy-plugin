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
// Handle the custom routes
add_action( 'template_redirect', 'my_brizy_plugin_template_redirect' );

/**
 * Intercepts requests to custom routes and outputs the required HTML content.
 */
function my_brizy_plugin_template_redirect() {
    $route = get_query_var( 'brizy_route' );
    $post_id = get_query_var( 'post_id' );
    
    if ( $route && $post_id ) {
        // Ensure Brizy plugin is active
        if ( ! class_exists( 'Brizy_Editor_Post' ) ) {
            wp_die( 'Brizy plugin is not active.' );
        }
        
        // Get the Brizy post
        $post = Brizy_Editor_Post::get( $post_id );
        $html = new Brizy_Editor_CompiledHtml( $post->get_compiled_html() );
        
        if ( $route == 'head' ) {
            // Get the <head> content
            $headHtml = apply_filters( 'brizy_content', $html->get_head(), Brizy_Editor_Project::get(), $post->getWpPost() );
            // Output the HTML content
            header( 'Content-Type: text/html' );
            echo $headHtml;
            exit;
        } elseif ( $route == 'body' ) {
            // Get the <body> content
            $bodyHtml = apply_filters( 'brizy_content', $html->get_body(), Brizy_Editor_Project::get(), $post->getWpPost() );
            // Output the HTML content
            header( 'Content-Type: text/html' );
            echo $bodyHtml;
            exit;
        }
    }
}
