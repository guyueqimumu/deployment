<?php

namespace Controllers;

use Core\Container\DiInterface;
use Core\Container\Injection;

/**
 * @property \Core\Container\Injection $di
 * @property \Core\db\TargetInterface $db
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 15:06
 */
class BaseController implements DiInterface
{
    public $di;

    public $db;

    public function getDi()
    {
        return $this->di;
    }

    public function setDi(Injection $di)
    {
        $this->di = $di;
    }

    /**
     * Author:QiLin
     * @return mixed
     * @throws \Exception
     */
    public function getDb()
    {
        return $this->db = $this->di->get('db');
    }

    public function before()
    {
        $this->getDb();
    }

}
