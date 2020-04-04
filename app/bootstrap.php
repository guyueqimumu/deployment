<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 17:58
 */

try {
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

    define('CORE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);

    define('CONFIG_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);

    include CONFIG_PATH . 'autoload.php';

    include CONFIG_PATH . 'route.php';

    $di = include CONFIG_PATH . 'service.php';
    $route = new Route();
    $route->setDi($di);
    $route->after($route->handle());

} catch (Exception $exception) {

}