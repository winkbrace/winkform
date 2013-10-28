<?php namespace WinkForm\Support;

/**
 * WinkForm
 * Created by Bas de Ruiter
 * Date: 10-10-2013
 */
interface ObserverInterface
{
    /**
     * update all observers of given subject
     * @param ObserverSubject $subject
     */
    public function update(ObserverSubject $subject);
}
