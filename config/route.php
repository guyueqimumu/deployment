<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 11:45
 */

use Core\Container\DiInterface;
use Core\Container\Injection;
use Core\Http\Response;

class Route implements DiInterface
{
    public $di;

    public function getDi()
    {
        return $this->di;
    }

    public function setDi(Injection $di)
    {
        $this->di = $di;
    }

    public $pathInfo = '';


    public function __construct()
    {
        $this->pathInfo = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    }


    /**
     * Author:QiLin
     */
    public function handle()
    {
        try {
            $version = $this->load();
            if ($version) {
                return $version;
            }
            $controller = $this->pathInfo[0] . 'Controller';
            $action = $this->pathInfo[1];
            $namespaceName = "Controllers\\" . $controller;
            $obj = new $namespaceName();
            $obj->setDi($this->di);
            $obj->before();
            return $obj->$action();
        } catch (Exception $exception) {
            if ($exception->getCode() == 404) {
                return $this->notFind();
            }
            throw  $exception;
        }
    }

    /**
     * Author:QiLin
     * @return array
     * @throws Exception
     */
    protected function load()
    {
        if (isset($this->pathInfo[0]) and $this->pathInfo[0]) {
            $filename = sprintf('%s' . 'controllers/%sController.php', ROOT_PATH, ucfirst($this->pathInfo[0]));
            $filename = str_replace(["\\"], ["/"], $filename);
            if (!file_exists($filename)) {
                throw new Exception("控制器文件不存在", 404);
            }
            if (!include_once $filename) {
                throw new Exception("加载控制器文件失败");
            }
            return [];
        } else {
            return $this->getVersion();
        }
    }

    /**
     * Author:QiLin
     * @return array
     */
    public function getVersion(): array
    {
        return [
            'code' => '200',
            'data' => [],
            'status' => 'success',
            'message' => '成功',
            'version' => '1.0',
            'time' => date("Y-m-d H:i:s"),
        ];
    }

    public function notFind()
    {
        return [
            'code' => '404',
            'data' => [],
            'status' => 'not find',
            'message' => '',
            'time' => date("Y-m-d H:i:s"),
        ];
    }


    /**
     * Author:QiLin
     */
    public function before()
    {

    }

    public function after($result)
    {
        $response = new Response();
        $response->setContent($result);
    }
}
