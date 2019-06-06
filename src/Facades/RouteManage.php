<?php
/**
 * Created by PhpStorm.
 * User: chunrongl
 * Date: 2019/6/6
 * Time: 下午5:22
 */

namespace Chunrongl\tqigouRpcService\Facades;


use Illuminate\Support\Facades\Facade;

class RouteManage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tqigou.route';
    }
}