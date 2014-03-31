<?php
use jframe\APP as APP;

class Errors
{
    static $errors = array();

    public function __construct()
    {

        set_error_handler( array( $this, 'exception_handler'), E_ALL & ~E_NOTICE );
        set_exception_handler(array( $this, 'error' ));
    }

    public function exception_handler($errno, $errstr, $errfile, $errline ) 
    {
        $errfile = str_replace( APP::dir('root'), '', $errfile);
        throw new ErrorException($errstr . ' in ' . $errfile . ' on line ' . $errline, $errno, 0, $errfile, $errline);
    }

    public function error($e)
    {
        echo "<strong>Error:</strong> " . $e->getMessage() . "<br />";
    }
}

APP::set('errors', new Errors());

?>