<?php

namespace Zngly\ACF\Scripts;

class Utils
{
    /**
     * Modifies a file by adding a string if a match is found.
     * The string added is added two lines before the match.
     * 
     * @param string $file_path
     * @param string $match_string - the match will be compared without any spaces
     * @param string $replace_string   
     */
    public static function add_to_file(string $file_path, string $match_str, string $replace_str)
    {
        // update file
        $lines = file($file_path);

        // check for error, throw error if file is not found
        if (!$lines)
            throw new \Exception("File not found: " . $file_path);


        $new_config = [];

        foreach ($lines as $key => $value) {
            $trim_val = preg_replace('/\s+/', '', $value);
            $trim_replace = preg_replace('/\s+/', '', $replace_str);

            // if the replace string exists, exit gracefully
            if (strpos($trim_val, $trim_replace) !== false)
                return;

            // we add our code before this line
            if (strpos($trim_val, $match_str) !== false)
                $new_config[] = "{$replace_str}\n\n";

            // add the new line
            $new_config[] = $value;
        }

        $new_file_content = implode("", $new_config);
        file_put_contents($file_path, $new_file_content);
    }

    // delete a directory and all its contents
    public static function delete_dir(string $dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        self::delete_dir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function copy_plugin()
    {
        // get the directory to watch
        $src_folder = dirname(dirname(__FILE__)) . '/src';

        // get all the files in the directory recursively
        $plugin_name = self::get_plugin_name();
        $plugin_folder = self::get_root_dir() . "/wordpress/wp-content/plugins/{$plugin_name}";

        // if the plugin folder doesn't exist, create it
        if (!file_exists("{$plugin_folder}"))
            mkdir("{$plugin_folder}/", 0777, true);

        // copy plugin_idx to the plugins directory
        copy(self::get_root_dir() . "/{$plugin_name}.php", "{$plugin_folder}/{$plugin_name}.php");

        // copy the src folder and the contents recursively to the plugins directory
        self::copy_folder($src_folder . '/', $plugin_folder . '/src');
    }

    // function to copy a folder recursively
    public static function copy_folder($src, $dest)
    {
        $dir = opendir($src);
        @mkdir($dest);
        while (false !== ($file = readdir($dir)))
            if (($file != '.') && ($file != '..'))
                if (is_dir($src . '/' . $file)) self::copy_folder($src . '/' . $file, $dest . '/' . $file);
                else copy($src . '/' . $file, $dest . '/' . $file);
        closedir($dir);
    }


    public static function get_root_dir()
    {
        return dirname(dirname(__FILE__));
    }

    public static function get_root_name()
    {
        return basename(dirname(dirname(__FILE__)));
    }

    public static function get_plugin_name()
    {
        return basename(dirname(dirname(__FILE__)));
    }
}
