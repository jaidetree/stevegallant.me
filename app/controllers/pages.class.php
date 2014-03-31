<?php
use jframe\APP;
class PagesController extends Controller
{
    /**
     * This is a view. It runs our business logic. Like getting data from a 
     * database then sends it to a template file in our views folder
     * to render to the user.
     */
    function home()
    {

        return new TemplateResponse("app");
    }        
}
?>
