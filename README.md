# Laravel HTMX Plugin

一个声明式、服务端驱动的 HTMX UI 框架 for Laravel。

## 🎯 核心理念

- **UI = f(state) → HTML** - 纯粹的状态到HTML转换
- **只返回HTML** - 彻底告别JSON API
- **零前端状态** - 所有状态都在服务端管理
- **HTTP + HTML patch** - 所有交互都是HTTP请求和HTML片段更新

## 🚀 快速开始

### 1. 安装

```bash
composer require your-vendor/laravel-hx
```

### 2. 发布配置

```bash
php artisan vendor:publish --provider="YourVendor\LaravelHx\HxServiceProvider"
```

### 3. 创建第一个组件

```php
// app/Hx/Components/UserProfileComponent.php
namespace App\Hx\Components;

use YourVendor\LaravelHx\HxComponent;

class UserProfileComponent extends HxComponent
{
    public string $name;
    public string $email;
    public bool $isActive = true;

    public function view(): string
    {
        return 'hx.components.user-profile';
    }

    public function actions(): array
    {
        return [
            'toggleStatus' => function() {
                $this->isActive = !$this->isActive;
                // 保存到数据库...
                return $this;
            },
            'updateEmail' => function() {
                // 更新邮箱逻辑...
                return $this;
            }
        ];
    }
}
```

### 4. 创建视图模板

```blade
{{-- resources/views/hx/components/user-profile.blade.php --}}
<div class="user-profile">
    <h3>{{ $component->name }}</h3>
    <p>Email: {{ $component->email }}</p>
    <p>Status: {{ $component->isActive ? 'Active' : 'Inactive' }}</p>
    
    <button @hxAction('toggleStatus')>
        {{ $component->isActive ? 'Deactivate' : 'Activate' }}
    </button>
</div>
```

### 5. 在Blade中使用

```blade
{{-- 在任何Blade模板中 --}}
<x-hx-wrapper :component="$userProfileComponent" />
```

## 🔧 核心概念

### 组件 (Component)

组件是唯一的抽象单位，包含：
- **状态** - public属性
- **视图** - view()方法返回模板名
- **动作** - actions()方法定义可执行的操作

### 路由系统

自动为组件生成RESTful路由：

```
GET  /hx/user-profile/{id}           # 获取组件HTML
POST /hx/user-profile/{id}/toggleStatus  # 执行动作
```

### 动作 (Action)

组件内部定义的动作会自动映射到路由：

```php
public function actions(): array
{
    return [
        'toggleStatus' => fn() => $this->toggleUserStatus(),
        'delete' => fn() => $this->deleteUser(),
    ];
}
```

## 🛠️ 辅助函数

### hx_action()

生成动作URL：

```blade
<button hx-post="{{ hx_action('toggleStatus') }}">
    Toggle Status
</button>
```

### hx_component()

生成组件URL：

```blade
<div hx-get="{{ hx_component(App\Hx\Components\UserProfileComponent::class, $userId) }}">
    <!-- 组件内容将被加载到这里 -->
</div>
```

## 🎨 Blade指令

### @hxAction

```blade
<button @hxAction('toggleStatus')>
    Toggle
</button>
```

等价于：

```html
<button hx-post="/hx/user-profile/1/toggleStatus" 
        hx-target="#user-profile-1" 
        hx-swap="outerHTML">
    Toggle
</button>
```

## 🚀 高级功能

### 事件系统

组件可以触发自定义事件：

```php
public function actions(): array
{
    return [
        'save' => function() {
            // 保存数据...
            $this->trigger('user-saved', [
                'id' => $this->id,
                'name' => $this->name
            ]);
            return $this;
        }
    ];
}
```

在前端监听事件：

```javascript
document.body.addEventListener('user-saved', function(evt) {
    console.log('用户已保存:', evt.detail);
});
```

### 多组件刷新

同时刷新多个组件：

```php
$this->hx->refresh([
    UserProfileComponent::class => $userId,
    UserStatsComponent::class => 'global',
    NotificationComponent::class => 1
]);
```

### 自定义响应头

添加自定义HTMX响应头：

```php
public function actions(): array
{
    return [
        'delete' => function() {
            // 删除逻辑...
            $this->header('HX-Location', '/dashboard');
            return $this;
        }
    ];
}
```

### 中间件支持

自动处理HTMX特定的请求头和响应：

```php
// 在路由中使用
Route::middleware('hx')->group(function () {
    // HTMX路由组
});
```

### 自动错误处理

验证错误自动转换为HTMX友好的响应：

```php
// 组件动作中的验证
$data = request()->validate([
    'email' => 'required|email|unique:users'
]);
```

失败时自动返回422状态码和错误HTML片段。

## 📋 配置选项

```php
// config/hx.php
return [
    'route_prefix' => 'hx',                    // 路由前缀
    'component_namespace' => 'App\\Hx\\Components', // 组件命名空间
    'component_views' => 'hx.components',      // 视图路径
    'auto_error_handling' => true,             // 自动错误处理
];
```

## 🧪 示例应用

查看 `examples/` 目录中的完整Todo应用示例。

## 📚 API参考

### HxComponent

```php
abstract class HxComponent
{
    // 设置ID
    public function setId(int $id): self
    
    // 获取ID
    public function getId(): ?int
    
    // 设置数据
    public function setData(array $data): self
    
    // 获取数据
    public function getData(): array
    
    // 渲染组件
    public function render(): View
    
    // 检查动作是否存在
    public function hasAction(string $actionName): bool
    
    // 获取组件标识符
    public function getIdentifier(): string
}
```

### HxManager

```php
class HxManager
{
    // 注册组件
    public function component(string $componentClass): self
    
    // 添加OOB更新
    public function oob(string $view, array $data = []): self
    
    // 刷新组件
    public function refresh(array $components): self
    
    // 检查是否为HTMX请求
    public function isHtmxRequest(): bool
}
```

## 🎯 最佳实践

1. **保持组件小巧** - 每个组件只负责一个功能
2. **使用明确的动作名称** - toggleStatus 比 toggle 更清晰
3. **合理使用OOB更新** - 用于全局通知或导航更新
4. **组件间通信** - 通过HX-Trigger事件系统

## 📄 许可证

MIT License