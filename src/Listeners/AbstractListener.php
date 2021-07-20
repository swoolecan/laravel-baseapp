<?php

namespace Framework\Baseapp\Listeners;

use App\Events\OrderSavingEvent;
use App\Events\ExampleEvent;
 
class AbstractListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
 
    /**
     * Handle the event.
     *
     * @param  OrderSavingEvent  $event
     * @return void
     */
    /*public function handle(OrderSavingEvent $event)
    {
        info($event->order);
    }*/
}
