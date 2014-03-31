<?php
namespace jframe\testing;
use jframe\APP;
use jframe\forms\BaseForm;

class TestForm extends BaseForm
{
   var $name = array('TextField', array('label' => 'Name')); 
   var $name2 = array('TextField', array('label' => 'Name 2')); 
}

class FormsTest extends Test 
{
    private $form = null;
    protected $end_on_error = true;

    public function test_constructor()
    {
        $this->should_pass(class_exists('jframe\forms\BaseForm'), 
            "BaseForm class should exist.");
        $this->should_pass(class_exists('jframe\testing\TestForm'), 
            "TestForm class should exist.");
        $this->form = new TestForm();
        $this->should_pass(is_a($this->form, 'jframe\forms\BaseForm'), "Form should be a BaseForm object.");
    }

    public function test_fields()
    {
        $this->should_pass( is_a( $this->form->fields(), 'ArrayIterator' ) 
            && count($this->form->fields()->count()) > 0, 
            "Form should have fields and more than 0.");
    }

    public function test_output()
    {
        $this->should_pass(
            is_callable(array( $this->form, 'format')), 
            "Form should have a format method.");
        $html = $this->form->as_html();
        $this->output( htmlentities($html) );

        $this->should_pass( strlen($html) > 1, 
            "Form HTML should not be empty.");
        $this->should_pass(
            preg_match('@(<([\w]+)[^>]*>)@U', $html) === 1,
            "Form HTML should contain at least one tag.");
        $this->assert_equal(array($html, '<ul>
<li>
<label>Name</label>
<input label="Name" name="name" type="text" id="id_name"/>
</li>

<li>
<label>Name 2</label>
<input label="Name 2" name="name2" type="text" id="id_name2"/>
</li>
</ul>'));
    }
}    
APP::modules()->tester->register_test('Form Test', new FormsTest());
?>