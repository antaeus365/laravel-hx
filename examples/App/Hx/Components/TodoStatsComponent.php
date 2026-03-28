<?php

namespace App\Hx\Components;

use Antaeus365\LaravelHx\HxComponent;

class TodoStatsComponent extends HxComponent
{
    public int $total = 0;
    public int $completed = 0;
    public int $remaining = 0;

    public function __construct()
    {
        parent::__construct();
        $this->calculateStats();
    }

    public function view(): string
    {
        return 'hx.components.todo-stats';
    }

    public function actions(): array
    {
        return [
            'refresh' => function() {
                $this->calculateStats();
                return $this;
            }
        ];
    }

    protected function calculateStats(): void
    {
        // 这里应该从实际数据源获取统计数据
        // 现在使用模拟数据
        $this->total = 5;
        $this->completed = 2;
        $this->remaining = 3;
    }
}
