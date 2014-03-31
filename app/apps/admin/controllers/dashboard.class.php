<?php
namespace jframe\admin;
use jframe\APP, Controller, RedirectResponse, TemplateResponse;

class Dashboard extends Controller
{
    function index()
    {
        @login_required();
        return new TemplateResponse('admin/dashboard/index.html');
    }

    function update_kendall_logo()
    {
        @login_required();      
        move_uploaded_file($_FILES['kendall_logo']['tmp_name'], APP::dir('app') . 'static/uploads/logos/kendall_college_logo.png');
        return new RedirectResponse('jframe\admin\Dashboard.index');
    }
}
?>