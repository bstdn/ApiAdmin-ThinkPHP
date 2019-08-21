<?php

use app\util\Enum;
use app\util\Strs;
use app\util\Tools;
use think\facade\Env;
use think\migration\Migrator;

class InitAdminUser extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up() {
        $username = 'admin';
        $password = Strs::randString(6);
        $salt = Strs::randString(6);
        $data = [
            'id'          => 1,
            'username'    => $username,
            'nickname'    => $username,
            'password'    => Tools::encryptPassword($password, $salt),
            'salt'        => $salt,
            'create_time' => time(),
            'create_ip'   => ip2long('127.0.0.1'),
            'update_time' => time(),
            'status'      => Enum::isTrue,
        ];
        $this->table('admin_user')->insert($data)->saveData();

        $lockFile = Env::get('app_path') . 'install' . DIRECTORY_SEPARATOR . 'lock.ini';
        file_put_contents($lockFile, "username:{$username}, password:{$password}");
    }
}
