<?php
/**
 * Routes determine which controller and method to respond
 * to which URLS using regex patterns.
 */
$routes = array(
    //array( '^login/$', 'Account.login'),
    //array( '^logout/$', 'Account.logout'),
    array( '^$', 'Pages.home' ),
    array( '^api/v1', 'api'),
);
?>
