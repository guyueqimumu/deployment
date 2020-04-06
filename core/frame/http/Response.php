<?php

namespace Core\Frame\Http;
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 11:39
 */
class Response
{
    use HttpBase;

    public function success()
    {

    }

    public function setHeader()
    {
        header("Content-type:application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Credentials:true");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers:GET, Access-Token");
    }

    /**
     * 设置文本类容
     * Author:QiLin
     * @param $data
     */
    public function setContent($data)
    {
        $this->setHeader();
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            $jsonp = $_GET['callback'] ?? '';
            echo ($jsonp) ? $jsonp . '(' . $data . ')' : $data;
            die();
        } else {
            echo $data;
            die();
        }
    }
}