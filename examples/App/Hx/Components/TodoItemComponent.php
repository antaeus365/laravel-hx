<?php

namespace App\Hx\Components;

use YourVendor\LaravelHx\HxComponent;

class TodoItemComponent extends HxComponent
{
    public string $title;
    public bool $completed = false;

    public function view(): string
    {
        return 'hx.components.todo-item';
    }

    public function actions(): array
    {
        return [
            'toggle' => function() {
                $this->completed = !$this->completed;
                return $this;
            },
            'delete' => function() {
                // 这里可以添加删除逻辑
                return $this;
            }
        ];
    }
}