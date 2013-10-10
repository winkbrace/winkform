<?php namespace WinkForm\Support;
/**
 * WinkForm
 * Created by Bas de Ruiter
 * Date: 10-10-2013
 */
abstract class ObserverSubject
{
    /**
     * @var ObserverInterface[]
     */
    protected $observers = array();

    /**
     * Add observer to the subject
     * @param ObserverInterface $observer
     */
    public function attachObserver(ObserverInterface $observer)
    {
        array_push($this->observers, $observer);
    }

    /**
     * Remove observer from the subject
     * @param ObserverInterface $observer
     */
    public function detachObserver(ObserverInterface $observer)
    {
        foreach ($this->observers as $i => $obs)
        {
            if ($obs == $observer)
                array_splice($this->observers, $i, 1);
        }
    }

    /**
     * Notify all registered observers of the current state
     */
    protected function notify()
    {
        foreach ($this->observers as $observer)
        {
            $observer->update($this);
        }
    }
}
