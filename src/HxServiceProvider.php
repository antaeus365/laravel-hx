<?php

namespace YourVendor\LaravelHx;

use Illuminate\Support\ServiceProvider;
use YourVendor\LaravelHx\Routing\HxRouter;
use YourVendor\LaravelHx\Support\BladeDirectives;

class HxServiceProvider extends ServiceProvider
{
    /**
     * 注册服务
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/hx.php', 'hx');
        
        $this->app->singleton(HxManager::class, function ($app) {
            return new HxManager($app);
        });
        
        $this->app->alias(HxManager::class, 'hx.manager');
    }

    /**
     * 启动服务
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/hx.php' => config_path('hx.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/hx'),
        ], 'views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hx');

        if ($this->app->runningInConsole()) {
            $this->commands([]);
        }

        // 注册中间件
        $this->registerMiddleware();

        // 注册Blade指令
        BladeDirectives::register();

        // 注册路由
        $this->registerRoutes();
    }

    /**
     * 注册HTMX路由
     */
    protected function registerRoutes(): void
    {
        $router = new HxRouter($this->app['router']);
        $router->registerRoutes();
    }

    /**
     * 注册中间件
     */
    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('hx', \YourVendor\LaravelHx\Http\Middleware\HxMiddleware::class);
    }
}