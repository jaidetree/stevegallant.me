<?php
class Collection
{
    private $_data = array();
    private $_index = array();
    private $_iterator = null;

    public function __construct($data=null)
    {
        if( is_array($data) )
        {
            $this->_data = $data;
            $this->_index = array_keys($this->_data);
        }
    }

    public function iterator()
    {
        return new ArrayIterator($this->_data);
    }

    public function __get($name)
    {
        if( is_numeric($name) )
        {
            return $this->_data[ $this->_index[$name] ];
        }else{
            return $this->_data[(string)$name];
        }
    }

    public function __set($name, $value)
    {
        $this->append($name, $value);
    }

    public function get($name, $default=null)
    {
        if( array_key_exists($name, $this->_data) )
        {
            return $this->$name;
        }
        else
        {
            return $default;
        }
    }

    public function append($name, $value)
    {
       $this->_data[(string)$name]  = $value;
       $this->_index[] = (string)$name;
    }
}
?>