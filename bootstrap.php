<?php
namespace jframe\bootstrap;

use \ReflectionClass;

    /**
     * Files to include 
     */
class Includes {
    private $files = array( 
        'modules' => array(
            //"php-activerecord/ActiveRecord.php",
            "context.class.php",
            //"messenger.class.php",
            //"auth.php",
            //"standardauth.class.php",
            "html.php",
            //"validators.php",
            //"fieldwidgets.php",
            //"fields.php",
            "api.php",
            //"forms.php",
            "observer.class.php",
            //"crud.class.php"
        ),
        'apps' => array(
            //'admin',
            //'testing'
            'api'
        )
    );
    public function files() 
    {

        return $this->$files;
    }

    public function get($name)
    {
        if( array_key_exists($name, $this->files) )
        {
            return $this->files[$name];
        }
        else 
        {
            return array();
        }
    }

    public function __get($name)
    {
        return $this->get($name);
    }

}

define( 'jframe\bootstrap\ROOT', dirname( __FILE__ ) . '/' );
require_once ROOT . "core/includes/app.class.php";

use jframe\APP as APP;

class TaskMaster 
{

    private $start = 0;
    private $stop = 0;

    private function start()
    {
       $this->start = time(); 
    }

    private function stop()
    {
       $this->stop = time(); 
    }

    public function run($tasks)
    {
        $this->start();
        $class = new \ReflectionClass($tasks);

        foreach( $class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method )
        {
            if( ! preg_match( "&^setup_&", $method->name ) )
            {
                continue;
            }
            $method->invoke($tasks);
        }
        $this->stop();
    }
}

class FileLoader 
{
    protected $dir = "";
    public $file;
    protected $callbacks = array();
    protected $filter = "\.php$";

    public function __construct($dir, $filter=false, $callbacks=array())
    {
        if( strlen($dir) < 3 )
        {
           return false; 
        }
        $this->dir = $dir;
        $this->dir = preg_replace( "#/$#", "", $this->dir ) . '/';

        $this->callbacks = $callbacks;


        if( $filter )
        {
            $this->filter = $filter;
        }


        $files = $this->read_files();

        foreach($files as $file)
        {
            $this->file = $this->dir . $file;

            if( $this->process() ) 
            {
                $this->include_file();
            }
        }
    }

    public function read_files() 
    {
        if( array_key_exists('read_files', $this->callbacks) )
        {
            return $this->callbacks['read_files']();
        }

        $files = array();
        $dir = dir($this->dir);
        while( ($file = $dir->read()) !== false ) 
        {
            if( ! preg_match("@" . $this->filter . "@i", $file) ) 
            {
                continue;
            }

            $files[] = $file;
        }

        return $files;
    }

