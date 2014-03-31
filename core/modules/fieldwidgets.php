<?php
namespace jframe\forms;
use HTML;
/**
 * Field Widgets define how various fields may render in HTML.
 */
abstract class FieldWidget
{
    protected $html;
    protected $_atts = array( 'name' => 'field', 'value' => '' );
    protected $value;

    protected function __construct($atts=array())
    {
        $this->_atts = $atts;
    }

    abstract function render($name, $value=null);

    protected function build_atts($atts=array())
    {
        $this->_atts = array_merge( $this->_atts, $atts);
        return $this->_atts;
    }
}

/**
 * Text field widget.
 */
class InputWidget extends FieldWidget
{
    protected $type = null;

    public function __construct($atts=array())
    {
        parent::__construct($atts, $tag_atts);
    }

    public function render($name, $value=null, $atts=array())
    {
        $atts['type'] = $this->type;
        $atts['name'] = $name;
        $atts['id'] = 'id_' . $name;

        $atts = $this->build_atts($atts);
        
        if( $value == null )
        {
            $value = "";
        }

        if( $value != "" )
        {
            $atts['value'] = $this->value;
        }

        $html = HTML::input($atts);

        return $html;
    }
}

class TextWidget extends InputWidget
{
    protected $type = "text";
}

class CheckboxWidget extends InputWidget
{
    protected $type = "checkbox";
    public function __construct($atts=array())
    {
        parent::__construct($atts);
    }

    public function render($name, $value=null, $atts=array())
    {
        $atts['type'] = $this->type;
        $atts['name'] = $name;
        $atts['id'] = 'id_' . $name;
        $atts = $this->build_atts($atts);

        if( $value === true )
        {
            $atts['checked'] = true;
        }

        return HTML::input($atts);
    }
}

/**
 * Composite Field Widget
 */
class MultiFieldWidget extends FieldWidget
{
    protected $widgets;

    public function __construct($widgets, $atts=array())
    {
        $this->widgets = $widgets;
        parent::__construct($atts);
    }

    public function expand($value)
    {
        if( is_array( $value ) )
        {
            return $value;
        }
        return explode(', ', $value);
    }

    public function render($name, $value=null, $atts=array())
    {
        $this->build_atts($atts);

        if( $value )
        {
            $value = $this->expand($value);
        }

        $span = HTML::span(array('id' => 'id_' . $name));
        foreach($this->widgets as $i => $widget)
        {
            $atts['id'] = 'id_' . $name . '_' . $i;
            $span->insert($widget->render( $name . '_' . $i, $value[$i], $atts));
        }
    }
}

/**
 * Date time in the work.
 */
class DateTimeSelectWidget extends MultiFieldWidget
{
    public function __construct($atts=array())
    {
        $widgets = array(
            new TextWidget($atts),
            new TextWidget($atts)
        );
        parent::__construct($widgets, $atts);
    }

    public function expand($value)
    {
        if( is_array( $value ) ) 
        {
            return $value;
        }

        return array(date("m/d/Y", $value), date("h:i a", $value));
    }

}
?>