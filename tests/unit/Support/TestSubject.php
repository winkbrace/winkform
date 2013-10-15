<?php
/**
 * Class to test ObserverSubject
 * TestSubject.php
 * Part of WinkForm
 * Created by Bas de Ruiter
 * Date: 10-10-2013
 */
class TestSubject extends \WinkForm\Support\ObserverSubject
{
    protected $attributes;

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        $this->notify();
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

}
