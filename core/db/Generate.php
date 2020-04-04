<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/2
 * Time: 10:14
 */

namespace Core\db;

/**
 * @property TargetInterface $db
 * Author:QiLin
 * Class Generate
 * @package Core\db
 */
class Generate
{
    public $db;

    /**
     * Generate constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $adapter = new Adapter($config);
        $this->db = $adapter->connect();
    }

    public function getTables()
    {
        $res = $this->db->query("SHOW TABLES");
        if (empty($res)) {
            return [];
        }
        $field = "Tables_in_" . $this->db->dbName;
        return array_column($res, $field);
    }

    public function getTableStructure(string $tableName)
    {
        $res = $this->db->query("DESC `{$tableName}`");
        return $res;
    }

    public function create(string $tableName)
    {
        $desc = $this->getTableStructure($tableName);
        $className = ucwords(str_replace(["_"], [], $tableName));
        $tr = "<?php" . PHP_EOL;
        $tr .= "class{" . PHP_EOL;
        foreach ($desc as $k => $v) {
            if (isset($v['Default']) && $v['Default']) {
                $tr .= 'public $' . $v['Field'] . "='" . $v['Default'] . "';" . PHP_EOL;
            } else {
                $tr .= 'public $' . $v['Field'] . ';' . PHP_EOL;
            }
        }
        $tr .= "}";
        echo $tr;
        $a= <<<EOF
        
EOF;

        die();
    }
}