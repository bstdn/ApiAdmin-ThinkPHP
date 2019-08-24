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

Route::group('admin', function() {
    Route::rule('Login/index', 'admin/Login/index', 'post');
    Route::rule(
        'Login/getUserInfo', 'admin/Login/getUserInfo', 'get'
    )->middleware(['AdminAuth', 'AdminLog']);
    Route::rule(
        'Login/logout', 'admin/Login/logout', 'get'
    )->middleware(['AdminAuth', 'AdminLog']);

    Route::group('Menu', [
        'index'        => ['admin/Menu/index', ['method' => 'get']],
        'changeStatus' => ['admin/Menu/changeStatus', ['method' => 'get']],
        'add'          => ['admin/Menu/add', ['method' => 'post']],
        'edit'         => ['admin/Menu/edit', ['method' => 'post']],
        'del'          => ['admin/Menu/del', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminLog']);

    Route::group('User', [
        'index'        => ['admin/User/index', ['method' => 'get']],
        'changeStatus' => ['admin/User/changeStatus', ['method' => 'get']],
        'add'          => ['admin/User/add', ['method' => 'post']],
        'edit'         => ['admin/User/edit', ['method' => 'post']],
        'del'          => ['admin/User/del', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminLog']);

    Route::group('Log', [
        'index' => ['admin/Log/index', ['method' => 'get']],
        'del'   => ['admin/Log/del', ['method' => 'get']],
    ])->middleware(['AdminAuth', 'AdminLog']);
    //MISS路由定义
    Route::miss('admin/Miss/index');
})->middleware('AdminResponse');
