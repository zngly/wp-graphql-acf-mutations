<?php

namespace Zngly\ACF\Dev;

require_once __DIR__ . '/acf/acf.php';

class Init
{

    public static function run()
    {
        add_action('init', function () {
            Init::welcome_message();
            Init::plugins();
        });
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
}
