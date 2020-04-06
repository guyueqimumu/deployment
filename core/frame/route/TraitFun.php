<?php

namespace Core\Frame\route;

trait TraitFun
{
    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 19:05
     * @param callable $callback
     * @return mixed
     */
    public function notFind(callable $callback)
    {
        return call_user_func($callback);
    }

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 19:05
     * @param callable $callback
     * @return mixed
     */
    public function notFindAction(callable $callback)
    {
        return call_user_func($callback);
    }

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 19:06
     * @param callable $callback
     * @return mixed
     */
    public function before(callable $callback)
    {
        return call_user_func($callback);
    }

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 19:06
     * @param callable $callback
     * @return mixed
     */
    public function after(callable $callback)
    {
        return call_user_func($callback);
    }

    /**
     * Notes:
     * User: QiLin
     * Date: 2020/4/4 0004
     * Time: 22:11
     * @return string
     */
    public function welcomeToUse(): string
    {
        $json = [
            'code' => '200',
            'data' => [],
            'status' => 'success',
            'message' => '成功',
            'version' => '1.0',
            'time' => date("Y-m-d H:i:s"),
        ];
        return json_encode($json);
    }
}