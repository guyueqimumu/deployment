<?php

namespace Core\Frame\route;

use Core\Frame\Container\Injection;

class Restful extends RouteBase
{


    public function dispatch()
    {
        $version = $this->load();
        if ($version) {
            return $version;
        }
    }


    public static function getApp()
    {
        return new class
        {
            const ALLOW_METHOD = ["GET", "POST", "PUT", "DELETE", "OPTIONS"];

            public $methods = [];

           public function __call($name, $arguments)
           {
               if(in_array(strtoupper($name),self::ALLOW_METHOD)){

                   if(is_callable($arguments[1])){
                      call_user_func($arguments[1]);
                  }
               }
           }
        };
    }

    static function getInstance(Injection $di)
    {
        $instance = new self($di);
        $instance->dispatch();
        return $instance;
    }

}