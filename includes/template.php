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

    /**
     * Give priority to app views but also include
     * sub app views.
     */

    $files = array(
        ROOT_DIR . '/views/' . $file . ".php",
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
    echo SITE_URL . 'static/';
}
?>
