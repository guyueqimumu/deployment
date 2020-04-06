<?php

namespace Service\Core;

/**
 *
 * @author Robert
 *
 * Class Repository
 * @package Service\Core
 *
 */
class Repository
{

    protected $options;

    /**
     * @var array
     */
    protected $repoName;

    /**
     * 部署目标目录
     * @var
     */
    protected $distPath;

    /**
     * @var
     */
    protected $copy = [];

    /**
     * @var
     */
    protected $clean = [];

    /**
     * @var
     */
    protected $configFiles = [];

    /**
     * 部署地址
     * @var
     */
    protected $repo;


    /**
     * @var
     */
    protected $reloadSupervisor = [];


    /**
     * @var
     */
    protected $tmpFolderName = '.branch';

    /**
     * @var string
     */
    protected $tmpFolder;

    /**
     * @var array
     */
    protected $makeDir = [];

    /**
     * @var
     */
    protected $repoPath;

    /**
     * @var
     */
    protected $variables;

    /**
     * @var array
     */
    protected $chmod = [];

    /**
     * @var array
     */
    protected $version = '';


    /**
     *
     */
    const DEFAULT_DEPLOYED_BRANCH = 'master';

    /**
     * @var string
     */
    protected $logPath;

    /**
     * 不被部署的文件
     * @var array
     */
    protected $excludeSyncFiles = [
        '.doc',
        '.git*',
        '*.swp',
        'Gruntfile.js',
        'package.json',
        '.jshintrc',
    ];

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        if (isset($options['version'])) {
            $this->version = $options['version'];
        }
        if (isset($options['distPath'])) {
            $this->distPath = $options['distPath'];
        }
        if (isset($options['makeDir'])) {
            $this->makeDir = $options['makeDir'];
        }
        if ($this->distPath && !preg_match('/\/$/', $this->distPath)) {
            $this->distPath .= '/';
        }
        if (isset($options['repo'])) {
            $this->repo = $options['repo'];
        }
        if (isset($options['chmod']) && is_array($options['chmod'])) {
            $this->chmod = $options['chmod'];
        }
        if (isset($options['clean']) && is_array($options['clean'])) {
            $this->clean = $options['clean'];
        }
        if (isset($options['copy']) && is_array($options['copy'])) {
            $this->copy = $options['copy'];
        }
        if (isset($options['variables']) && is_array($options['variables'])) {
            $this->variables = $options['variables'];
        }
        if (isset($options['reloadSupervisor']) && is_array($options['reloadSupervisor'])) {
            $this->reloadSupervisor = $options['reloadSupervisor'];
        }
        if (isset($options['configFiles']) && is_array($options['configFiles'])) {
            $this->configFiles = $options['configFiles'];
        }
        if (isset($options['excludeSyncFiles']) && is_array($options['excludeSyncFiles'])) {
            array_merge($this->excludeSyncFiles, $options['excludeSyncFiles']);
        }

