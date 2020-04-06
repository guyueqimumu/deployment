<?php
namespace  Core\Frame\Http;
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 11:39
 */
trait HttpBase
{

    public $errorMsg = '';

    public $code = 200;

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    public function getErrorCode()
    {
        return $this->errorMsg;
    }
}

