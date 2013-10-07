<?php
if (strpos(get_include_path(), 'private') === false)
    set_include_path('/web/www/private'.PATH_SEPARATOR.get_include_path());

use WinkForm\Form;

require_once 'simpletest/autorun.php';
require_once 'models/TestForm.php';

class TestsForm extends UnitTestCase
{
    
    /**
     * test construction
     */
    public function testCreation()
    {
        $form = new TestForm();
        $this->assertTrue($form instanceof Form);
    }
    
    /**
     * test setting of enctype
     */
    public function testEnctype()
    {
        $form = new TestForm();
        
        $this->assertTrue($form->getEnctype() == Form::ENCTYPE_DEFAULT);
        
        // upon rendering the form head all objects are checked for file input objects and the enctype is set
        $form->render();
        
        $this->assertEqual($form->getEnctype(), Form::ENCTYPE_FILE);
    }
}
