<?php 
use jframe\APP as APP;
/**
 * General Functions File
 */
function slugify($str)
{
    $str = preg_replace( '/-/', ' ', $str );
    $str = preg_replace( '/\s\s+/', ' ', $str );
    preg_match_all('/([\s_a-z0-9]+)/', strtolower($str), $chars);
    $str = implode( '', $chars[1] );
    $str = trim($str);
    $str = str_replace(' ', '-', $str);
    return $str;
}

function get_base_class($object)
{
    $name = get_class($object);
    $name = explode("\\", $name);
    $name = end($name);

    return $name;
}

function is_page($controller)
{
    if( is_url($controller) )
    {
        echo "active ";
    }
}
function is_url($controller)
{
    $test_url = APP::url($controller);
    $uri = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $uri;

    if( $url == $test_url )
    {
        return true;
    }
    else
    {
        return false;
    }
}
function url($path, $data=array())
{
    echo APP::url($path, $data);
}
function bodyclass()
{
    $class = Context::get('bodyclass');

    if( $class )
    {
        $class = " " . $class;
    }
    echo $class;
}
function is_selected($i, $value)
{
    if( $i == $value )
    {
        echo ' selected';    
    }
}
function call_from_model($model, $function, $args=array())
{
    return call_user_func_array($model . '::' . $function, $args);
}
function activate_page($path)
{
    if( is_url($path) )
    {
        echo ' class="active"';
    }
}
?>