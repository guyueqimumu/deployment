<?php

namespace Core\Frame\route;


use Core\Frame\Container\Injection;
use Core\Frame\Route\Events\EventsInstance;

class Mvc extends RouteBase
{
    use TraitFun;

    public $di;

    public $events = [];

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 19:04
     * @return array|string|void
     * @throws \Exception
     */
    public function dispatch()
    {
        try {
            $controller = $this->pathInfo[0] . 'Controller';
            $action = $this->pathInfo[1];
            $namespaceName = "Controllers\\" . $controller;
            $rc = new \ReflectionClass($namespaceName);
            if (!$rc->hasMethod($action)) {
                return $this->notFindAction(function () {
                });
            }
            //中间件处理

            $instance = $rc->newInstance();
            $instance->setDi($this->di);
            $instance->before();
            return $instance->$action();
        } catch (\Exception $exception) {
            if ($exception->getCode() == 404) {
                return $this->notFind(function () {
                });
            }
            throw  $exception;
        }
    }



    public function eventCallbacks()
    {

    }

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 21:29
     * @param Injection $di
     * @return Mvc
     * @throws \Exception
     */
    static function getInstance(Injection $di)
    {
        $instance = new self($di);
        $instance->dispatch();
        return $instance;
    }

}
