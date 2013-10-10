<?php namespace WinkForm\Support;
/**
 * WinkForm
 * Created by Bas de Ruiter
 * Date: 10-10-2013
 */
interface ObserverInterface
{
    function update(ObserverSubject $subject);
}
