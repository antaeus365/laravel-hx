<?php

namespace App\Hx\Components;

use YourVendor\LaravelHx\HxComponent;
use Illuminate\Validation\Rule;

class TodoListComponent extends HxComponent
{
    public array $todos = [];
    public int $totalCount = 0;
    public int $completedCount = 0;

    public function __construct()
    {
        parent::__construct();
        $this->loadTodos();
    }

    public function view(): string
    {
        return 'hx.components.todo-list';
    }

    public function actions(): array
    {
        return [
            'add' => function() {
                $data = request()->validate([
                    'title' => 'required|string|max:255'
                ]);

                // 模拟添加TODO
                $this->todos[] = [
                    'id' => count($this->todos) + 1,
                    'title' => $data['title'],
                    'completed' => false,
                    'created_at' => now()
                ];

                $this->updateCounts();
                
                // 触发事件通知其他组件
                $this->trigger('todo-added', [
                    'count' => $this->totalCount
                ]);
                
                // 刷新统计组件
                hx()->refresh([
                    TodoStatsComponent::class => 'global'
                ]);

                return $this;
            },

            'clearCompleted' => function() {
                $this->todos = array_filter($this->todos, function($todo) {
                    return !$todo['completed'];
                });
                
                $this->updateCounts();
                
                $this->trigger('todos-cleared', [
                    'remaining' => $this->totalCount
                ]);

                return $this;
            }
        ];
    }

    protected function loadTodos(): void
    {
        // 模拟从数据库加载数据
        $this->todos = [
            ['id' => 1, 'title' => 'Learn HTMX', 'completed' => false, 'created_at' => now()],
            ['id' => 2, 'title' => 'Build Laravel Plugin', 'completed' => true, 'created_at' => now()],
            ['id' => 3, 'title' => 'Deploy Application', 'completed' => false, 'created_at' => now()]
        ];
        
        $this->updateCounts();
    }

    protected function updateCounts(): void
    {
        $this->totalCount = count($this->todos);
        $this->completedCount = count(array_filter($this->todos, function($todo) {
            return $todo['completed'];
        }));
    }
}