<?php
class CRUDController extends Controller
{
    protected $model = "";
    protected $columns = array();
    protected $_name = "";
    protected $_urls = array();
    protected $fields = array();

    public function __construct()
    {
        $this->fields = $this->columns = call_from_model($this->model, 'Table')->columns;
        $this->get_fields();
    }

    public function read()
    {
        login_required();

        $data = call_from_model($this->model, 'all');

        return new TemplateResponse('cms/read.php', array( 
            'name' => $this->_name, 
            'data' => $data, 
            'fields' => $this->fields, 
            'urls' => $this->_urls
        ));
    }

    public function show($data)
    {
        login_required(); 
        return new TemplateResponse($template, $args);
    }

    private function get_fields()
    {
        $fields = array();
        $reflector = new ReflectionClass($this);

        foreach( $reflector->getProperties( ReflectionProperty::IS_PUBLIC ) as $field )
        {
            $data = $field->getValue($this);
            $fields[] = array( 
                'name' => $field->getName()
            );
        }

        if( count( $fields ) )
        {
            $this->fields = $fields;
        }

        foreach( $this->fields as $key=>$field )
        {
            $this->fields[$key]['label'] = ucwords(str_replace('_', ' ', $field['name']));
        }
    }
}
?>