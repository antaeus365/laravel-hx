<?php

use Antaeus365\LaravelHx\HxManager;
use Antaeus365\LaravelHx\HxContext;
use Antaeus365\LaravelHx\Routing\HxRouter;

if (!function_exists('hx')) {
    /**
     * 获取HTMX管理器实例
     */
    function hx(): HxManager
    {
        return app(HxManager::class);
    }
}

if (!function_exists('hx_action')) {
    /**
     * 生成HTMX动作URL
     */
    function hx_action(string $action, ?int $id = null): string
    {
        $context = HxContext::current();
        
        if (!$context) {
            throw new InvalidArgumentException('No current component context');
        }

        $componentClass = get_class($context);
        $componentId = $id ?? $context->getId();
        
        if (!$componentId) {
            throw new InvalidArgumentException('Component ID is required');
        }

        $router = app(HxRouter::class);
        return $router->componentUrl($componentClass, $componentId, $action);
    }
}

if (!function_exists('hx_component')) {
    /**
     * 生成组件URL
     */
    function hx_component(string $componentClass, int $id): string
    {
        $router = app(HxRouter::class);
        return $router->componentUrl($componentClass, $id);
    }
}

if (!function_exists('hx_oob')) {
    /**
     * 添加OOB更新
     */
    function hx_oob(string $view, array $data = []): void
    {
        hx()->oob($view, $data);
    }
}

if (!function_exists('hx_trigger')) {
    /**
     * 触发HTMX事件
     */
    function hx_trigger(string $event, array $details = []): void
    {
        hx()->trigger($event, $details);
    }
}

if (!function_exists('hx_header')) {
    /**
     * 添加HTMX响应头
     */
    function hx_header(string $key, string $value): void
    {
        hx()->header($key, $value);
    }
}
