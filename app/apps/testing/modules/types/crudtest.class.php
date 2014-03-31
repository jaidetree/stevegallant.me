<?php
namespace jframe\testing;
use jframe\APP as APP, \ReflectionClass, \ReflectionMethod;

class CRUDTest extends Test
{
    protected $model = "stdObject";
    protected $controller = "stdObject";

    public function test_create()
    {
        $columns = call_user_func( $this->model . '::Table')->columns;
        for($i=0;$i<=1000;$i++)
        {
            $model = new $this->model();
            foreach($columns as $column)
            {
                $column_name = $column->name;
                if( $column_name == "id" )
                {
                    continue;
                }
                $model->$column_name = md5(time());
            }
            $this->output("saving model: " . $this->model . ' id: ' . $columns[0]['name']);
            $this->should_pass($model->save(), 'Saving model ' . $this->model . '.');
            $this->data[] = $model;
        }
    }

    public function test_read()
    {
        $data = call_user_func( $this->model . '::all');
        $this->should_pass(count($data) > 0, 'The number of rows should be more than 0.');

        foreach( $data as $item )
        {
            $this->should_pass(is_object($item), 'The collection of rows should be an item.');
        }
    }

    public function test_update()
    {
        $columns = array_values(call_user_func( $this->model . '::Table')->columns);
        $data = $this->data;

        $this->should_pass(count($data) > 0, 'The number of rows should be more than 0 in test_update.');

        foreach( $data as $item )
        {
            $name = $columns[1]->name;
            $item->$name = base64_encode(md5(time()));
            $this->should_pass($item->save(), 'The table ' . $this->model . ' could not be updated.');
        }
    }

    public function test_delete()
    {
       foreach( $this->data as $item ) 
       {
            $this->should_pass($item->delete(), 'Tried deleting item ' . $item->id );
       }
    }

    public function test_routes()
    {
        $controller = new ReflectionClass($this->controller);

        foreach( $controller->getMethods(ReflectionMethod::IS_PUBLIC) as $method )
        {
            if( $method->name == "route" )
            {
                continue;
            }

            $method_class = str_replace('Controller', '', $method->class);
            $method_name = trim($method->name);

            $url = APP::url($method_class . '.' . $method_name ); 

            if( $url == "#error" )
            {
                $this->error( "Could not find route to " . $method_class . ' ' . $method_name );
            }
            else
            {
                $this->should_pass( preg_match('#^http(.*)/$#i', trim($url)) == 1, 'URL reversal not working for ' . $url );
            }
        }
    }

    public function test_controller()
    {

        $controller = new ReflectionClass($this->controller);

        foreach( $controller->getMethods(ReflectionMethod::IS_PUBLIC) as $method )
        {
            if( $method->name == "route" )
            {
                continue;
            }

            $method_class = str_replace('Controller', '', $method->class);
            $method_name = trim($method->name);

            $url = APP::url($method_class . '.' . $method_name ); 

            if( $url == "#error" )
            {
                $this->error( "Could not find route to " . $method_class . ' ' . $method_name );
            }
            else
            {
                $this->should_pass( preg_match('#^http(.*)/$#i', trim($url)) == 1, 'URL reversal not working for ' . $url );
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            @session_write_close();
            $result = curl_exec($ch);
            curl_close($ch);
            @session_start();

            $this->should_fail( preg_match( '/404|Error|Not Found/', $result) == 1, "An error occured when testing " . $url . htmlentities($result) );
        }
    }
}
?>