<?php 
use jframe\APP as APP;
/**
 * Template functions for rendering our views.
 */
function render($file, $args=array())
{
    extract( $args );

    ob_start();
    $file = preg_replace('/(.*)\..*$/', '$1', $file);

    $parts = explode("/", $file);
    $template_file = str_replace( $parts[0] . '/', '', $file);
    $app_dir = "";

    if( Context::get('cd') )
    {
        $app_dir = Context::get('cd');
    }
    else
    {
        $app_dir = APP::dir('app') . 'apps/' . $parts[0] . '/views/';
        Context::set('cd', $app_dir);
    }

    /**
     * Give priority to app views but also include
     * sub app views.
     */

    $files = array(
        $app_dir . $file . ".php",
        APP::dir('app') . 'views/' . $file . ".php",
        APP::dir('app') . 'apps/' . $parts[0] . '/views/' . $template_file . '.php'
    );

    foreach($files as $file)
    {
        if( file_exists( $file ) )
        {
            include $file;
            break;
        }
    }

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
function static_url($uri=false)
{
    if( ! $uri )
    {
        echo APP::vars('url') . 'static/';
    }
    else
    {
        $parts = explode('/', $uri);
        $app = $parts[0];
        array_shift($parts);
        echo APP::vars('url') . 'app/apps/' . $app . '/static/' . implode('/', $parts);
    }
}
?>