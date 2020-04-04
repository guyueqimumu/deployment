<?php

namespace Core\db\Adaptee;

use Core\db\TargetInterface;

/**
 * @property \PDO $db
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/1
 * Time: 15:52
 */
class Pdo implements TargetInterface
{
    public $ip = '127.0.0.1';
    public $port = '3306';
    public $username = 'root';
    public $password = 'root';
    public $dbName = 'car_insurance_genius_v2_prod';
    public $schema = 'mysql';
    public $con;

    public function __construct(array $option = [])
    {

    }

    /**
     * Author:QiLin
     * @throws \Exception
     */
    public function connect()
    {
        if ($this->con) {
            return $this->con;
        }
        try {
            $dsn = "{$this->schema}:host={$this->ip};dbname={$this->dbName}";
            $this->con = new \PDO($dsn, $this->username, $this->password);
        } catch (\PDOException $exception) {
            throw  new \Exception("Error!: " . $exception->getMessage(), $exception->getCode());
        }
        return $this;
    }

    public function close()
    {

    }

    public function query(string $sql)
    {
        $data = [];
        foreach ($this->con->query($sql) as $row) {
            $node = [];
            foreach ($row as $k => $value) {
                if (is_string($k)) {
                    $node[$k] = $value;
                }
            }
            $data[] = $node;
        }
        return $data;
    }

    public function fetchAll()
    {
        // TODO: Implement fetchAll() method.
    }

    public function fetchOne()
    {
        // TODO: Implement fetchOne() method.
    }

    public function setSchema()
    {

    }

}