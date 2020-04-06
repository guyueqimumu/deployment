<?php

$route = new \Core\Frame\route\Mvc($di);
try {
    $pathInfo = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    if (isset($pathInfo[0]) and $pathInfo[0]) {
        $filename = sprintf('%s' . 'controllers/%sController.php', ROOT_PATH, ucfirst($pathInfo[0]));
        $filename = str_replace(["\\"], ["/"], $filename);
        if (!file_exists($filename)) {
            throw new \Exception("控制器文件不存在", 404);
        }
        if (!include_once $filename) {
            throw new \Exception("加载控制器文件失败");
        }
    }else {
        $route->setEvent('welcomeToUse');
        die();
        return $route->welcomeToUse();
    }
} catch (\Exception $exception) {
    if ($exception->getCode() == 404) {
        $route->setEvent();
        return $route->notFind(function () {
        });
    }
    throw  $exception;
}
return $route;