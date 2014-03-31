<?php
include "bootstrap.php";
use jframe\APP as APP;
APP::module('router')->load_route(preg_replace('/^\//', '', $_SERVER['REQUEST_URI']) );
?>
