<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/3/31
 * Time: 18:02
 */

namespace Core\Frame\db;


/**
 * @property TargetInterface $obj
 * Author:QiLin
 * Class Adapter
 * @package Core\db
 */
class Adapter implements TargetInterface
{
    public $obj;

    public function __construct($config=[])
    {
        $adapter = $config['Adapter'];
        $class="Core\\Frame\\db\\Adaptee\\".$adapter;
        $this->obj = new $class($config);
    }

    /**
     * Author:QiLin
     * @throws \Exception
     */
    public function connect()
    {
        return $this->obj->connect();
    }

    public function close()
    {
        return $this->obj->close();
    }

    public function query(string $sql)
    {
        return $this->obj->query($sql);
    }

    public function fetchAll()
    {
        return $this->obj->fetchAll();
    }

    public function fetchOne()
    {
        return $this->obj->fetchOne();
    }

}
