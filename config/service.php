<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 11:45
 */

use Core\Frame\Container\Injection;
$di = new Injection();
$di->set('db',function (){
    return new \Core\Frame\db\Adaptee\Pdo([]);
});

return $di;