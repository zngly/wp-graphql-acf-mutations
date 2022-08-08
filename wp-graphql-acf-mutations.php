<?php

/**
 * Plugin Name:       WPGraphQL ACF Mutations
 * Description:       Adds Advanced Custom Fields Mutations to the WPGraphQL Schema
 * Author:            Vlad-Anton Medves
 * Text Domain:       wp-graphql-acf
 * Version:           1.0.5
 * Requires PHP:      7.0
 *
 * @package         WPGraphQL_ACF_Mutations
 */

namespace Zngly\ACFM;

if (!defined('ABSPATH')) {
    exit;
}

// if namespace Zngly\ACFM is not defined
if (!class_exists('Zngly\\ACFM\\Mutations')) {
    // require the autoloader in wordpress/vendor/autoload.php
    // if the autoloader is not found, exit with an error message
    if (!file_exists(__DIR__ . '/wordpress/vendor/autoload.php')) {
        wp_die(
            "
            <samp>
                Error: Could not load WPGraphQL ACF Mutations. Please install the WPGraphQL ACF Mutations plugin via Composer.
            </samp>
            <br>
            <br>
            <code>
                composer install:prod
            </code>
            "
        );
    }

    require_once __DIR__ . '/wordpress/vendor/autoload.php';
}

/**
 * Define constants
 */
const WPGRAPHQL_REQUIRED_MIN_VERSION = '0.4.0';
const WPGRAPHQL_ACF_VERSION = '0.5.3';

/**
 * Initialize the plugin
 *
 * @return void
 */
function init()
{
    /**
     * If either ACF or WPGraphQL are not active, show the admin notice and bail
     */
    if (!can_load_plugin()) {
        // Show the admin notice
        add_action('admin_init', __NAMESPACE__ . '\show_admin_notice');
        return;
    }
    // register acf input types such as groups
    new RegisterTypes();

    // run the inputs filtering
    new RegisterInputs();

    // run the post object actions
    Mutations::registerMutations();
}

add_action('init', '\Zngly\ACFM\init');

/**
 * Check whether ACF and WPGraphQL are active, and whether the minimum version requirement has been
 * met
 *
 * @return bool
 * @since 0.3
 */
function can_load_plugin()
{
    // Is ACF active?
    if (!class_exists('ACF'))
        return false;

    // Is WPGraphQL active?
    if (!class_exists('WPGraphQL'))
        return false;

    // Do we have a WPGraphQL version to check against?
    if (empty(defined('WPGRAPHQL_VERSION')))
        return false;

    return true;
}

/**
 * Show admin notice to admins if this plugin is active but either ACF and/or WPGraphQL
 * are not active
 *
 * @return bool
 */
function show_admin_notice()
{

    /**
     * For users with lower capabilities, don't show the notice
     */
    if (!current_user_can('manage_options')) {
        return false;
    }

    add_action(
        'admin_notices',
        function () {
?>
        <div class="error notice">
            <p><?php esc_html_e(sprintf('Both WPGraphQL (v%s+) and Advanced Custom Fields (v5.7+) must be active for "wp-graphql-acf" to work', WPGRAPHQL_REQUIRED_MIN_VERSION), 'wp-graphiql-acf'); ?></p>
        </div>
<?php
        }
    );
}
