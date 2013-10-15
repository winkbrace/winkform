<?php
use WinkForm\Support\ObserverSubject;

/**
 * Class to test ObserverInterface
 * TestObserver.php
 * Part of WinkForm
 * Created by Bas de Ruiter
 * Date: 10-10-2013
 */
class TestObserver implements \WinkForm\Support\ObserverInterface
{
    protected $attributes;

    /**
     * @param ObserverSubject $subject
     */
    function update(ObserverSubject $subject)
    {
        $this->attributes = $subject->getAttributes();
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

}
