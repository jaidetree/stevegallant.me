<?php
$routes = array(
    array('/$', 'jframe\admin\Dashboard.index'),
    array('/login/$', 'jframe\admin\Accounts.login'),
    array('/logout/$', 'jframe\admin\Accounts.logout'),

    array('/update_kendall_logo/$', 'jframe\admin\Dashboard.update_kendall_logo'),

    array('/videos/$', 'jframe\admin\Videos.index'),
    array('/videos/edit/([0-9]+)/$', 'jframe\admin\Videos.edit'),
    array('/videos/delete/([0-9]+)/$', 'jframe\admin\Videos.delete'),
    array('/videos/create/$', 'jframe\admin\Videos.create'),
    array('/videos/import/$', 'jframe\admin\Videos.import'),
    array('/videos/image/([0-9]+)/$', 'jframe\admin\Videos.image'),

    array('/users/$', 'jframe\admin\Users.index'),
    array('/users/edit/([0-9]+)/$', 'jframe\admin\Users.edit'),
    array('/users/delete/([0-9]+)/$', 'jframe\admin\Users.delete'),
    array('/users/add/$', 'jframe\admin\Users.add'),
    array('/users/reset/([0-9]+)/$', 'jframe\admin\Users.reset'),

    array('/departments/$', 'jframe\admin\Departments.index'),
    array('/departments/edit/([0-9]+)/$', 'jframe\admin\Departments.edit'),
    array('/departments/delete/([0-9]+)/$', 'jframe\admin\Departments.delete'),
    array('/departments/add/$', 'jframe\admin\Departments.add'),
);
?>