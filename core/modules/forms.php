<?php
namespace jframe\forms;
use Collection, ReflectionClass, ReflectionProperty;
use HTML;

class BaseForm
{
    protected $fields = array();
    protected static $formats = array(
        'html' => 'jframe\forms\HTMLFormatter',
    );

    public function __construct()
    {
        $this->fields = new Collection();

        $class = new ReflectionClass($this);
        
        foreach( $class->getProperties(ReflectionProperty::IS_PUBLIC) as $property )
        {
            $name = $property->getName();
            $field = $property->getValue($this);

            if( ! is_array($field) or ! class_exists('jframe\forms\\' . $field[0]) )
            {
                continue;
            }

            list($field_class, $args) = $field;
            $args['name'] = $name;

            $this->fields->append( $name, FieldFactory::create($field_class, $args) );

            unset( $this->$name );
        }
    }

    public function fields()
    {
        return $this->fields->iterator();
    }

    public function format($format)
    {
        $formatter = BaseForm::$formats[$format];
        $formatter = new $formatter($this);
        return $formatter->output();
    }

    public function as_html()
    {
      return $this->format('html');
    }
}


interface Formatter {
    public function output();
}

class HTMLFormatter implements Formatter {
    private $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function output()
    {
        $html = HTML::ul();

        foreach( $this->form->fields() as $name=>$field)
        {
            $input = $field->render();
            $html->insert(array(
                "\n",
                HTML::li(array(), array( 
                    "\n",
                    HTML::label($field->label(), array( 'for' => 'id_' . $name )),
                    "\n",
                    $input,
                    "\n"
                )),
                "\n"
            ));
        }

        return "\n" . $html;
    }
}
?>