<?php

return [
    //发布目录
    'distPath' => '/data/wwwroot/backend.insurance.genius',
    //发布版本
    'version' => '',
    //仓库地址
    'repo' => 'ssh://qilin@repo.janfish.cn:29418/backend.insurance.genius.git',
    //是否需要重启supervisord服务
    'reloadSupervisor' => [],
    //不需要被同步到发布目录的文件
    'excludeSyncFiles' => [],
    //需要清除的文件(发布目录的相对路径)
    'clean' => [
        'platform/public/static'
    ],
    //需要创建的文件(发布目录的相对路径)
    'makeDir' => [

    ],
    //修改文件或者文件夹的权限的配置
    'chmod' => [
        'logs' => '777',
        'cli/run' => '777',
        'api/public/files' => '777',
        'core/vendor/aip-php-sdk/Runtime' => '777',
    ],
    //执行动态配置的模版和目标文件
    'configFiles' => [
        'configs/config.template.php' => 'configs/dev.php',
    ],
    //执行动态配置的变量 通过 @@VARIABLE_NAME@@格式在配置模版中使用
    'variables' => [
        'BASE_URL' => 'api.dev.insurance.xy.cn',
        'PINGAN_API' => 'http://api.pingan.xy.cn',
        'PICC_API' => 'http://api.picc.xy.cn',
        'JINTAI_API' => 'http://api.jintai.xy.cn/jintai',
        'DB_NAME' => 'car_insurance_genius_v2',
        'COMPANYKEY' => '1bbd58b46c2fd564190de4099d4af141',
        'HOST' => 'open.tax.xy.cn',
        'APP_KEY' => '78f9b4fad3481fbce1df0b30eee58577',
        'DES_3KEY' => '123456788765432112345678',
        'SIGN_TYPE' => 'sha256',
    ],
    //部署程序日志记录位置
    'logPath'=>LOGS_PATH,
    //临时仓库地址
    'tmpFolder'=>ROOT_PATH,
];