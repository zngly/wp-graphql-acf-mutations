<?php

namespace Zngly\ACFM\Dev;

require_once __DIR__ . '/acf/acf.php';

class Init
{

    public static function run()
    {
        add_action('admin_init', function () {
            Init::welcome_message();
            Init::plugins();
            Init::introspection();
        }, 1);
    }

    public static function welcome_message()
    {
        echo '<script>';
        echo 'console.log("Welcome to Zngly WP-Graphq-ACF-Mutations");';
        echo '</script>';
    }

    public static function plugins()
    {
        // get all the activated plugins
        $active_plugins = get_option('active_plugins');

        // get all the plugins
        $plugins = get_plugins();

        // if a plugin is not activated, activate it
        foreach ($plugins as $plugin_path => $plugin)
            if (!in_array($plugin_path, $active_plugins))
                activate_plugin($plugin_path);
    }

    public static function introspection()
    {
        // ensure that wpgraphql introspection is enabled
        add_filter('graphql_get_setting_section_field_value', function ($value, $default, $option_name, $section_fields, $section_name) {
            if ($option_name === 'public_introspection_enabled')
                return 'on';

            if ($option_name === 'debug_mode_enabled')
                return 'on';
        }, 10, 5);
    }
}
