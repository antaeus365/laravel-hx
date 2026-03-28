<div class="todo-list-container">
    <!-- 错误显示区域 -->
    <div id="hx-errors"></div>
    
    <!-- 添加TODO表单 -->
    <form hx-post="{{ hx_action('add') }}" hx-target="closest .todo-list-container">
        <div class="input-group mb-3">
            <input type="text" name="title" class="form-control" placeholder="添加新的TODO..." required>
            <button class="btn btn-primary" type="submit">添加</button>
        </div>
    </form>
    
    <!-- TODO列表 -->
    <div class="todo-items">
        @forelse($component->todos as $todo)
            <x-hx.todo-item :todo="$todo" />
        @empty
            <div class="text-center text-muted">暂无TODO项</div>
        @endforelse
    </div>
    
    <!-- 操作按钮 -->
    @if($component->completedCount > 0)
        <div class="mt-3">
            <button class="btn btn-secondary" @hxAction('clearCompleted')>
                清除已完成 ({{ $component->completedCount }})
            </button>
        </div>
    @endif
    
    <!-- 事件监听示例 -->
    <script>
        document.body.addEventListener('todo-added', function(evt) {
            console.log('TODO已添加，总数:', evt.detail.count);
        });
        
        document.body.addEventListener('todos-cleared', function(evt) {
            console.log('已完成项已清除，剩余:', evt.detail.remaining);
        });
    </script>
</div>