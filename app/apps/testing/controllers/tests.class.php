<?php
namespace jframe\testing;
use jframe\APP as APP;
use jframe\bootstrap\DirectoryCrawler,
    jframe\bootstrap\FileIncluder;
use \Controller, \HTML, \HTMLResponse, \RedirectResponse, \ErrorException;

class TestsController extends Controller
{
    public function __construct()
    {
        set_error_handler(array($this, 'error_handler'), E_ALL & ~E_NOTICE);
        set_exception_handler(array($this, 'exception_handler'));
        $current_dir = dirname(dirname(__FILE__));

        new FileIncluder(
            new DirectoryCrawler($current_dir . '/modules/types', '\.class\.php$')
        );

        $this->load_tests();
    }

    public function error_handler($errno, $errstr, $errfile, $errline ) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    public function exception_handler($exception) {
        echo "Error: " . $exception->getMessage() . "\n";
    }

    public function load_tests()
    {
        $dir = new DirectoryCrawler(
            APP::dir('app'), 
            '^(.*)/tests/(.*)\.class\.php$',
            true,
            3
        );

        new FileIncluder($dir);
    }

    public function index()
    {
        login_required();

        $html = HTML::div();
        $ul = HTML::ul();
        $page = HTML::div(array( 'class' => 'page', 'style' => 'margin: 1em;' ));

        $ul->style = "margin: 1em;";

        foreach( APP::modules()->tester->get() as $name=>$item )
        {
            $object = $item->object;
            $test = HTML::li(HTML::a(array(
                'href' => APP::url('jframe\testing\Tests.run', array( slugify($name) )),
            ), $item->name ));

            $ul->insert($test);
        }

        $html->insert(HTML::h1('Tests'));
        $html->insert(render('_status'));

        if( $_SESSION['test_verbose'] )
        {
            $verbose = "on";
        }
        else
        {
            $verbose = "off";
        }

        $a = HTML::a(array( 'href' => APP::url('jframe\testing\Tests.verbose') ), 'Toggle Verbose Mode');

        $page->insert(HTML::p('Verbose mode is ' . $verbose . ' ' . $a));
        $page->insert(HTML::p('Run any of the following tests:'));
        $page->insert($ul);
        $html->insert($page);

        echo new HTMLResponse($html);
    }

    public function run($class_name)
    {
        login_required();

        foreach( APP::modules()->tester->get() as $test)
        {
            if( slugify(get_base_class($test->object)) == $class_name )
            {
                new TestRunner($test->object, $test->name, $_SESSION['test_verbose']);
            }
        }
    }

    public function verbose()
    {
        login_required();

        if( ! $_SESSION['test_verbose'] )
        {
            $_SESSION['test_verbose'] = true;
            send_message('status', 'Verbose mode activated.');
        }
        else
        {
            $_SESSION['test_verbose'] = false;
            send_message('status', 'Verbose mode deactivated.');
        }
        $url = APP::url('jframe\testing\Tests.index');
        return new RedirectResponse($url);
    }
}?>