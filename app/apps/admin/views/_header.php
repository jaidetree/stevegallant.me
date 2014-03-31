<!DOCTYPE HTML>
<html>
    <head>
        <title><?php if( $page_title ): ?><?php echo $page_title; ?><?php else: ?>KCADStudio Admin<?php endif; ?></title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="<?php static_url('admin/css/bootstrap.min.css'); ?>" />
        <link rel="stylesheet" href="<?php static_url('admin/css/sorting_table.css'); ?>" />
        <link rel="stylesheet" href="<?php static_url('admin/css/screen.css'); ?>" />
        <script src="<?php static_url(); ?>js/jquery-1.8.2.min.js"></script>
        <script src="<?php static_url(); ?>js/jquery.dataTables.min.js"></script>
        <script src="<?php static_url(); ?>js/crud.js"></script>
        <script src="<?php static_url('admin/js/modal.js'); ?>"></script>
    </head>
    <body>
        <div id="wrapper">
            <header id="header">
                <div class="navbar navbar-inverse">
                    <div class="navbar-inner">
                        <nav>
                            <ul class="nav">
                                <li><a href="<?php url('Pages.home'); ?>">Home</a></li>
                                <li><a href="<?php url('admin\Dashboard.index'); ?>">Dashboard</a></li>
                                <li><a href="<?php url('admin\Videos.index'); ?>">Videos</a></li>
                                <li><a href="<?php url('admin\Departments.index'); ?>">Departments</a></li>
                                <li><a href="<?php url('admin\Users.index'); ?>">Users</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>
            <section id="content">
