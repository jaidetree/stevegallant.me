<?php
class ContentEntry extends ActiveRecord\Model
{
    static $belongs_to = array(
        array('content_type'),
        array('content_row'),
        array('content_field')
    );
}
?>