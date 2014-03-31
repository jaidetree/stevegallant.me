<?php
namespace jframe\testing;
use jframe\APP, \HTML;

/**
 * Class: TestPrinter
 *
 * Responsible for printing out lines to the browser.
 */
class TestPrinter
{
   public static function start()
   {
        echo "<pre>";
   } 
   public static function stop()
   {
        echo "</pre>";
   }
   private static function send($object)
   {
        echo $object . "\n"; 
   }
   public static function rule($char="=")
   {
        self::send(new TestPrintRule($char));
   }
   public static function headline($line)
   {  
        self::send(new TestPrintRule());
        self::send(new TestPrintHeader(strtoupper(date( 'h:i:s a' )) . ': ' . $line));
        self::send(new TestPrintRule());
   }
   public static function action($line, $indent=1)
   {
        self::send(new TestPrintLine($line, $indent));
   }
   public static function error($line, $indent=2)
   {
        self::send(new TestPrintError($line, $indent));
   }
}
class TestPrintDisplay
{
    protected $data;
    public function __construct($data)
    {
       $this->data = $data; 
    }
    public function __toString()
    {
        return $this->output();
    }
}
class TestPrintRule extends TestPrintDisplay
{
    public function __construct($char="=")
    {
        $this->data = $char;
    }

    public function output()
    {
        $suffix = "";
        if( $this->data != '=' )
        {
            $suffix = "\n";
        }
        return $suffix . str_repeat($this->data, 100) . $suffix;
    }
}
class TestPrintHeader extends TestPrintDisplay
{
    public function __construct($data)
    {
        parent::__construct($data);
    }
    public function output()
    {
        return $this->data;
    }
}
class TestPrintLine extends TestPrintDisplay
{
    public function __construct($text, $indent=1)
    {
        $this->indent = $indent;
        parent::__construct($text);
    }
    public function output()
    {
        return str_repeat("\t", $this->indent) . $this->data;
    }
}
class TestPrintError extends TestPrintLine
{
    public function output()
    {
        $tag = HTML::span(array( 'style' => 'color: red;'), $this->data);
        return str_repeat("\t", $this->indent) . $tag;
    }
}
class TestPrintLineBreak extends TestPrintDisplay
{
    public function output()
    {
        return "\n\n";
    }
}

class TestError extends \Exception
{
    public function __construct($message="", $code=0, $exception=NULL)
    {
        parent::__construct($message, $code, $exception);    
    }

    public function __toString()
    {
        TestPrinter::error( $this->message );
        return ' ';
    }
}
?>