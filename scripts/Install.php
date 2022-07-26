<?php

namespace Zngly\ACFM\Scripts;

use Zngly\ACFM\Scripts\Utils;


class Install
{

    // create root_dir variable
    private string $root_dir;

    // singleton
    private static $instance = null;

    // construct
    public function __construct()
    {
        $this->root_dir = dirname(dirname(__FILE__));
    }

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new Install();
        return self::$instance;
    }

    public static function run()
    {

        self::getInstance()->wordpress();
        self::getInstance()->npm();
    }

    private function wordpress()
    {
        self::print("Installing Wordpress...\n");

        // install wordpress
        $command = "cd {$this->root_dir} && composer install --prefer-dist --no-interaction";
        echo "running: " . $command . "\n";
        $output = shell_exec($command);
        echo $output;

        self::$instance->modify_wordpress();

        self::print("Wordpress finished.\n");
    }

    private function modify_wordpress()
    {
        self::print("Modifying Wordpress...\n");

        // add autoload to wp-config
        $wordpress_dir = $this->root_dir . '/wordpress';
        Utils::add_to_file($wordpress_dir . '/wp-config.php', "if(!defined('ABSPATH'))", 'require ABSPATH . "vendor/autoload.php";');

        // load our dev code here
        // create a new file called zngly.php in the mu-plugins folder
        $zngly_path = Utils::format_path($wordpress_dir . '/wp-content/mu-plugins/zngly_autoloader.php');

        // create the file
        $zngly_file = fopen($zngly_path, 'w');
        // write the contents
        fwrite($zngly_file, "<?php\n\nuse Zngly\ACFM\Dev\Init;\nInit::run();\n\n");
        // close the file
        fclose($zngly_file);

        // delete all themes except twentytwentytwo
        $themes_dir = $wordpress_dir . '/wp-content/themes';
        $themes = scandir($themes_dir);
        foreach ($themes as $theme) {
            if ($theme != '.' && $theme != '..' && $theme != 'twentytwentytwo') {
                $theme_path = $themes_dir . '/' . $theme;
                Utils::delete_dir($theme_path);
            }
        }

        Utils::copy_plugin();
    }

    private function npm()
    {
        self::print("Installing NPM...\n");

        // install npm
        $command = "cd {$this->root_dir} && npm install --no-save";
        echo "running: " . $command . "\n";
        $output = shell_exec($command);
        echo $output;
    }

    public static function print(string $message)
    {
        echo "\n" . $message . "\n";
    }
}
