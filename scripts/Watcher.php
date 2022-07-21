<?php

namespace Zngly\ACF\Scripts;

require dirname(dirname(__FILE__)) . '/wordpress/vendor/autoload.php';

use Spatie\Watcher\Watch;


// class will watch a directory for changes and run a command when a file is added or modified
class Watcher
{

    public static function run()
    {
        // get the directory to watch
        $src_folder = dirname(dirname(__FILE__)) . '/src';

        // get all the files in the directory recursively
        $plugin_name = Utils::get_plugin_name();
        $plugin_folder = Utils::get_root_dir() . "/wordpress/wp-content/plugins/{$plugin_name}";

        // watch for any file changes in the directory
        self::watch_dirs([$src_folder, "{$plugin_folder}/{$plugin_name}.php"]);
    }



    // function to watch a directory for changes
    public static function watch_dirs(array $dirs)
    {
        $root_dir = Utils::get_root_dir();
        $plugins_folder = $root_dir . "/wordpress/wp-content/plugins/" . Utils::get_plugin_name();

        Watch::paths(...$dirs)
            ->onAnyChange(function (string $type, string $path) use ($root_dir, $plugins_folder) {
                $path_parts = explode($root_dir, $path);
                $new_path = $plugins_folder . $path_parts[1];

                switch ($type) {
                    case Watch::EVENT_TYPE_DIRECTORY_CREATED:
                        mkdir($new_path, 0777, true);
                        break;
                    case Watch::EVENT_TYPE_DIRECTORY_DELETED:
                        rmdir($new_path);
                        break;
                    case Watch::EVENT_TYPE_FILE_DELETED:
                        unlink($new_path);
                        break;
                    case Watch::EVENT_TYPE_FILE_CREATED:
                    case Watch::EVENT_TYPE_FILE_UPDATED:
                        copy($path, $new_path);
                        break;

                    default:
                        echo "Event Happened: {$type} - from {$path} - to {$new_path}\n";
                        break;
                }
            })
            ->start();
    }
}
