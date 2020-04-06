<?php

namespace Core\Frame\Container;
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 17:20
 */
interface DiInterface
{
    public function getDi();

    public function setDi(Injection $di);
}