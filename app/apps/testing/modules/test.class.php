<?php
namespace jframe\testing;
use jframe\APP as APP;

/**
 * Class: Test
 *
 * Base test all tests must subclass.
 */
class Test
{
    protected $verbose = false;
    protected $status = true;
    protected $_errors = 0;
    protected $end_on_error;

    public function status()
    {
        return $this->status;
    }

    public function reset_status()
    {
        $this->status = true;;
    }

    public function error($message)
    {
        $data = debug_backtrace();
        $data[1]['file'] = basename( $data[1]['file'] );
        $error_msg = vsprintf("Error in %s on line %s in %s", $data[1]);
        $this->_errors++;
        TestPrinter::error( $message . "\n\t\t" . $error_msg);
        $this->status = false;
        if( $this->end_on_error ) {
            APP::modules()->tester->runner()->stop_testing();
            die();
        }
    }

    public function errors()
    {
        return $this->_errors;
    }

    public function output()
    {
        if( ! $this->verbose )
        {
            return false;
        }
        $args = func_get_args();
        $message = array_shift($args);

        if( count($args) > 0 )
        {
            $message = vsprintf($message, $args);
        }

        TestPrinter::action($message, 2);
    }

    public function should_pass($condition, $error_message="")
    {
        if( $condition === true )
        {
            return true;
        }
        else
        {
            $this->error('Condition should pass but failed: ' . $error_message );
            return false;
        }
    }
    public function should_fail($condition, $error_message="")
    {
        if( $condition === true )
        {
            $this->error('Condition should fail but passed: ' . $error_message );
            return true;
        }
        else
        {
            return false;
        }
    }
    public function assert_equal()
    {
        $args = func_get_args();
        foreach($args as $idx=>$arg)
        {
            if( $idx > 0 && $arg[$idx] !== $arg[$idx-1] )
            {
                $this->error(sprintf('Assert failed: %s does not equal %s', $arg[$idx-1], $arg[$idx]));
                return false;
            }
        }

        return true;
    }
    public function assert_not_equal()
    {
        $args = func_get_args();
        foreach($args as $idx=>$arg)
        {
            if( $idx > 0 && $arg[$idx] === $arg[$idx-1] )
            {
                $this->error(sprintf('Assert failed: %s does equal %s', $arg[$idx-1], $arg[$idx]));
                return false;
            }
        }

        return true;
    }
    public function verbose($mode)
    {
        $this->verbose = $mode;
    }
}
