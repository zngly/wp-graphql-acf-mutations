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
}
