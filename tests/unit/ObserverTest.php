<?php
use Codeception\Util\Stub;

require_once 'Support/TestObserver.php';
require_once 'Support/TestSubject.php';

class ObserverTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     *
     */
    protected function _before()
    {
    }

    /**
     *
     */
    protected function _after()
    {
    }

    /**
     *
     */
    public function testObserver()
    {
        // create the concrete objects
        $observer = new TestObserver();
        $subject = new TestSubject();

        // init attributes. Notify() will be called and should not throw an exception,
        // even though there are no observers registered yet.
        $subject->setAttributes(array('one', 'two'));

        // attach observer to subject
        $subject->attachObserver($observer);

        // change state of subject
        $newAttributes = array('foo' => 'bar');
        $subject->setAttributes($newAttributes);

        // observer should now have the same state
        $this->assertEquals($newAttributes, $observer->getAttributes());

        // TODO add another observer
        // TODO remove an observer
    }

}
