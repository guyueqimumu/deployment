<?php

namespace Core\Frame\Route\Events;
class EventsInstance
{
    use Event;

    public $events = [];

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 22:31
     * @param string $event
     * @param callable|null $callback
     * @throws \ReflectionException
     */
    public function setEvent(string $event, callable $callback = null)
    {
        $eventsInstance = new \ReflectionClass($this);
        if ($eventsInstance->hasMethod($event)) {
            $this->events[$event] = $callback;
            return;
        };
        throw new \Exception('Event callback not exist');
    }

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 22:35
     * @param string $event
     * @return mixed|null
     */
    public function getEvent(string $event)
    {
        return $this->events[$event] ?? null;

    }
}
