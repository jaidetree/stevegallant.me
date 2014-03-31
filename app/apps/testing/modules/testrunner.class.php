<?php
namespace jframe\testing;
use jframe\APP as APP, \ReflectionClass, \ReflectionMethod;

/**
 * Class Test Runner
 *
 * Responsible for running all test methods in a test.
 */
class TestRunner
{
    private $test;
    public function __construct($test_object, $name, $verbose=false)
    {
        APP::modules()->tester->set_runner($this);

        $this->test = $test_object;
        APP::modules()->observer->attach(new TestLogger(), 'TestRunner');
        $class = new ReflectionClass($test_object);
        $this->class = $class;

        $test_object->verbose($verbose);

        APP::notify($this, 'start_testing', array( $name ));


        if( is_callable(array( $test_object, 'setup')) )
        {
            APP::notify($this, 'setup', array( $class->getMethod('setup') ));
            $test_object->setup();
        }

       foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
       {
            if( ! preg_match('/^test_/', $method->name ) )  
            {
                continue;
            }

            APP::notify($this, 'start_test', array( $method ));

            try
            { 
                $method->invoke($test_object);
            }
            catch (Exception $e)
            {
                $test_object->error($e->getMessage());
            }

            $result = $test_object->status();
            APP::notify($this, 'ran', array( $method, $result));
            $test_object->reset_status();
       }

       $this->stop_testing();
    }
    public function stop_testing()
    {
       if( is_callable(array( $this->test, 'teardown')) )
       {
           APP::notify($this, 'teardown', array( $this->class->getMethod('teardown') ));
           $this->test->teardown();
       }

       APP::notify($this, 'finished_testing', array( $class, $this->test->errors() ));
    }
}
?>