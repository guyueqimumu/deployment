<?php

namespace Core\db;
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/4/2
 * Time: 14:12
 */
class Snippet
{
    public function getClass(string $namespace,string $useDefinition,string $classDoc = '', string $abstract = '',$modelOptions,string $extends = '',string $content, string $license = '')
    {
        $templateCode = <<<EOD
<?php
%s%s%s%s%sclass %s extends %s
{
%s
}
EOD;
        return sprintf(
                $templateCode,
                $license,
                $namespace,
                $useDefinition,
                $classDoc,
                $abstract,
                $modelOptions->getOption('className'),
                $extends,
                $content)
            . PHP_EOL;
    }


}