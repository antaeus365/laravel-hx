<?php

namespace Antaeus365\LaravelHx;

use Illuminate\Contracts\Container\Container;
use Antaeus365\LaravelHx\Routing\HxRouter;

class HxManager
{
    /**
     * Laravel 应用容器
     */
    protected Container $app;

    /**
     * 已注册的组件
     */
    protected array $components = [];

    /**
     * OOB 更新队列
     */
    protected array $oobUpdates = [];

    /**
     * 事件队列
     */
    protected array $events = [];

    /**
     * 响应头
     */
    protected array $headers = [];

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * 注册组件
     */
    public function component(string $componentClass): self
    {
        $this->components[] = $componentClass;
        return $this;
    }

    /**
     * 获取已注册的组件
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * 添加 OOB 更新
     */
    public function oob(string $view, array $data = []): self
    {
        $this->oobUpdates[] = compact('view', 'data');
        return $this;
    }

    /**
     * 获取 OOB 更新
     */
    public function getOobUpdates(): array
    {
        return $this->oobUpdates;
    }

    /**
     * 清空 OOB 更新队列
     */
    public function clearOobUpdates(): void
    {
        $this->oobUpdates = [];
    }

    /**
     * 触发事件
     */
    public function trigger(string $event, array $details = []): self
    {
        $this->events[] = [
            'event' => $event,
            'details' => $details
        ];
        return $this;
    }

    /**
     * 获取待触发的事件
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * 清空事件队列
     */
    public function clearEvents(): void
    {
        $this->events = [];
    }

    /**
     * 添加响应头
     */
    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * 获取响应头
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 清空响应头
     */
    public function clearHeaders(): void
    {
        $this->headers = [];
    }

    /**
     * 刷新指定组件
     */
    public function refresh(array $components): self
    {
        foreach ($components as $componentClass => $id) {
            if (is_numeric($componentClass)) {
                // 如果键是数字，则值是组件类
                $componentClass = $id;
                $id = null;
            }
            
            $this->oob($componentClass, ['id' => $id]);
        }
        return $this;
    }

    /**
     * 检查是否为 HTMX 请求
     */
    public function isHtmxRequest(): bool
    {
        $request = $this->app->make('request');
        return $request->header('HX-Request') === 'true';
    }

    /**
     * 获取组件标识符
     */
    public function getComponentIdentifier(object $component): string
    {
        $className = class_basename(get_class($component));
        $id = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $className));
        
        if (property_exists($component, 'id')) {
            $id .= '-' . $component->id;
        }
        
        return $id;
    }
}
