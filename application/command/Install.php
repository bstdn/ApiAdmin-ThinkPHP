<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\db\Connection;
use think\facade\Env;

class Install extends Command {

    protected function configure() {
        // 指令配置
        $this->setName('apiadmin:install')
            ->addOption('db', null, Option::VALUE_REQUIRED, '数据库连接参数，格式为：数据库类型://用户名:密码@数据库地址:数据库端口/数据库名#字符集')
            ->addOption('prefix', null, Option::VALUE_OPTIONAL, '数据库表前缀', '')
            ->setDescription('ApiAdmin安装脚本');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\Exception
     */
    protected function execute(Input $input, Output $output) {
        $tplPath = Env::get('app_path') . 'install' . DIRECTORY_SEPARATOR;
        $lockFile = $tplPath . 'lock.ini';
        if(file_exists($lockFile)) {
            $output->highlight('你已经安装过了，请勿重新安装');
            $output->highlight('如有需要请删除application/install/lock.ini文件再次尝试');
            exit;
        }
        if(!is_writable($tplPath)) {
            $output->highlight($tplPath . '缺少写权限');
            exit;
        }
        $tempPath = Env::get('runtime_path');
        if(!is_writable($tempPath)) {
            $output->highlight($tempPath . '缺少写权限');
            exit;
        }
        if(!extension_loaded('redis')) {
            $output->highlight('缺少Redis扩展');
            exit;
        }
        if($input->hasOption('db')) {
            try {
                $options = $this->parseDsnConfig($input->getOption('db'));
                $prefix = $input->getOption('prefix');
                Connection::instance($options)->getTables($options['database']);
                $confPath = Env::get('config_path');
                $dbConf = str_replace([
                    '{$DB_TYPE}', '{$DB_HOST}', '{$DB_NAME}',
                    '{$DB_USER}', '{$DB_PASSWORD}', '{$DB_PORT}',
                    '{$DB_CHAR}', '{$DB_PREFIX}',
                ], [
                    $options['type'], $options['hostname'], $options['database'],
                    $options['username'], $options['password'], $options['hostport'],
                    $options['charset'], $prefix,
                ], file_get_contents($tplPath . 'db.tpl'));
                file_put_contents($confPath . 'database.php', $dbConf);
                $output->info('数据库配置更新成功');
                file_put_contents($lockFile, 'lock');
                $output->info('lock文件初始化成功');
            } catch(\PDOException $e) {
                $output->highlight($e->getMessage());
            }
        } else {
            $output->highlight('请输入数据库配置');
        }
    }

    /**
     * @param $dsnStr
     * @return array
     */
    private function parseDsnConfig($dsnStr) {
        $info = parse_url($dsnStr);
        if(!$info) {
            return [];
        }
        $dsn = [
            'type'     => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'hostname' => isset($info['host']) ? $info['host'] : '',
            'hostport' => isset($info['port']) ? $info['port'] : '3306',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? $info['fragment'] : 'utf8mb4',
        ];
        if(isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }

        return $dsn;
    }
}
