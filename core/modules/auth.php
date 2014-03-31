<?php
class Auth
{
    private static $instance;
    private static $driver;

    public static function driver($driver=null)
    {
        if( $driver )
        {
            self::$driver = $driver;
        }else{
            return self::$driver;
        }
    }
    public static function get()
    {
        if( ! self::$instance )
        {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    public static function user($key=false, $default=false)
    {
        if( ! $key )
        {
            return self::$driver->get_current_user();
        }
        if( self::$driver->get_current_user()->$key )
        {
            return self::$driver->get_current_user()->$key;
        }

        if( $default )
        {
            return $default;
        }
    }

    public static function __callStatic($name, $arguments=array())
    {
        if( method_exists( self::$driver, $name ) )
        {
            return call_user_func_array(array( self::$driver, $name ), $arguments);
        }
    }

    public function __call($name, $arguments=array())
    {
        if( method_exists( self::$driver, $name ) )
        {
            return call_user_func_array(array( self::$driver, $name ), $arguments);
        }
    }

}

abstract class AuthDriver
{
    abstract public function login($username, $password);
    abstract public function logout();
    abstract public function validate_session();
    abstract public function is_logged_in();
    abstract public function get_current_user();
}

function login_required()
{
    if( ! Auth::is_logged_in() )
    {
        Context::response( new RedirectResponse('admin\Accounts.login'));
        Context::flush();
    }
}
?>
