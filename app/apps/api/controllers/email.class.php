<?php
namespace jframe\api;
use APP, Controller, JSONResponse;

class EmailController extends Controller
{
    public function create()
    {
        /**
         * Do we have post data?
         */
        if( $_POST ) 
        {
            /**
             * Let's validate/sanitize the post data.
             * Needs to be streamlined in some way.
             */
            $_POST['name'] = strip_tags($_POST['name']);
            $_POST['email'] = strip_tags($_POST['email']);
            $_POST['message'] = strip_tags($_POST['message']);


            if( ! strstr($_POST['email'], '@') || ! strstr($_POST['email'], '.')) 
            {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Hey buddy: Please use a normal email format this time ok?'
                ));
            }

            if ( ! $_POST['message'] && strlen($_POST['message']) > 5 ) {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Yo friend: Try a sentence this time and send again.'
                ));
            }

            if ( ! empty($_POST['cartoon']) ) {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Hmmm kinda thinkin&rsquo; you are a bot..soooo--NOPE!'
                ));
            }

            $to = 'steve@stevegallant.me';

            $subject = "Incoming Transmission: Portfolio Contact Form";


            $message = array(
                            $_POST['name'] . " <" . $_POST['email'] . "> wrote,",
                            $_POST['message'],
                            "Sincerely,",
                            "Your Portfolio Site",
                        );

            $headers = array(
                            'From: ' . $_POST['email'],
                            'Reply-To: ' . $_POST['email'],
                            'X-Mailer: PHP/' . phpversion(),
                        );

            mail($to, $subject, $this->concat($message), $this->concat($headers));

            return new JSONResponse(array( 
                'status' => 'success', 
                'message' => "Your message was sent and I'll get back to you when I can. Thanks!"
            ));
        } else {
            return new JSONResponse(array( 
                'status' => 'error', 
                'message' => "Hey just what are you trying to pull here?!"
            ));
        }
    }

    private function concat($arr) {
        return implode("\r\n", $arr);
    }

}
?>