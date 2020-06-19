<?php

use think\migration\Migrator;

class AdminList extends Migrator
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
        $table = $this->table('admin_list', [
            'comment' => '用于维护接口信息',
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('api_class', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => 'api索引，保存了类和方法',
        ])->addColumn('hash', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => 'api唯一标识',
        ])->addColumn('access_token', 'integer', [
            'limit'   => 2,
            'default' => 1,
            'comment' => '是否需要认证AccessToken 1：需要，0：不需要',
        ])->addColumn('need_login', 'integer', [
            'limit'   => 2,
            'default' => 1,
            'comment' => '是否需要认证用户token  1：需要 0：不需要',
        ])->addColumn('status', 'integer', [
            'limit'   => 2,
            'default' => 1,
            'comment' => 'API状态：0表示禁用，1表示启用',
        ])->addColumn('method', 'integer', [
            'limit'   => 2,
            'default' => 2,
            'comment' => '请求方式0：不限1：Post，2：Get',
        ])->addColumn('info', 'string', [
            'limit'   => 500,
            'default' => '',
            'comment' => 'api中文说明',
        ])->addColumn('is_test', 'integer', [
            'limit'   => 2,
            'default' => 0,
            'comment' => '是否是测试模式：0:生产模式，1：测试模式',
        ])->addColumn('return_str', 'text', [
            'null'    => true,
            'comment' => '返回数据示例',
        ])->addColumn('group_hash', 'string', [
            'limit'   => 64,
            'default' => 'default',
            'comment' => '当前接口所属的接口分组',
        ])->addIndex(['hash'])->create();
    }
}
