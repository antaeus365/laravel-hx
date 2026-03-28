<?php

namespace Antaeus365\LaravelHx\Support;

use Illuminate\Support\Facades\Blade;

class BladeDirectives
{
    /**
     * 注册Blade指令
     */
    public static function register(): void
    {
        // hx-action 指令
        Blade::directive('hxAction', function ($expression) {
            return "<?php echo 'hx-post=\"' . hx_action({$expression}) . '\" hx-target=\"#' . HxContext::currentIdentifier() . '\" hx-swap=\"outerHTML\"'; ?>";
        });

        // hx-component 指令
        Blade::directive('hxComponent', function ($expression) {
            return "<?php echo 'hx-get=\"' . hx_component({$expression}) . '\" hx-target=\"this\" hx-swap=\"outerHTML\"'; ?>";
        });

        // hx-oob 指令
        Blade::directive('hxOob', function ($expression) {
            return "<?php hx_oob({$expression}); ?>";
        });

        // 组件包装器
        Blade::component('hx-wrapper', \Antaeus365\LaravelHx\View\Components\HxWrapper::class);
    }
}
