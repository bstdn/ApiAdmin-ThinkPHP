<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminMenu extends Migrator
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
        $table = $this->table('admin_menu', [
            'comment' => '目录信息',
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('name', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '菜单名',
        ])->addColumn('fid', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '父级菜单ID',
        ])->addColumn('url', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '链接',
        ])->addColumn('auth', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '访客权限',
        ])->addColumn('sort', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '排序',
        ])->addColumn('hide', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '是否显示',
        ])->addColumn('icon', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '菜单图标',
        ])->addColumn('level', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '菜单认证等级',
        ])->create();
    }
}
