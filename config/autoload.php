<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 14:27
 */

spl_autoload_register(function ($class) {
    var_dump($class);
    $filePath = str_replace(["\\"], ["/"], ROOT_PATH . $class . '.php');
    if (file_exists($filePath)) {
        include_once $filePath;
    }
});