<?php
namespace jframe\forms;
use ReflectionClass, HTML;

const NS = "jframe\\forms\\";

/**
 * Form Fields
 *
 * They represent a field in a form object. Used to
 * create forms to represent models in CRUD pages.
 * 
 * 
 * @note <?php print_r( User::table()->columns ) ?>
 */
class Field
{
    protected $widget = 'TextWidget';
    private $default_validators = array();
    private $validators = array();
    protected $value = null;
    protected $initial = null;
    protected $label = null;


    public function __construct($args=array())
    {
        extract($args);
        $this->name = $name;
        $this->inital = $inital;
        $this->label = ( $label ) ? $label : ucwords($name);
        $this->widget = NS . $this->widget;
        $this->widget = new $this->widget($args);
    }

    public function clean($raw_value)
    { 
        $this->value = $this->sanitize($raw_value);
        $this->validate();
        $this->run_validators();

        return $value;
    }

    public function label()
    {
        return $this->label;
    }

    protected function sanitize($value)
    {
        return $value;
    }

    private function compress($value)
    {
        return $value;
    }

    public function validate()
    {
        return ( ! empty($this->value) ) ? true : false;
    }

    public function render($atts=array())
    {
        return $this->widget->render($this->name, $this->value, $atts);
    }

    public function run_validators()
    {
        $errors = array();
        foreach($validators as $validator)
        {
            try 
            { 
                call_user_func($validator, $this->value);
            }
            catch (ValidationError $e)
            {
                $errors[] = $this->error_messages[ $e->getCode() ];
            }
        }

        if( count($errors) )
        {
            throw new ValidationError($errors);
        }
    }

    private function error($group, $class, $content)
    {
        $this->errors[] = array( 
            'group' => $group,
            'class' => $class,
            'message' => $content
        );
    }
}

class BooleanField extends Field 
{
    protected $widget = 'CheckboxWidget';
    
    public function __construct($initial=false, $label=null, $atts=array())
    {
        parent::__construct($initial, $label, $atts);
    }

    protected function sanitize($value)
    {
        return ( $value ) ? true : false;
    }

    private function compress($value)
    {
        return $value === true ? 1 : 0;
    }

    public function validate()
    {
        if( $this->value === true )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

class TextField extends Field
{
    protected $widget = "TextWidget";
    private $max_length = 255;
    private $min_length = 0;

    public function __construct($args=array())
    {
        extract($args);
        parent::__construct($args);
    }
}

class SlugField extends TextField
{
    public function __construct($args)
    {
        parent::__construct($args);
    }

    protected function sanitize($value)
    {
        return slugify($value);
    }

    public function validate()
    {
        return ( slugify( $this->value ) == $this->value && preg_match('/^[-a-z0-9]+$/', $this->value) ) ? true : false;
    }
}

class PasswordField extends TextField
{
    public function __construct($args)
    {
        parent::__construct($args);
    }

    protected function compress($value)
    {
        return Auth::encrypt($value);
    }
}

class IntField extends TextField
{
    protected $widget = 'TextField';

    public function __construct($args)
    {
        parent::__construct($args);
    }

    protected function sanitize($value)
    {
        return grab('/([0-9]+)/', $value);
    }

    private function compress($value)
    {
        return (int)$value;
    }

    public function validate()
    {
        return ( is_int($this->cleaned_value) ) ? true : false;
    }
}

class DateField extends Field
{
    protected $widget = 'DateTimeField';
    private $format = "Y-m-d h:i a";

    public function __construct($args)
    {
        extract( $args );
        if( $format )
        {
            $this->format = $format;
        }

        parent::__construct($args);
    }

    protected function sanitize($value) 
    {
       return grab('/([-\/,\.\sa-zA-Z0-9]+)/', $value);
    }

    private function compress($value)
    {
        return strtotime($value);
    }

    public function validate()
    {
        if( ! is_string($this->value) )
        {
            return false;        
        }

        $date = strtotime($this->value);

        if( ! $date ) 
        {
            return false;
        }

        if( ! checkdate(date("Y", $date), date("m", $date), date("j", $date)) )
        {
           return false; 
        }

        return true;
    }
}

class FieldFactory {
    public static function create($class, $args=array()) 
    {
        return self::new_instance( NS . $class, $args);
    }

    private static function new_instance($class, $args) 
    {
        $reflection_class = new ReflectionClass($class);

        return $reflection_class->newInstanceArgs(array($args));
    } 
}
?>