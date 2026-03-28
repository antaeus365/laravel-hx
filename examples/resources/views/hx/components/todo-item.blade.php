<div class="todo-item {{ $component->completed ? 'completed' : '' }}">
    <span class="title">{{ $component->title }}</span>
    
    <div class="actions">
        <button @hxAction('toggle') class="btn btn-sm btn-primary">
            {{ $component->completed ? 'Undo' : 'Complete' }}
        </button>
        
        <button @hxAction('delete') class="btn btn-sm btn-danger">
            Delete
        </button>
    </div>
</div>
