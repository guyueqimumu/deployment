<?php
namespace Core\db;
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 11:35
 */
interface TargetInterface
{
    /**
     * Author:QiLin
     * @throws \Exception
     */
    public function connect();

    public function close();

    public function query(string $sql);

    public function fetchAll();

    public function fetchOne();
}