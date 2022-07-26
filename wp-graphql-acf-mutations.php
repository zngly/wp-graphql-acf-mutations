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
    if (!can_load_plugin())
        return;

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
