<?php

namespace Core\Container;

/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 17:17
 */
class Injection
{
    public $container = [];

    public $shareContainer = [];

    /**
     * Author:QiLin
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function get(string $name)
    {
        if (isset($this->container[$name])) {
            $definition = $this->container[$name];
        } else if ($this->shareContainer[$name]) {
            $definition = $this->shareContainer[$name];
        } else {
            throw new \Exception("Service '" . $name . "' wasn't found in the dependency injection container");
        }
        if (is_object($definition)) {
            if ($definition instanceof DiInterface) {
                $definition->setDI($this);
            }
            if (is_callable($definition)) {
                $definition = call_user_func($definition);
            }
        }
        return $definition;
    }

    /**
     * Author:QiLin
     * @param string $name
     * @param $definition
     */
    public function set(string $name, $definition)
    {
        if (!isset($this->container[$name])) {
            $this->container[$name] = $definition;
        }
    }

    /**
     * 设置共享注入
     * Author:QiLin
     * @param string $name
     * @param $definition
     */
    public function setShare(string $name, $definition)
    {
        $this->shareContainer[$name] = $definition;
    }
}
