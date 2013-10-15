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
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
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