    public function process() 
    {
        if( array_key_exists('process', $this->callbacks) )
        {
            return $this->callbacks['process']($this);
        }

        if( file_exists( $this->file ) ) {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function include_file() 
    {
        if( array_key_exists('include_file', $this->callbacks) ){
            return $this->callbacks['include_file']($this);
        }

        require_once $this->file;
    }
}

class DirectoryCrawler
{
    private $recursive = false;
    private $level_limit = 0;
    private $filter = "\.php$"; 
    private $files = array();

    public function __construct($dir, $filter="\.php$", $recursive=false, $level_limit=0)
    {

        if( strlen($dir) < 3 )
        {
            return false;
        }

        $this->filter = $filter;
        $this->recursive = $recursive;
        $this->level_limit = $level_limit;

        $this->files = $this->crawl($dir);

    }

    public function files() 
    {
        return $this->files;
    }

    public function dump()
    {
        echo "<pre>";
        print_r($this->files());
    }

    public function crawl($dir, $level=0)
    {
        if( $this->level_limit > 0 && $level > $this->level_limit )
        {
            return array();
        }

        $files = array();
        $dir = preg_replace( "#/$#", "", $dir ) . '/';
        $dirIterator = dir($dir);

        while( ($file = $dirIterator->read()) !== false ) 
        {
            if( substr($file, 0, 1) == "." )
            {
                continue;
            }

            $file_path = $dir . $file;

            if( is_dir( $file_path ) && $this->recursive == true ) 
            {
                $files = array_merge($files, $this->crawl($file_path, $level+1));
            } 
            else if( is_file( $file_path ) )
            {

                if( ! preg_match("@" . $this->filter . "@i", $file_path) ) 
                {
                    continue;
                }

                $files[] = $file_path;
            }

        }

        return $files;
    }
}

class FileIncluder
{
    public function __construct($crawler, $filter_callback="")
    {
        $files = $crawler->files();

        foreach($files as $file) 
        {
            if( is_callable($filter_callback) ) 
            {
                $file = $filter_callback($file);
            }

            if( $file && file_exists($file) )
            {
                require_once( $file );
            }
        }
    }
}

class AppTasks
{
    private $files = null;

    public function __construct($files=false)
    {
        $this->files = new \stdClass();

        if( is_object($files) )
        {
            $this->files = $files;
        }
    }

    public function setup_dirs() 
    {

        APP::dir('root', ROOT);
        APP::dir('app', ROOT . 'app/');
        APP::dir('core', ROOT . 'core/');
    }

    public function setup_config()
    {
        require_once APP::dir('core') . 'includes/collection.class.php';
        APP::set('modules', new \Collection(APP::modules()));
        APP::set('vars', new \Collection());

        $config = array();
        require_once APP::dir('root') . 'config/config.php';

        APP::load_config($config);
    }

    public function setup_includes()
    {
        new FileLoader(APP::dir('core') . 'includes/');
    }

    public function setup_modules() 
    {
        $modules = $this->files->modules;

        new FileLoader(APP::dir('core') . 'modules/', null, array( 
            'read_files' => function() use ($modules) { 
                return $modules;
            },
        ));
    }

    public function setup_observers()
    {
        new FileLoader(APP::dir('app') . 'observers/', '\.class\.php$');
    }

    public function setup_contollers() 
    {
        new FileLoader(APP::dir('app') . 'controllers/', '\.class\.php$');
    }

    public function setup_drivers()
    {
        /**
         * Initalize our drivers
         */
        /*
        \ActiveRecord\Config::initialize(function($cfg) {
            $cfg->set_model_directory( APP::dir('app') . 'models');
            $cfg->set_connections(array(
                'development' => 'mysql://' . APP::cfg('db', 'username') . ':' . APP::cfg('db', 'password') . '@' . APP::cfg('db', 'host' ) . '/' . APP::cfg('db', 'name' )
            ));
        });
        */
        
        /**
         * Authentication initialization
         */
        /*
        \Auth::driver(new \StandardAuth());
        \Auth::driver()->start();
        */
    }

    public function setup_apps()
    {
        $dirs = array( 
            'modules' => '\.php$',
            'models' => '[A-Za-z]+\.php$',
            'controllers' => '\.class\.php$',
            'observers' => '\.class\.php$'
        );

        foreach( $this->files->apps as $app )
        {
            $app_dir = APP::dir('app') . 'apps/' . $app . '/';

            if( ! is_dir( $app_dir ) )
            {
                throw new \Exception("Subapp '" . $app . "' is not installed. Looking for '" . $app_dir . "'.");
            }

            foreach( $dirs as $dir=>$filter )
            {
                if( ! is_dir( $app_dir . $dir ) )
                {
                    continue;
                }

                new FileLoader( $app_dir . $dir, $filter, array( 
                    'include_file' => function($loader) {
                        require_once $loader->file;
                        APP::notify($loader, 'include_file', $loader->file);
                    }
                ));
            }

            if( file_exists( $app_dir . 'init.php' ) )
            {
                require_once $app_dir . 'init.php';
            }
        }
    }
}

class Bootstrap 
{

    private $task_manager = null;
    private $includes = null;

    public function __construct()
    {
        $this->task_manager = new TaskMaster(); 
        $this->includes = new Includes();
        $this->task_manager->run(new AppTasks( $this->includes ));
    }
}

APP::register_module(new Bootstrap());
?>