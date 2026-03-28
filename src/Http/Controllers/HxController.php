<?php

namespace Antaeus365\LaravelHx\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Antaeus365\LaravelHx\HxManager;
use Antaeus365\LaravelHx\HxContext;
use Antaeus365\LaravelHx\Exceptions\HxException;

class HxController extends Controller
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
     * 显示组件
     */
    public function show(Request $request, string $componentName, int $id)
    {
        $componentClass = $this->resolveComponentClass($componentName);
        
        if (!class_exists($componentClass)) {
            abort(404, "Component {$componentClass} not found");
        }

        $component = new $componentClass();
        $component->setId($id);
        
        // 设置上下文
        HxContext::setCurrent($component);

        try {
            $response = $component->render();
            
            // 处理OOB更新
            $oobUpdates = $this->hx->getOobUpdates();
            if (!empty($oobUpdates)) {
                $response = $this->appendOobUpdates($response, $oobUpdates);
                $this->hx->clearOobUpdates();
            }
            
            return $response;
        } finally {
            HxContext::clear();
        }
    }

    /**
     * 执行组件动作
     */
    public function action(Request $request, string $componentName, int $id, string $action)
    {
        $componentClass = $this->resolveComponentClass($componentName);
        
        if (!class_exists($componentClass)) {
            abort(404, "Component {$componentClass} not found");
        }

        $component = new $componentClass();
        $component->setId($id);
        
        // 设置上下文
        HxContext::setCurrent($component);

        try {
            if (!$component->hasAction($action)) {
                abort(404, "Action {$action} not found in component");
            }

            $response = $component->callAction($action);
            
            // 处理OOB更新
            $oobUpdates = $this->hx->getOobUpdates();
            if (!empty($oobUpdates)) {
                $response = $this->appendOobUpdates($response, $oobUpdates);
                $this->hx->clearOobUpdates();
            }
            
            return $response;
        } finally {
            HxContext::clear();
        }
    }

    /**
     * 解析组件类名
     */
    protected function resolveComponentClass(string $componentName): string
    {
        $namespace = config('hx.component_namespace', 'App\\Hx\\Components');
        $className = str_replace('-', '', ucwords($componentName, '-'));
        
        return "{$namespace}\\{$className}Component";
    }

    /**
     * 附加OOB更新到响应中
     */
    protected function appendOobUpdates($response, array $oobUpdates)
    {
        $oobHtml = '';
        
        foreach ($oobUpdates as $update) {
            $view = view($update['view'], $update['data']);
            $oobHtml .= $view->render();
        }
        
        if ($oobHtml) {
            $content = $response->getContent();
            $response->setContent($content . $oobHtml);
        }
        
        return $response;
    }
}
