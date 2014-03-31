<?php
/**
 * Handles sending messages to the templates.
 */
class Messenger
{
    private static $messages = array();
    private static $instance;

    public static function all($key=false)
    {
        $_messages = $_SESSION['messages'];

        if( ! is_array($_messages) || ! count($_messages))
        {
            return false;
        }

        unset( $_SESSION['messages'] );
        $_SESSION['messages'] = array();

        if($key)
        {
            return $_SESSION['messages'][$key];
        }

        $messages = array();

        foreach($_messages as $section=>$data)
        {
            $messages[] = $data;
        }

        return $messages;
    }

    public static function has_any()
    {
        if(count($_SESSION['messages']))
        {
            return true;
        }else{
            return false;
        }
    }
}
class Message
{
    private $_data = array();
    public function __construct($type, $message, $extra_classes=false)
    {
        $class = $type;

        if($extra_classes)
        {
            $class .= " " . $extra_class;
        }

        $this->_data = array( 
            'type' => $type,
            'text' => $message,
            'class' => $class
        );
    }

    public function get($key)
    {
        echo $this->_data[$key];
    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function __get($key)
    {
        return $this->_data[$key];
    }
}

function send_message($type, $message, $extra_classes=false)
{
    $message = new Message($type, $message, $extra_classes);
    $_SESSION['messages'][] = $message;
}
?>