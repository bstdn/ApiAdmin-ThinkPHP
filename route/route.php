<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::miss('api/Miss/index');

Route::group('admin', function() {
    Route::rule('Login/index', 'admin/Login/index', 'post');
    Route::rule(
        'Login/getUserInfo', 'admin/Login/getUserInfo', 'get'
    )->middleware(['AdminAuth', 'AdminLog']);
    Route::rule(
        'Login/logout', 'admin/Login/logout', 'get'
    )->middleware(['AdminAuth', 'AdminLog']);
    Route::rule(
        'Index/upload', 'admin/Index/upload', 'post'
    )->middleware(['AdminAuth', 'AdminLog']);

    Route::group('Menu', [
        'index'        => ['admin/Menu/index', ['method' => 'get']],
        'changeStatus' => ['admin/Menu/changeStatus', ['method' => 'get']],
        'add'          => ['admin/Menu/add', ['method' => 'post']],
        'edit'         => ['admin/Menu/edit', ['method' => 'post']],
        'del'          => ['admin/Menu/del', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('User', [
        'index'        => ['admin/User/index', ['method' => 'get']],
        'changeStatus' => ['admin/User/changeStatus', ['method' => 'get']],
        'add'          => ['admin/User/add', ['method' => 'post']],
        'edit'         => ['admin/User/edit', ['method' => 'post']],
        'del'          => ['admin/User/del', ['method' => 'get']],
        'own'          => ['admin/User/own', ['method' => 'post']],
        'getUsers'     => ['admin/User/getUsers', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('Auth', [
        'index'        => ['admin/Auth/index', ['method' => 'get']],
        'changeStatus' => ['admin/Auth/changeStatus', ['method' => 'get']],
        'add'          => ['admin/Auth/add', ['method' => 'post']],
        'edit'         => ['admin/Auth/edit', ['method' => 'post']],
        'del'          => ['admin/Auth/del', ['method' => 'get']],
        'delMember'    => ['admin/Auth/delMember', ['method' => 'get']],
        'getGroups'    => ['admin/Auth/getGroups', ['method' => 'get']],
        'getRuleList'  => ['admin/Auth/getRuleList', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('Log', [
        'index' => ['admin/Log/index', ['method' => 'get']],
        'del'   => ['admin/Log/del', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('InterfaceGroup', [
        'index'        => ['admin/InterfaceGroup/index', ['method' => 'get']],
        'changeStatus' => ['admin/InterfaceGroup/changeStatus', ['method' => 'get']],
        'add'          => ['admin/InterfaceGroup/add', ['method' => 'post']],
        'edit'         => ['admin/InterfaceGroup/edit', ['method' => 'post']],
        'del'          => ['admin/InterfaceGroup/del', ['method' => 'get']],
        'getAll'       => ['admin/InterfaceGroup/getAll', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('InterfaceList', [
        'index'        => ['admin/InterfaceList/index', ['method' => 'get']],
        'changeStatus' => ['admin/InterfaceList/changeStatus', ['method' => 'get']],
        'add'          => ['admin/InterfaceList/add', ['method' => 'post']],
        'edit'         => ['admin/InterfaceList/edit', ['method' => 'post']],
        'del'          => ['admin/InterfaceList/del', ['method' => 'get']],
        'refresh'      => ['admin/InterfaceList/refresh', ['method' => 'get']],
        'getHash'      => ['admin/InterfaceList/getHash', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('Fields', [
        'index'        => ['admin/Fields/index', ['method' => 'get']],
        'request'      => ['admin/Fields/request', ['method' => 'get']],
        'response'     => ['admin/Fields/response', ['method' => 'get']],
        'upload'       => ['admin/Fields/upload', ['method' => 'post']],
        'add'          => ['admin/Fields/add', ['method' => 'post']],
        'edit'         => ['admin/Fields/edit', ['method' => 'post']],
        'del'          => ['admin/Fields/del', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('AppGroup', [
        'index'        => ['admin/AppGroup/index', ['method' => 'get']],
        'changeStatus' => ['admin/AppGroup/changeStatus', ['method' => 'get']],
        'add'          => ['admin/AppGroup/add', ['method' => 'post']],
        'edit'         => ['admin/AppGroup/edit', ['method' => 'post']],
        'del'          => ['admin/AppGroup/del', ['method' => 'get']],
        'getAll'       => ['admin/AppGroup/getAll', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);

    Route::group('App', [
        'index'            => ['admin/App/index', ['method' => 'get']],
        'changeStatus'     => ['admin/App/changeStatus', ['method' => 'get']],
        'add'              => ['admin/App/add', ['method' => 'post']],
        'edit'             => ['admin/App/edit', ['method' => 'post']],
        'del'              => ['admin/App/del', ['method' => 'get']],
        'getAppInfo'       => ['admin/App/getAppInfo', ['method' => 'get']],
        'refreshAppSecret' => ['admin/App/refreshAppSecret', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminPermission', 'AdminLog']);
    //MISS路由定义
    Route::miss('admin/Miss/index');
})->middleware('AdminResponse');
