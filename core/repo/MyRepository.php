<?php

namespace Core\Repo;


class MyRepository
{
    /**
     * 发布目录
     * Author:QiLin
     * @var string
     */
    public $distPath;
    /**
     * 仓库地址
     * Author:QiLin
     * @var mixed
     */
    public $repo;
    /**
     * 版本
     * Author:QiLin
     * @var
     */
    public $version;
    /**
     * 需要创建的目录
     * Author:QiLin
     * @var
     */
    public $makeDir;
    /**
     * 需要配置的权限
     * Author:QiLin
     * @var mixed
     */
    public $chmod;

    /**
     * Author:QiLin
     * @var mixed
     */
    public $variables;
    /**
     * 配置文件
     * Author:QiLin
     * @var mixed
     */
    public $configFiles;
    /**
     * 日志路径
     * Author:QiLin
     * @var string
     */
    public $logPath;
    /**
     * 仓库名称
     * Author:QiLin
     * @var
     */
    public $repoName;
    /**
     * 本地仓库地址
     * Author:QiLin
     * @var string
     */
    public $repoPath;
    /**
     * 临时文件名称
     * Author:QiLin
     * @var
     */
    public $tmpFolderName = '.branch';
    /**
     * 临时文件
     * Author:QiLin
     * @var string
     */
    public $tmpFolder;
    /**
     * 需要清理的文件
     * Author:QiLin
     * @var
     */
    public $clean;
    /**
     * copy文件(用于内部文件的复制)
     * Author:QiLin
     * @var
     */
    public $copy;
    /**
     * 重新加载守护进程
     * Author:QiLin
     * @var mixed
     */
    public $reloadSupervisor;

    protected $excludeSyncFiles = [
        '.doc',
        '.git*',
        '*.swp',
        'Gruntfile.js',
        'package.json',
        '.jshintrc',
    ];

    public function __construct(array $options = [])
    {
        if (isset($options['distPath'])) {
            $this->distPath = $options['distPath'];
        }
        if (isset($options['repo'])) {
            $this->repo = $options['repo'];
        }
        if (isset($options['makeDir'])) {
            $this->makeDir = $options['makeDir'];
        }
        if (isset($options['chmod']) && is_array($options['chmod'])) {
            $this->chmod = $options['chmod'];
        }
        if (isset($options['variables']) && is_array($options['variables'])) {
            $this->variables = $options['variables'];
        }
        if (isset($options['configFiles']) && is_array($options['configFiles'])) {
            $this->configFiles = $options['configFiles'];
        }
        if (isset($options['clean']) && is_array($options['clean'])) {
            $this->clean = $options['clean'];
        }
        if (isset($options['reloadSupervisor']) && is_array($options['reloadSupervisor'])) {
            $this->reloadSupervisor = $options['reloadSupervisor'];
        }
        if (isset($options['logPath'])) {
            $this->logPath = $options['logPath'];
        }
        if (isset($options['tmpFolder'])) {
            $this->tmpFolder = $options['tmpFolder'] . DIRECTORY_SEPARATOR . $this->tmpFolderName . DIRECTORY_SEPARATOR;
        }
        if ($this->distPath && !preg_match('/\/$/', $this->distPath)) {
            $this->distPath .= '/';
        }
        if (!$this->logPath) {
            $this->logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR;
        }
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
        if (!$this->tmpFolder) {
            $this->tmpFolder = dirname(__DIR__) . DIRECTORY_SEPARATOR . $this->tmpFolderName . DIRECTORY_SEPARATOR;
        }
        if (!file_exists($this->tmpFolder)) {
            mkdir($this->tmpFolder, 0777);
        }
        $this->repoName = $this->getRepoName();
        $this->repoPath = $this->tmpFolder . $this->repoName . DIRECTORY_SEPARATOR;
    }

    /**
     * Author:QiLin
     * @return bool
     */
    public function validate()
    {
        if (!trim($this->distPath)) {
            return false;
        }
        if (!trim($this->repo)) {
            return false;
        }
        return true;
    }

    /**
     * 是否相对路径
     * Author:QiLin
     * @param $path
     * @return bool
     */
    public static function isRelativePath($path)
    {
        if (preg_match('/^\//', $path)) {
            return false;
        }
        return true;
    }

