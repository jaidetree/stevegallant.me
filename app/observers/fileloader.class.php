<?php 
namespace jframe;

class FileLoaderLogger 
{
    private static $files = array();

    public function watch_include_file($file)
    {
        FileLoaderLogger::$files[] = $file;
    }

    public static function files()
    {
        echo "<pre>";
        print_r( self::$files );
        echo "</pre>";

    }
}

APP::modules()->observer->attach(new FileLoaderLogger(), 'jframe\bootstrap\FileLoader');
?>