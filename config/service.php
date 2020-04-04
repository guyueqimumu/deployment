<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 11:45
 */

use Core\Container\Injection;
$di = new Injection();
$di->set('db',function (){
    return new \Core\db\Adaptee\Pdo([]);
});

return $di;