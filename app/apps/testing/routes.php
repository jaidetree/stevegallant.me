<?php
$routes = array(
    array('/$', 'jframe\testing\Tests.index'),
    array('/verbose/$', 'jframe\testing\Tests.verbose'),
    array('/([-_a-z]+)/$', 'jframe\testing\Tests.run'),
);
?>