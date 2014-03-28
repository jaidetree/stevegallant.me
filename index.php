<?php
session_start();
include "includes/template.php";

define('ROOT_DIR', dirname(__FILE__));
define('SITE_URL', 'http://stevegallant.dev/');

echo render('app');
?>
