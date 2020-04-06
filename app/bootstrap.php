<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 17:58
 */
use Core\Frame\Http\Response;
try {
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

    define('LOGS_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR);

    define('CORE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);

    define('CONFIG_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);


    include CONFIG_PATH . 'autoload.php';

    $di = include CONFIG_PATH . 'service.php';

    $route = include CORE_PATH . '/frame/autoload.php';

    $route->after(function () use ($route){
        $response = new Response();
        $response->setContent($route->dispatch());
    });

} catch (Exception $exception) {
    echo $exception->getMessage();
}