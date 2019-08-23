<?php

use think\migration\Migrator;

class InitAdminMenu extends Migrator
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
        $data = [
            [
                'id'   => 1,
                'name' => '用户登录',
                'url'  => 'admin/Login/index',
            ],
            [
                'id'   => 2,
                'name' => '获取用户信息',
                'url'  => 'admin/Login/getUserInfo',
            ],
            [
                'id'   => 3,
                'name' => '用户登出',
                'url'  => 'admin/Login/logout',
            ],
            [
                'id'   => 4,
                'name' => '系统管理',
                'sort' => 1,
            ],
            [
                'id'   => 5,
                'name' => '菜单维护',
                'fid'  => 4,
                'sort' => 1,
            ],
            [
                'id'   => 6,
                'name' => '菜单列表',
                'fid'  => 5,
                'url'  => 'admin/Menu/index',
            ],
            [
                'id'   => 7,
                'name' => '菜单状态修改',
                'fid'  => 5,
                'url'  => 'admin/Menu/changeStatus',
            ],
            [
                'id'   => 8,
                'name' => '新增菜单',
                'fid'  => 5,
                'url'  => 'admin/Menu/add',
            ],
            [
                'id'   => 9,
                'name' => '编辑菜单',
                'fid'  => 5,
                'url'  => 'admin/Menu/edit',
            ],
            [
                'id'   => 10,
                'name' => '菜单删除',
                'fid'  => 5,
                'url'  => 'admin/Menu/del',
            ],
            [
                'id'   => 11,
                'name' => '日志管理',
                'fid'  => 4,
                'sort' => 2,
            ],
            [
                'id'   => 12,
                'name' => '获取操作日志列表',
                'fid'  => 11,
                'url'  => 'admin/Log/index',
            ],
            [
                'id'   => 13,
                'name' => '删除单条日志记录',
                'fid'  => 11,
                'url'  => 'admin/Log/del',
            ],
        ];
        $this->table('admin_menu')->insert($data)->save();
    }
}
