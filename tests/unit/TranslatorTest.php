<?php
use Codeception\Util\Stub;

class TranslateTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Illuminate\Translation\Translator
     */
    protected $trans;

    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    protected function _before()
    {
        $this->trans = \WinkForm\Translation\Translator::getInstance();
    }

    protected function _after()
    {
    }

    /**
     * test that it works
     */
    public function testCreation()
    {
        $this->assertEquals('postal-code', $this->trans->get('inputs.postal-code'));

        // non-existing translation should return key
        $this->assertEquals('dummy.value', $this->trans->get('dummy.value'));
    }

    /**
     * test changing locale should fetch from other language file
     */
    public function testOtherLocale()
    {
        $this->trans->setLocale('nl');
        $this->assertEquals('postcode', $this->trans->get('inputs.postal-code'));
    }

}
