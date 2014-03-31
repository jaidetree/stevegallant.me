<?php
namespace jframe\admin;
use RedirectResponse, TemplateResponse, Auth, Controller;

class AccountsController extends Controller 
{
    function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];


        if( Auth::is_logged_in() ) 
        {
            send_message('status', "You are already logged in.");
            return new RedirectResponse('admin\Dashboard.index');
        }
        if( ( ! $username or ! $password ) and ! Auth::is_logged_in() ) 
        {
            return new TemplateResponse('admin/account/login');
        }
        else
        {
            if( Auth::login($username, $password) )
            {
                return new RedirectResponse('admin\Dashboard.index');
            }
            else
            {
                send_message('error', "Account email/password did not match.");
                return new TemplateResponse('admin/account/login');
            }
        }
    }
    function logout()
    {
        Auth::logout();
    }
}
?>