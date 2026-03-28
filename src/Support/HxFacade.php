<?php

namespace Antaeus365\LaravelHx\Support;

use Illuminate\Support\Facades\Facade;

class HxFacade extends Facade
{
    /**
     * 获取组件注册名称
     */
    protected static function getFacadeAccessor(): string
    {
        return 'hx.manager';
    }
}
