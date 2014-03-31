<?php 
use jframe\APP;
class Router 
{
    private static $urls;
    public function __construct()
    {
        APP::register_function($this, 'reverseurl', 'url');

        include APP::dir('root') . "routes.php";

        self::$urls = $this->process_routes($routes);
    }

    public function process_routes($routes_array, $base='', $level=0)
    {
        $routes = array();

        foreach($routes_array as $route)
        {
            list($pattern, $action) = $route;

            if( preg_match('@^[-_a-zA-Z0-9/]+$@i', $action) ) 
            {
                $subroutes = $this->load($action);
                $subroutes = $this->process_routes($subroutes, $pattern, $level++);
                foreach( $subroutes as $subroute )
                {
                    array_push($routes, $subroute);
                }
            }
            else
            {
                $route[0] = $base . $route[0];
                $routes[] = $route;
            }
        }

        return $routes;
    }

    public function load($filename) 
    {
        $files = array(
            APP::dir('app') . 'apps/' . $filename . '/routes.php',
            APP::dir('app') . 'apps/' . $filename,
            APP::dir('app') . 'apps/' . $filename . '.php',
        );

        foreach( $files as $file )
        {
            if( file_exists( $file ) )
            {
               require_once $file;
               break;
            }
        }

        return $routes;
    }

    public function append($path, $action)
    {
        $route = array( array( $path, $action ) );
        array_push(self::$urls, $this->process_routes( $route ) );
    }

    public function load_route($url)
    {
        $url = preg_replace('/\?.*$/', '', $url);
        foreach( self::$urls as $route )
        {
            if( preg_match( '#' . $route[0] . '#', $url, $args ) )
            {
                array_shift($args);

                $this->call_action( $route[1], $args ); 
                return;
            }
        }

        $this->call_action( 'Errors.notfound_404', array() );
    }

    /**
     * Call the method on the controller.
     */
    public function call_action( $route, $args)
    {
        if( is_callable($route) )
        {
            call_user_func_array($route, $args);
            return;
        }

        list($controller_name, $action ) = explode(".", $route);

        if( class_exists( $controller_name . 'Controller') )
        {
            $controller_name .= "Controller";
        }

        $controller = new $controller_name;
        $response = call_user_func_array( array( $controller, $action ), $args ); 
        APP::notify(get_class($this), $action);

        if( Context::has_response() )
        {
            $response = Context::response();
        }

        if( is_subclass_of($response, 'Response') )
        {
            $response->render();
        }
    }

    /**
     * Reverse a URL by it's controller/view
     */
    public function reverseurl($path, $args=array())
    {
        foreach( self::$urls as $idx => $route )
        {
            $action = strtolower($route[1]);
            $action = str_replace('jframe\\', '', $action);

            if( $action == strtolower($path) or strtolower($route[1]) === strtolower($path) )
            {
                $url = $route[0];
                $url = preg_replace("/(\(.+\)+)/Ums", "%s", $url);
                $url = preg_replace('/[\[\]^\$\?]/', "", $url);

                if( ( is_array($args) && sizeof($args) > 0 ) || is_numeric($args) )
                {
                    $url = vsprintf( $url, $args );
                }

                $url = preg_replace('/\/$/', '', $url) . '/';

                if( $url == "/" )
                {
                    $url = "";
                }

                return 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url;

            }
        }

        return "#error";
    }
}
APP::register_module( new Router() );
?>