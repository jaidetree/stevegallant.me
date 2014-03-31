<?php
class ContentField extends ActiveRecord\Model
{
    static $belongs_to = array(
        array('content_type')
    );
    static $has_many = array(
        array('content_entries', 'foreign_key' => 'content_row_id')
    );
}
?>