<?php
namespace jframe\testing;
use jframe\APP, \HTML;

/**
 * Class TestLogger
 *
 * Fires on the methods of the TestRunner.
 */
class TestLogger
{
    private static $timer;
    private static $total_time;

    function watch_start_testing($name)
    {
        TestPrinter::start();
        self::$total_time = microtime(true);
        TestPrinter::headline('STARTED TEST: ' . $name);
    }
    function watch_finished_testing($class, $errors=0)
    {
        TestPrinter::rule();
        $time = HTML::span(array( 'style' => "font-weight: bold;color: blue;" ), round((microtime(true) - self::$total_time), 5));
        TestPrinter::action('Finished Testing at ' . date("h:i a") . ' in ' . $time . ' seconds, ' . $errors . ' errors were encountered.', 0);
        TestPrinter::rule();
        TestPrinter::stop();
    }
    function watch_start_test($method)
    {
       self::$timer = microtime(true);
       TestPrinter::action('Running Test: ' . $method->name);
    }
    function watch_ran($method, $success=false)
    {
        $tag = HTML::span();
        if( $success )
        {
            $tag->style = "color: green;";
            $tag->insert('Test Passed');
            $status = "..........";
        }
        else 
        {
            $tag->style = "color: red;";
            $status = "..........";
            $tag->insert('Test Failed');
        }
        $time = HTML::span(array( 'style' => "font-weight: bold;color: blue;" ), round((microtime(true) - self::$timer), 5));
        TestPrinter::action($status . $tag . ' in ' . $time . ' seconds.', 2);
        TestPrinter::rule('-');
    }

    function watch_teardown()
    {
        TestPrinter::action('Running Teardown....', 2);
    }
}
?>