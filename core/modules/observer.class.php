<?php
use jframe\APP as APP;
class Observer
{
    private $observers = array();

    public function __construct()
    {
        APP::register_function($this, 'notify');
    }

    public function attach($object, $subject)
    {
        $class = new ReflectionClass($object);        
        $subject = strtolower($subject);

        foreach( $class->getMethods(ReflectionMethod::IS_PUBLIC) as $method )
        {
            if( ! preg_match( '/^watch_/', $method->name) )
            {
                continue;
            }
            $this->observers[$subject][] = array( $object, $method->name );
        }
    }

    public function notify($subject, $method, $args=array())
    {
        if( ! is_string($subject) )
        {
            $subject = get_base_class($subject);
        }

        $subject = strtolower($subject);


        if( ! array_key_exists($subject, $this->observers) )
        {
            return false;
        }

        foreach( $this->observers[$subject] as $observer_method )
        {
            if( $observer_method[1] == "watch_" . $method )
            {
                if( ! is_array( $args ) )
                {
                    $args = array( $args );
                }
                call_user_func_array($observer_method, $args);
            }
        }
    }
}
APP::register_module(new Observer());
?>