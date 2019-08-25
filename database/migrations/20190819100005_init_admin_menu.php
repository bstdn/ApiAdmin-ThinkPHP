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
                'sort' => 4,
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
            [
                'id'   => 14,
                'name' => '用户列表',
                'fid'  => 4,
                'url'  => 'admin/User/index',
                'sort' => 2,
            ],
            [
                'id'   => 15,
                'name' => '用户状态修改',
                'fid'  => 14,
                'url'  => 'admin/User/changeStatus',
            ],
            [
                'id'   => 16,
                'name' => '新增用户',
                'fid'  => 14,
                'url'  => 'admin/User/add',
            ],
            [
                'id'   => 17,
                'name' => '编辑用户',
                'fid'  => 14,
                'url'  => 'admin/User/edit',
            ],
            [
                'id'   => 18,
                'name' => '用户删除',
                'fid'  => 14,
                'url'  => 'admin/User/del',
            ],
            [
                'id'   => 19,
                'name' => '更新个人信息',
                'fid'  => 14,
                'url'  => 'admin/User/own',
            ],
            [
                'id'   => 20,
                'name' => '文件上传',
                'fid'  => 0,
                'url'  => 'admin/Index/upload',
            ],
            [
                'id'    => 21,
                'name'  => '权限管理',
                'fid'   => 4,
                'sort'  => 3,
            ],
            [
                'id'    => 22,
                'name'  => '权限列表',
                'fid'   => 21,
                'url'   => 'admin/Auth/index',
            ],
            [
                'id'    => 23,
                'name'  => '权限组状态编辑',
                'fid'   => 21,
                'url'   => 'admin/Auth/changeStatus',
            ],
            [
                'id'    => 24,
                'name'  => '新增权限组',
                'fid'   => 21,
                'url'   => 'admin/Auth/add',
            ],
            [
                'id'    => 25,
                'name'  => '权限组编辑',
                'fid'   => 21,
                'url'   => 'admin/Auth/edit',
            ],
            [
                'id'    => 26,
                'name'  => '删除权限组',
                'fid'   => 21,
                'url'   => 'admin/Auth/del',
            ],
            [
                'id'    => 27,
                'name'  => '从指定组中删除指定用户',
                'fid'   => 21,
                'url'   => 'admin/Auth/delMember',
            ],
            [
                'id'    => 28,
                'name'  => '获取全部已开放的可选组',
                'fid'   => 21,
                'url'   => 'admin/Auth/getGroups',
            ],
            [
                'id'    => 29,
                'name'  => '获取组所有的权限列表',
                'fid'   => 21,
                'url'   => 'admin/Auth/getRuleList',
            ],
            [
                'id'    => 30,
                'name'  => '获取当前组的全部用户',
                'fid'   => 14,
                'url'   => 'admin/User/getUsers',
            ],
        ];
        $this->table('admin_menu')->insert($data)->save();
    }
}
