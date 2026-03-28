<?php

namespace Antaeus365\LaravelHx;

use Illuminate\View\View;
use Antaeus365\LaravelHx\Exceptions\HxException;

abstract class HxComponent
{
    /**
     * 组件ID
     */
    public ?int $id = null;

    /**
     * 组件数据
     */
    protected array $data = [];

    /**
     * HTMX管理器实例
     */
    protected HxManager $hx;

    /**
     * 当前请求的动作
     */
    protected ?string $action = null;

    public function __construct()
    {
        $this->hx = app(HxManager::class);
    }

    /**
     * 设置组件ID
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * 获取组件ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 设置组件数据
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 获取组件数据
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 获取组件视图
     */
    abstract public function view(): string;

    /**
     * 定义组件动作
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * 执行指定动作
     */
    public function callAction(string $actionName): mixed
    {
        $actions = $this->actions();
        
        if (!isset($actions[$actionName])) {
            throw new HxException("Action '{$actionName}' not found in component");
        }

        $this->action = $actionName;
        
        $result = $actions[$actionName]($this);
        
        // 动作执行后自动渲染
        return $this->render();
    }

    /**
     * 渲染组件
     */
    public function render(): View
    {
        $viewName = $this->getViewName();
        $viewData = $this->getViewData();
        
        $view = view($viewName, $viewData);
        
        // 如果是HTMX请求，添加必要的响应头
        if ($this->hx->isHtmxRequest()) {
            $headers = [
                'HX-Retarget' => '#' . $this->hx->getComponentIdentifier($this),
                'HX-Reswap' => 'outerHTML'
            ];
            
            // 添加自定义响应头
            $customHeaders = $this->hx->getHeaders();
            if (!empty($customHeaders)) {
                $headers = array_merge($headers, $customHeaders);
                $this->hx->clearHeaders();
            }
            
            // 添加事件触发
            $events = $this->hx->getEvents();
            if (!empty($events)) {
                $headers['HX-Trigger'] = json_encode($this->formatEvents($events));
                $this->hx->clearEvents();
            }
            
            $view->withHeaders($headers);
        }
        
        return $view;
    }

    /**
     * 获取视图名称
     */
    protected function getViewName(): string
    {
        return $this->view();
    }

    /**
     * 获取视图数据
     */
    protected function getViewData(): array
    {
        return array_merge($this->data, [
            'component' => $this,
            'hx' => $this->hx
        ]);
    }

    /**
     * 获取当前动作
     */
    public function getCurrentAction(): ?string
    {
        return $this->action;
    }

    /**
     * 检查是否有指定动作
     */
    public function hasAction(string $actionName): bool
    {
        return array_key_exists($actionName, $this->actions());
    }

    /**
     * 获取组件标识符
     */
    public function getIdentifier(): string
    {
        return $this->hx->getComponentIdentifier($this);
    }

    /**
     * 触发事件
     */
    public function trigger(string $event, array $details = []): self
    {
        $this->hx->trigger($event, $details);
        return $this;
    }

    /**
     * 添加响应头
     */
    public function header(string $key, string $value): self
    {
        $this->hx->header($key, $value);
        return $this;
    }

    /**
     * 格式化事件数据
     */
    protected function formatEvents(array $events): array
    {
        $formatted = [];
        
        foreach ($events as $event) {
            if (empty($event['details'])) {
                $formatted[] = $event['event'];
            } else {
                $formatted[$event['event']] = $event['details'];
            }
        }
        
        return $formatted;
    }
}
