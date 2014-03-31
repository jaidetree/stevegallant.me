<?php
namespace jframe;
/**
 * This class is our main app. Holds our settings, data, and acces
 * to some global utilities.
 */
class APP
{
    static $data = array();
    static $urls = array();
    static $extra = array(
        'modules' => array()
    );
    static $functions = array();
    static $dirs = array();

    private static $errors_handled = false;

    public static $db;

    public static function cfg($section, $key)
    {
        return self::$data[$section][$key];
    }

    /**
     * Init
     */
    public static function init()
    {
        self::set('modules', new \stdClass() );
        self::set('vars', new \stdClass() );
        self::$data['app']['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    }


    /**
     * Load config options
     */
    public static function load_config($config)
    {
        self::$data = array_merge(self::$data, $config);
    }

    public static function vars($key)
    {
        return self::cfg('app', $key);
    }

    /**
     * Allows shortcut functions through the app.
     */
    public static function __callStatic($name, $arguments)
    {
        if( ! self::function_exists($name) )
        {
            $error_msg = 'Module function does not exist: ' . $name;

            if( ! self::$errors_handled )
            {
                trigger_error($error_msg);
            }
            else
            {
                throw new Exception($error_msg);
            }
            return false;
        }

        return call_user_func_array(self::get_function($name), $arguments);
    }

    public static function function_exists($name)
    {
        if( array_key_exists($name, self::$functions) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Register function.
     */
    public static function register_function($class, $function, $short_name="")
    {
        if( ! $short_name )
        {
            $short_name = $function;
        }

        self::$functions[$short_name] = array( $class, $function );
    }

    public static function get_function($name)
    {
        return self::$functions[$name];
    }

    public static function register_module($class, $name=false)
    {
        if( ! $name )
        {
            $name = get_class($class);
            $namespaces = explode("\\", $name);
            $name = end($namespaces);
        }
        $name = strtolower($name);
        return self::modules()->$name = $class;
    }

    public static function module($name)
    {
        return self::$extra['modules']->$name;
    }

    public static function modules()
    {
        return self::$extra['modules'];
    }

    public static function run($class, $function, $arguments=array())
    {
        $module = self::module($class);
        call_user_func_array( array( $module, $function ), $arguments);
    }

    public static function set($variable, $value)
    {
        self::$extra[$variable] = $value;
    }

    public static function dir($name, $path=false)
    {
        if( $path )
        {
            return self::$dirs[$name] = $path;
        }
        else
        {
            return self::$dirs[$name];
        }
    }

}

APP::init();
?>