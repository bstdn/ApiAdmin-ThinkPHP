<?php

use think\migration\Migrator;

class AdminUser extends Migrator
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
    public function change() {
        $table = $this->table('admin_user', [
            'comment' => '管理员认证信息',
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('username', 'string', [
            'limit'   => 64,
            'default' => '',
            'comment' => '用户名',
        ])->addColumn('nickname', 'string', [
            'limit'   => 64,
            'default' => '',
            'comment' => '用户昵称',
        ])->addColumn('password', 'char', [
            'limit'   => 32,
            'default' => '',
            'comment' => '用户密码',
        ])->addColumn('salt', 'char', [
            'limit'   => 6,
            'default' => '',
            'comment' => '密码',
        ])->addColumn('create_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '注册时间',
        ])->addColumn('create_ip', 'biginteger', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '注册IP',
        ])->addColumn('update_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '更新时间',
        ])->addColumn('status', 'integer', [
            'limit'   => 1,
            'default' => 0,
            'comment' => '账号状态 0封号 1正常',
        ])->addIndex(['create_time'])->create();
    }
}