        $this->logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR .DIRECTORY_SEPARATOR;
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0777);
        }

        $this->tmpFolder = dirname(__DIR__).DIRECTORY_SEPARATOR.$this->tmpFolderName.DIRECTORY_SEPARATOR;
        if (!file_exists($this->tmpFolder)) {
            mkdir($this->tmpFolder, 0777);
        }
        $this->repoName = $this->getRepoName();

        $this->repoPath = $this->tmpFolder.$this->repoName.DIRECTORY_SEPARATOR;
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
        $success = $this->exec("git clone ".$this->repo.' '.$name);
        if ($success != 0) {
            return false;
        }
        return true;
    }

    /**
     *
     * @author Robert
     *
     * @return mixed|string
     */
    protected function getRepoName()
    {
        if (!$this->repo) {
            return '';
        }
        if (!preg_match("/repo\.freeradio\.cn|127\.0\.0\.1|121\.41\.21\.104/", $this->repo)) {
            return sha1($this->repo.$this->version);
        }
        $path = explode('/', $this->repo);
        $name = preg_replace('/\.git$/', '', array_pop($path));
        return $this->version ? $name.'.'.$this->version : $name;
    }

    /**
     *
     * @author Robert
     *
     * @param $branch
     * @return bool
     */
    protected function fetch($branch)
    {
        chdir($this->repoPath);
        //          $this->exec('git stash; git stash clear; git fetch ');
        $this->exec('git clean -df; git fetch');
        $success = $this->exec('git checkout '.$branch);
        if ($success != 0 && $branch !== 'master') {
            $this->exec('git checkout master');
            return false;
        }
        $this->exec('git pull');
        $cmd = $this->genSyncCmd();
        return $this->exec($cmd);
    }

    /**
     *
     * @author Robert
     *
     * @param $cmd
     * @return mixed
     */
    protected function exec($cmd)
    {
        //        system($cmd."\n" . ' 2>&1 >>' . $this->logPath . self::logFile(), $success);
        //        system($cmd . ' >>' . $this->logPath . self::logFile(), $success);
        system($cmd, $success);
        return $success;
    }

    /**
     *
     * @author Robert
     *
     * @return array
     */
    public function tag()
    {
        if (!is_dir($this->repoPath)) {
            return [];
        }
        $getCwd = getcwd();
        chdir($this->repoPath);
        $outputs = [];
        exec("git tag", $outputs);
        $data = [];
        foreach ($outputs as $txt) {
            array_push($data, $txt);
        }
        chdir($getCwd);
        return $data;
    }

    /**
     *
     * @author Robert
     *
     * @param bool|true $fetch
     * @return array
     */
    public function branch($fetch = true)
    {

        if (!is_dir($this->repoPath)) {
            return [];
        }
        $getCwd = getcwd();
        chdir($this->repoPath);

        if ($fetch === true) {
            system("git fetch");
        }
        $outputs = [];
        exec("git branch -a", $outputs);
        //        if ($success != 0) {
        //            return [];
        //        }
        $data = [];
        $check = [];

        foreach ($outputs as $txt) {
            $branch = [];
            if (preg_match('/HEAD\s\->/', $txt)) {
                continue;
            }
            if (preg_match('/\*/', $txt)) {
                $branch['current'] = true;
            } else {
                $branch['current'] = false;
            }
            $name = trim(preg_replace(['/\*/', '/remotes\/origin\//'], ['', ''], $txt));
            if (in_array($name, $check)) {
                continue;
            }
            $branch['name'] = $name;
            array_push($check, $name);
            array_push($data, $branch);
        }
        chdir($getCwd);
        return $data;
    }

    /**
     *
     * @author Robert
     *
     * @param int $n
     * @return mixed
     */
    public function log($n = 10)
    {
        if (!is_dir($this->repoPath)) {
            return [];
        }
        $getCwd = getcwd();
        chdir($this->repoPath);
        $outputs = [];
        exec("git log -n $n", $outputs);
        //        if ($success != 0) {
        //            return [];
        //        }
        $data = [];
        $index = -1;
        foreach ($outputs as $text) {
            if (preg_match('/^commit/', $text)) {
                $index++;
            }
            if (!isset($data[$index]) || !is_array($data[$index])) {
                $data[$index] = [];
            }
            if (preg_match('/^commit/', $text)) {
                $data[$index]['commit'] = $text;
            } elseif (preg_match('/^Author/', $text)) {
                $data[$index]['author'] = $text;
            } elseif (preg_match('/^Date/', $text)) {
                $data[$index]['date'] = $text;
            } else {
                if (!isset($data[$index]['desc']) || !is_array($data[$index]['desc'])) {
                    $data[$index]['desc'] = [];
                }
                if (trim($text)) {
                    array_push($data[$index]['desc'], $text);
                }
            }
        }
        chdir($getCwd);
        return $data;
    }

    /**
     *
     * @author Robert
     *
     * @return string
     */
    public function genSyncCmd()
    {
        $excludeCmd = '';
        foreach ($this->excludeSyncFiles as $file) {
            $excludeCmd .= ' --exclude "'.$file.'" ';
        }
        return 'rsync -aqz'.$excludeCmd.$this->repoPath.' '.$this->distPath;
    }


    /**
     *
     * @author Robert
     *
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
     *
     * @author Robert
     *
     * @param string $branch
     * @return bool
     */
    public function deploy($branch = self::DEFAULT_DEPLOYED_BRANCH)
    {
        if ($this->validate() === false) {
            $this->writeLog($this->repoName." distPath or repo setting has error occurred....");
            return false;
        }
        if (!file_exists($this->repoPath)) {
            $this->initRepo($this->repoName);
        }
        $this->clean();
        if ($this->fetch($branch) === false) {
            $this->writeLog($this->repoName." Deploy failed....");
            return false;
        };
        $this->recordBranch($branch);
        $this->renderVariables();
        $this->mkdir();
        $this->chmod();
        $this->copy();
        $this->reloadServer();
        $this->writeLog($this->repoName." Deploy succeed....");
        return true;
    }


    /**
     * 是否相对路径
     * @author Robert
     *
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
     *
     * @author Robert
     *
     */
    public function chmod()
    {
        foreach ($this->chmod as $filename => $state) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath.$filename;
            }
            if (!file_exists($filename)) {
                continue;
            }
            $this->writeLog("Authorizing $filename to $state...");
            $this->exec("chmod -R  $state $filename");
        }
    }

    /**
     * 创建目录并设置权限
     */
    public function mkdir()
    {
        foreach ($this->makeDir as $filename => $state) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath.$filename;
            }
            if (file_exists($filename)) {
                continue;
            }
            mkdir($filename);
            $this->exec("chmod -R  $state $filename");
        }
    }


    /**
     * Author:Robert
     *
     */
    public function copy()
    {
        foreach ($this->copy as $filename => $to) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath.$filename;
            } else {
                continue;
            }
            if (self::isRelativePath($to) === true) {
                $to = $this->distPath.$to;
            }
            echo $filename.PHP_EOL;
            echo $to.PHP_EOL;
            if (!is_dir($filename) || !is_dir($to)) {
                continue;
            }

            $this->writeLog("Copy $filename/* to $to...");
            $this->exec("rsync -aqz $filename/* $to");
        }
    }

    /**
     * Author:Robert
     *
     */
    public function clean()
    {
        foreach ($this->clean as $filename) {
            if (!$this->distPath) {
                continue;
            }
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath.$filename;
            } else {
                continue;
            }
            echo $filename.PHP_EOL;
            if (!is_dir($filename)) {
                continue;
            }
            $this->writeLog("Clean $filename/* ...");
            $this->exec("rm -rf $filename/*");
        }
    }

    /**
     *
     * @author Robert
     *
     * @param $branch
     */
    protected function recordBranch($branch)
    {
        error_log("[".date('Y-m-d H:i:s')."] ".$branch.PHP_EOL, 3, $this->distPath.'branch.log');
    }

    /**
     *
     * @author Robert
     *
     */
    protected function renderVariables()
    {
        $variableFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'variables.ini';
        $variables = [];
        if (file_exists($variableFile)) {
            $variables = parse_ini_file($variableFile);
        }
        if (is_array($this->variables) && $this->variables) {
            $variables = array_merge($variables, $this->variables);
        }

        foreach ($this->configFiles as $filename => $dist) {
            if (self::isRelativePath($filename) === true) {
                $filename = $this->distPath.$filename;
            }
            if (!file_exists($filename)) {
                continue;
            }
            echo 'Rendering '.$filename.'...', PHP_EOL;
            $ctx = file_get_contents($filename);
            foreach ($variables as $variable => $value) {
                $ctx = str_replace('@@'.$variable.'@@', $value, $ctx);
            }

            file_put_contents($this->distPath.$dist, $ctx);
        }
    }

    /**
     *
     * @author Robert
     *
     */
    public function reloadServer()
    {
        if ($this->reloadSupervisor) {
            foreach ($this->reloadSupervisor as $service) {
                $this->exec('supervisorctl  restart '.$service);
            }
        }
    }

    /**
     *
     * @author Robert
     *
     * @param $msg
     */
    protected function writeLog($msg)
    {
        echo $msg.PHP_EOL;
        $msg = '['.date("Y-m-d H:i:s").'] '.$msg;
        error_log($msg."\n", 3, $this->logPath.self::logFile());
    }

    /**
     *
     * @author Robert
     *
     * @return string
     */
    public static function logFile()
    {
        return date('Y_m').'_deploy_log';
    }

}
