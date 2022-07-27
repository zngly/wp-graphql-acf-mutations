<?php

namespace Zngly\ACFM\Dev;

require_once __DIR__ . '/acf/acf.php';

define('GRAPHQL_JWT_AUTH_SECRET_KEY', 'vSoj)4k%[_10`%|/3l^M5AB XIzrIN=A]Z%4=9?+3D-F<E6(75U@yp(*e)Ckfi5`');

class Init
{

    public static function run()
    {
        add_action('admin_init', function () {
            Init::welcome_message();
            Init::plugins();
            Init::introspection();
            Init::jwt_auth();
            Init::cors();
        }, 1);

        add_action('init', function () {
            Init::cors();
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

    public static function jwt_auth()
    {
        // check if there is a user with the role of "api" and name of "api"
        $user = get_user_by('login', 'api');
        if (!$user) {
            $new_user_id = wp_create_user('api', 'vLmDecjmp&L][qgwipjqb34)ZTmMQhUOuT2@8ESUkql:&£aojAF0a87JhawJmCiFKxkP0Kf3aTI$5vJ8xVkBpaQuZKu', '');
            $new_user = get_user_by('id', $new_user_id);
            $new_user->set_role('administrator');
        }

        add_filter('graphql_jwt_auth_expire', function ($expiration) {
            return 60 * 10;
        }, 10, 1);
    }

    public static function cors()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Cache-Control, Pragma, Origin, Authorization, Content-Type, X-Requested-With");
    }
}
