<?php
class ContentRow extends ActiveRecord\Model
{
    static $belongs_to = array(
        array('content_type', 'foreign_key' => 'content_type_id')
    );
    static $has_many = array(
        array('content_entries', 'foreign_key' => 'content_row_id')
    );
}
?>