<?php

namespace Core\Frame\route;

use Core\Frame\Container\DiInterface;
use Core\Frame\Container\Injection;

abstract class RouteBase implements DiInterface
{
    public $pathInfo;
    public $method;
    const ALLOW_METHOD = ["GET", "POST", "PUT", "DELETE", "OPTIONS"];
    public $di;

    public function __construct(Injection $di)
    {
        $this->pathInfo = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->di = $di;
    }


    public function getDi()
    {
        return $this->di;
    }

    public function setDi(Injection $di)
    {
        $this->di = $di;
    }

    abstract function dispatch();

    abstract static function getInstance(Injection $di);

}