    /**
     * 清理目录
     * Author:QiLin
     */
    public function clean()
    {
        foreach ($this->clean as $filename) {
            if (!$this->distPath) {
                continue;
            }
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath . $filename;
            } else {
                continue;
            }
            echo $filename . PHP_EOL;
            if (!is_dir($filename)) {
                continue;
            }
            $this->writeLog("Clean $filename/* ...");
            $this->exec("rm -rf $filename/*");
        }
    }


    /**
     * 文件内部copy
     * Author:QiLin
     */
    public function copy()
    {
        foreach ($this->copy as $filename => $to) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath . $filename;
            } else {
                continue;
            }
            if (self::isRelativePath($to) === true) {
                $to = $this->distPath . $to;
            }
            echo $filename . PHP_EOL;
            echo $to . PHP_EOL;
            if (!is_dir($filename) || !is_dir($to)) {
                continue;
            }

            $this->writeLog("Copy $filename/* to $to...");
            $this->exec("rsync -aqz $filename/* $to");
        }
    }

    /**
     * 同步文件
     * Author:QiLin
     * @return string
     */
    public function genSyncCmd()
    {
        $excludeCmd = '';
        foreach ($this->excludeSyncFiles as $file) {
            $excludeCmd .= ' --exclude "' . $file . '" ';
        }
        return 'rsync -aqz' . $excludeCmd . $this->repoPath . ' ' . $this->distPath;
    }

    /**
     * 是否是本地仓库
     * Author:QiLin
     * @return bool
     */
    public function isLocalhostRepo()
    {
        if (preg_match("/repo\.freeradio\.cn|127\.0\.0\.1|121\.41\.21\.104/", $this->repo)) {
            return true;
        }
        return false;
    }

    public function mkdir()
    {
        foreach ($this->makeDir as $filename => $state) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath . $filename;
            }
            if (file_exists($filename)) {
                continue;
            }
            mkdir($filename);
            $this->exec("chmod -R  $state $filename");
        }
    }

    /**
     * 设置权限
     * Author:QiLin
     */
    public function chmod()
    {
        foreach ($this->chmod as $filename => $state) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath . $filename;
            }
            if (!file_exists($filename)) {
                continue;
            }
            $this->writeLog("Authorizing $filename to $state...");
            $this->exec("chmod -R  $state $filename");
        }
    }

    public function reloadServer()
    {
        if ($this->reloadSupervisor) {
            foreach ($this->reloadSupervisor as $service) {
                $this->exec('supervisorctl  restart ' . $service);
            }
        }
    }

    public function deploy(string $branch)
    {
        if ($this->validate() === false) {
            $this->writeLog($this->repoName . " distPath or repo setting has error occurred....");
            return false;
        }
        if (!file_exists($this->repoPath)) {
            $this->initRepo($this->repoName);
        }
        $this->clean();

        if ($this->fetch($branch) === false) {
            $this->writeLog($this->repoName . " Deploy failed....");
            return false;
        };
        $this->recordBranch($branch);
        $this->renderVariables();
        $this->mkdir();
        $this->chmod();
        $this->copy();
        $this->reloadServer();
        $this->writeLog($this->repoName . " Deploy succeed....");
    }

    public static function logFile()
    {
        return date('Y_m') . '_deploy_log';
    }

    /**
     *
     * @author Robert
     *
     * @param string $name
     * @return bool
     */
    protected function initRepo($name = '')
    {
        chdir($this->tmpFolder);
        $success = $this->exec("git clone " . $this->repo . ' ' . $name);
        if ($success != 0) {
            return false;
        }
        return true;
    }

    /**
     * 获取仓库地址
     * Author:QiLin
     * @return null|string|string[]
     */
    protected function getRepoName()
    {
        if (!$this->repo) {
            return '';
        }
        if ($this->isLocalhostRepo() === false) {
            return sha1($this->repo . $this->version);
        };
        $path = explode('/', $this->repo);
        $name = preg_replace('/\.git$/', '', array_pop($path));
        return $this->version ? $name . '.' . $this->version : $name;
    }

    protected function exec($cmd)
    {
        //        system($cmd."\n" . ' 2>&1 >>' . $this->logPath . self::logFile(), $success);
        //        system($cmd . ' >>' . $this->logPath . self::logFile(), $success);
        system($cmd, $success);
        return $success;
    }

    /**
     * 获取分支
     * Author:QiLin
     * @param $branch
     * @return bool|mixed
     */
    protected function fetch($branch)
    {

        chdir($this->repoPath);
        //          $this->exec('git stash; git stash clear; git fetch ');

        $this->exec('git clean -df');
        $this->exec('git fetch');
        $success = $this->exec('git checkout ' . $branch);
        if ($success != 0 && $branch !== 'master') {
            $this->exec('git checkout master');
            return false;
        }
        $this->exec('git pull');
        $cmd = $this->genSyncCmd();
        return $this->exec($cmd);
    }

    protected function recordBranch($branch)
    {
        error_log("[" . date('Y-m-d H:i:s') . "] " . $branch . PHP_EOL, 3, $this->distPath . 'branch.log');
    }

    /**
     * 读取变量
     * Author:QiLin
     */
    protected function renderVariables()
    {
        $variableFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'variables.ini';
        $variables = [];
        if (file_exists($variableFile)) {
            $variables = parse_ini_file($variableFile);
        }
        if (is_array($this->variables) && $this->variables) {
            $variables = array_merge($variables, $this->variables);
        }

        foreach ($this->configFiles as $filename => $dist) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath . $filename;
            }
            if (!file_exists($filename)) {
                continue;
            }
            echo 'Rendering ' . $filename . '...', PHP_EOL;
            $ctx = file_get_contents($filename);
            foreach ($variables as $variable => $value) {
                $ctx = str_replace('@@' . $variable . '@@', $value, $ctx);
            }

            file_put_contents($this->distPath . $dist, $ctx);
        }
    }

    protected function writeLog($msg)
    {
        echo $msg . PHP_EOL;
        $msg = '[' . date("Y-m-d H:i:s") . '] ' . $msg;
        error_log($msg . "\n", 3, $this->logPath . self::logFile());
    }
}
