<?php
class StandardAuth extends AuthDriver
{
    private $valid_session = false;

    public function __construct()
    {
        session_start();
    }

    public function start()
    {
        $this->valid_session = $this->validate_session();
    }

    public function is_logged_in()
    { 
        if($this->valid_session)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function validate_session()
    {
        $bcrypt = new Bcrypt(5);

        $token = $_SESSION['user']['token'];
        $time = $_SESSION['user']['time'];

        if($bcrypt->verify('tiktok' . $time, $token))
        {
            return true;
        }
        else
        {
            unset($_SESSION['user']);
            return false;
        }
    }

    public function login($name, $password)
    {
        $bcrypt = new Bcrypt(15);
        $user = User::find('first', array(
            'conditions' => array( 
                "LCASE(`username`) = LCASE(?) OR LCASE(`email`) = LCASE(?)", 
                $name, 
                $name))
        );

        if(! $user)
        {

            /**
             * That email or username has no match
             * That's probably bad...
             */
            send_message('error', 'Username or email not found in our database.');
            Context::response(new TemplateResponse('admin/account/login', array( 
                'status' => 'error' 
            )));
            return false;
        }

        if( $bcrypt->verify($password, $user->password) )
        {
            /**
             * We are logged in!
             */
            $this->create_session($user);
            return true;
        }
        else 
        {
            /**
             * Passwords didn't match.
             */
            send_message('error', 'Password does not match.');
            Context::response(new TemplateResponse('admin/account/login', array( 
                'status' => 'error' 
            )));
            return false;
        }
    }


    public function logout()
    {
        unset($_SESSION['user']);
        send_message('info', 'You are now logged out.');
        Context::response(new RedirectResponse('admin\accounts.login'));
    }

    public function get_current_user()
    { 
        if($this->valid_session)
        {
            return User::find($_SESSION['user']['id']);
        }
        else
        {
            /**
             * Might be good to put anonymous data here as a
             * placeholder.
             */
            return new stdClass();
        }
    }

    public function encrypt($password)
    {
        $bcrypt = new BCrypt(15);
        return $bcrypt->hash($password);
    }

    private function create_session($user)
    {
        /**
         * I can't gurantee security with this but
         * it seemed like a really good idea at the time.
         * We make a hash of the time the session was created
         * and this way we can compare it to know it's a valid
         * session generated from this framework.
         */

        $time = time();

        $bcrypt = new Bcrypt(5);
        $hash = $bcrypt->hash('tiktok' . $time);

        /**
         * Store our session data
         */
        $_SESSION['user'] = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'time' => $time,
            'token' => $hash
        );
    }
}
?>
