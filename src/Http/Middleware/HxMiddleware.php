<?php

namespace Antaeus365\LaravelHx\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Antaeus365\LaravelHx\HxManager;

class HxMiddleware
{
    /**
     * HTMX管理器实例
     */
    protected HxManager $hx;

    public function __construct(HxManager $hx)
    {
        $this->hx = $hx;
    }

    /**
     * 处理HTMX请求
     */
    public function handle(Request $request, Closure $next)
    {
        // 检查是否为HTMX请求
        if ($this->isHtmxRequest($request)) {
            $request->attributes->set('is_htmx_request', true);
            
            // 处理HX-Prompt头
            if ($prompt = $request->header('HX-Prompt')) {
                $request->merge(['hx_prompt' => $prompt]);
            }
            
            // 处理HX-Target头
            if ($target = $request->header('HX-Target')) {
                $request->attributes->set('hx_target', $target);
            }
            
            // 处理HX-Trigger头
            if ($trigger = $request->header('HX-Trigger')) {
                $request->attributes->set('hx_trigger', $trigger);
            }
        }

        $response = $next($request);

        // 如果是HTMX请求且响应是重定向，则转换为HX-Redirect
        if ($this->isHtmxRequest($request) && $response->isRedirection()) {
            return $this->handleRedirectResponse($response);
        }

        return $response;
    }

    /**
     * 检查是否为HTMX请求
     */
    protected function isHtmxRequest(Request $request): bool
    {
        return $request->header('HX-Request') === 'true';
    }

    /**
     * 处理重定向响应
     */
    protected function handleRedirectResponse($response)
    {
        $location = $response->headers->get('Location');
        
        // 创建新的响应，使用HX-Redirect头
        return response('')
            ->header('HX-Redirect', $location)
            ->setStatusCode(204);
    }
}
