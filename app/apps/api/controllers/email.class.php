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

            if( empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Some fields were left blank, check it out and try again please.'
                ), 400);
            }

            if( ! strstr($_POST['email'], '@') || ! strstr($_POST['email'], '.')) 
            {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Hey buddy: Please use a normal email format this time ok?'
                ), 400);
            }

            if ( ! $_POST['message'] && strlen($_POST['message']) > 5 ) {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Yo friend: Try a sentence this time and send again.'
                ), 400);
            }

            if ( ! empty($_POST['cartoon']) ) {
                return new JSONResponse(array(
                    'status' => 'error',
                    'message' => 'Hmmm kinda thinkin&rsquo; you are a bot..soooo--NOPE!'
                ), 400);
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
            ), 201);
        } else {
            return new JSONResponse(array( 
                'status' => 'error', 
                'message' => "Hey just what are you trying to pull here?!"
            ), 400);
        }
    }

    private function concat($arr) {
        return implode("\r\n", $arr);
    }

}
?>