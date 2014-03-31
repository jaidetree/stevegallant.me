<?php
class ContentType extends ActiveRecord\Model
{
    static $has_many = array(
        array('content_fields', 'foreign_key' => 'content_type_id'),
        array('content_rows', 'foreign_key' => 'content_type_id'),
        array('content_entries', 'foreign_key' => 'content_type_id')
    );
}
?>