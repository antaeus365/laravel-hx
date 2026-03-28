<?php

namespace YourVendor\LaravelHx\Routing;

use Illuminate\Routing\Router;
use YourVendor\LaravelHx\HxManager;
use YourVendor\LaravelHx\Http\Controllers\HxController;

class HxRouter
{
    /**
     * Laravel路由器实例
     */
    protected Router $router;

    /**
     * HTMX管理器实例
     */
    protected HxManager $hx;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->hx = app(HxManager::class);
    }

    /**
     * 注册HTMX路由
     */
    public function registerRoutes(): void
    {
        $prefix = config('hx.route_prefix', 'hx');
        
        $this->router->group([
            'prefix' => $prefix,
            'middleware' => ['web'],
        ], function () {
            // 为每个已注册的组件注册路由
            foreach ($this->hx->getComponents() as $componentClass) {
                $this->registerComponentRoutes($componentClass);
            }
        });
    }

    /**
     * 注册单个组件的路由
     */
    protected function registerComponentRoutes(string $componentClass): void
    {
        $componentName = $this->getComponentName($componentClass);
        
        // GET /hx/component-name/{id} - 获取组件HTML
        $this->router->get("{$componentName}/{id}", [HxController::class, 'show'])
            ->name("hx.{$componentName}.show")
            ->where('id', '[0-9]+');

        // POST /hx/component-name/{id}/{action} - 执行组件动作
        $this->router->post("{$componentName}/{id}/{action}", [HxController::class, 'action'])
            ->name("hx.{$componentName}.action")
            ->where('id', '[0-9]+')
            ->where('action', '[a-zA-Z0-9_-]+');
    }

    /**
     * 从类名获取组件名称
     */
    protected function getComponentName(string $componentClass): string
    {
        $basename = class_basename($componentClass);
        
        // 移除"Component"后缀（如果存在）
        if (str_ends_with($basename, 'Component')) {
            $basename = substr($basename, 0, -9);
        }
        
        // 转换为kebab-case
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $basename));
    }

    /**
     * 手动注册组件路由
     */
    public function component(string $componentClass): self
    {
        $this->hx->component($componentClass);
        $this->registerComponentRoutes($componentClass);
        return $this;
    }

    /**
     * 获取路由前缀
     */
    public function getRoutePrefix(): string
    {
        return config('hx.route_prefix', 'hx');
    }

    /**
     * 生成组件URL
     */
    public function componentUrl(string $componentClass, int $id, ?string $action = null): string
    {
        $componentName = $this->getComponentName($componentClass);
        $prefix = $this->getRoutePrefix();
        
        if ($action) {
            return "/{$prefix}/{$componentName}/{$id}/{$action}";
        }
        
        return "/{$prefix}/{$componentName}/{$id}";
    }
}