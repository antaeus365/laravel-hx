<?php

namespace YourVendor\LaravelHx\View\Components;

use Illuminate\View\Component;
use YourVendor\LaravelHx\HxContext;

class HxWrapper extends Component
{
    /**
     * 组件实例
     */
    public object $component;

    /**
     * 额外属性
     */
    public array $attributes;

    public function __construct(object $component, array $attributes = [])
    {
        $this->component = $component;
        $this->attributes = $attributes;
    }

    /**
     * 获取视图内容
     */
    public function render()
    {
        return function (array $data) {
            $component = $data['component'];
            $slot = $data['slot'];
            
            // 设置上下文
            HxContext::setCurrent($component);
            
            try {
                $identifier = $component->getIdentifier();
                
                return "<div id=\"{$identifier}\" {$this->buildAttributes()}>{$slot}</div>";
            } finally {
                HxContext::clear();
            }
        };
    }

    /**
     * 构建HTML属性
     */
    protected function buildAttributes(): string
    {
        $attrs = [];
        
        foreach ($this->attributes as $key => $value) {
            if ($value === true) {
                $attrs[] = $key;
            } elseif ($value !== false && $value !== null) {
                $attrs[] = "{$key}=\"" . e($value) . '"';
            }
        }
        
        return implode(' ', $attrs);
    }
}