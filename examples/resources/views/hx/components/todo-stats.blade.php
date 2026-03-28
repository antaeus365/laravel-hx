<div class="todo-stats card">
    <div class="card-body">
        <h5 class="card-title">统计信息</h5>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-label">总计:</span>
                <span class="stat-value">{{ $component->total }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">已完成:</span>
                <span class="stat-value text-success">{{ $component->completed }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">待完成:</span>
                <span class="stat-value text-warning">{{ $component->remaining }}</span>
            </div>
        </div>
        
        <!-- 刷新按钮 -->
        <button class="btn btn-sm btn-outline-primary mt-2" @hxAction('refresh')>
            刷新统计
        </button>
    </div>
</div>
