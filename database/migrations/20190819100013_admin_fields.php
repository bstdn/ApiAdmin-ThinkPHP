<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminFields extends Migrator
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
        $table = $this->table('admin_fields', [
            'comment' => '用于保存各个API的字段规则',
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('field_name', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '字段名称',
        ])->addColumn('hash', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '权限所属组的ID',
        ])->addColumn('data_type', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '数据类型，来源于DataType类库',
        ])->addColumn('default', 'string', [
            'limit'   => 500,
            'default' => '',
            'comment' => '默认值',
        ])->addColumn('is_must', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '是否必须 0为不必须，1为必须',
        ])->addColumn('range', 'string', [
            'limit'   => 500,
            'default' => '',
            'comment' => '范围，Json字符串，根据数据类型有不一样的含义',
        ])->addColumn('info', 'string', [
            'limit'   => 500,
            'default' => '',
            'comment' => '字段说明',
        ])->addColumn('type', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '字段用处：0为request，1为response',
        ])->addColumn('show_name', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => 'wiki显示用字段',
        ])->addIndex(['hash'])->create();
    }
}
