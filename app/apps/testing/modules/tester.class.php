<?php
namespace jframe\testing;

use jframe\APP as APP;
use jframe\bootstrap\FileLoader, 
    jframe\bootstrap\RecursiveFileLoader,
    jframe\bootstrap\DirectoryCrawler,
    jframe\bootstrap\FileIncluder;
use \Collection;
/** 
 * Tester.
 * Sets up the routes
 * Loads the tests.
 */
class Tester
{
    private $tests;
    private $runner;

    public function __construct()
    {
        $this->tests = new Collection();

    }   


    public function set_runner($runner)
    {
        $this->runner = $runner;
    }

    public function runner()
    {
        return $this->runner;
    }

    public function tests()
    {
        return $this->tests;
    }

    public function get()
    {
        return $this->tests->iterator();
    }

    public function register_test($display_name, $test)
    {
        $item = new Collection();
        $item->name = $display_name;
        $item->object = $test; 
        $name = get_base_class($test);
        $this->tests->append(slugify($name), $item);
    }
}
?>