<?php
namespace jframe\admin;
use Controller, TemplateResponse, User, RedirectResponse, JSONResponse, Error404Response, Auth;
class UsersController extends Controller
{

    public function index()
    {
        @login_required();

        $users = User::find('all', array('order' => 'id DESC'));
        return new TemplateResponse('admin/users/index', array( 'users' => $users ));
    }   

    public function add()
    {
        @login_required();
        /**
         * Do we have post data?
         */
        if( $_POST ) 
        {
            /**
             * Let's validate/sanitize the post data.
             * Needs to be streamlined in some way.
             */
            $_POST['username'] = strip_tags($_POST['username']);
            $_POST['email'] = strip_tags($_POST['email']);
            $_POST['type'] = intval($_POST['type']);
            $_POST['status'] = intval($_POST['status']);
            $_POST['password'] = strip_tags($_POST['password']);
            $_POST['confirm'] = strip_tags($_POST['confirm']);

            $user = new User();
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->type = $_POST['type'];
            $user->status = $_POST['status'];

            /**
             * If new user we need to set a password.
             */
            if( ! $user->id && $_POST['password'] && $_POST['confirm'] && $_POST['password'] == $_POST['confirm'] )
            {
                $user->password = Auth::encrypt($_POST['password']);
            }
            else
            {
                send_message('error', "Passswords do not match. Correct the mistake and try again.");
                return new TemplateResponse('admin/users/add', array('user' => $user));
            }



            $user->save();

            send_message('success', "User was successfully created.");

            return new RedirectResponse('admin\Users.index');
        }else{
            /**
             * No post data so show the add form.
             */
            return new TemplateResponse('admin/users/add');
        }
    }

    public function edit($user_id)
    {
        @login_required();

        $user = User::find($user_id);

        if( $_POST ) 
        {
            $_POST['username'] = strip_tags($_POST['username']);
            $_POST['email'] = strip_tags($_POST['email']);
            $_POST['type'] = intval($_POST['type']);
            $_POST['status'] = intval($_POST['status']);

            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->type = $_POST['type'];
            $user->status = $_POST['status'];

            $user->save();

            send_message('success', "Term was successfully updated.");

            return new RedirectResponse('admin\users.index');
        }
        else
        { 
            return new TemplateResponse('admin/users/edit', array('user' => $user));
        }
    }

    public function reset($user_id)
    {
        if( $_SERVER['REQUEST_METHOD'] !== "PUT") 
        {
            return new Error404Response();
        }
        if( Auth::is_logged_in() ) 
        {
            if( Auth::user('type') <= 1 )
            {
                $password = substr(md5(time(true)), -12, 10);
                $user = User::find($user_id);

                if( $user->type == 0 && Auth::user('type') != 0 )
                {
                    return new JSONResponse(array( 'status' => 'fail', 'message' => 'You do not have permission to reset a super admin user password!'));
                }

                $user->password = Auth::encrypt($password); 
                $user->save();

                return new JSONResponse(array( 'status' => 'success', 'password' => $password ));
            }
            else
            {
                return new JSONResponse(array( 'status' => 'fail', 'message' => 'You do not have permission to reset passwords!'));
            }
        }
        else
        {
            return new JSONResponse(array( 'status' => 'fail', 'message' => 'You are not logged in!'));
        }
    }
}
?>