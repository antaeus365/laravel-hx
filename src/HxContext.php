<?php

namespace YourVendor\LaravelHx;

class HxContext
{
    /**
     * 当前组件实例
     */
    protected static ?HxComponent $currentComponent = null;

    /**
     * 组件栈（支持嵌套组件）
     */
    protected static array $componentStack = [];

    /**
     * 设置当前组件
     */
    public static function setCurrent(HxComponent $component): void
    {
        self::$componentStack[] = $component;
        self::$currentComponent = $component;
    }

    /**
     * 获取当前组件
     */
    public static function current(): ?HxComponent
    {
        return self::$currentComponent;
    }

    /**
     * 获取当前组件ID
     */
    public static function currentId(): ?int
    {
        return self::$currentComponent?->getId();
    }

    /**
     * 获取当前组件名称
     */
    public static function currentName(): ?string
    {
        if (!self::$currentComponent) {
            return null;
        }

        return class_basename(self::$currentComponent);
    }

    /**
     * 获取当前组件标识符
     */
    public static function currentIdentifier(): ?string
    {
        return self::$currentComponent?->getIdentifier();
    }

    /**
     * 清除当前组件
     */
    public static function clear(): void
    {
        array_pop(self::$componentStack);
        self::$currentComponent = end(self::$componentStack) ?: null;
    }

    /**
     * 检查是否有当前组件
     */
    public static function hasCurrent(): bool
    {
        return self::$currentComponent !== null;
    }

    /**
     * 获取组件栈
     */
    public static function getComponentStack(): array
    {
        return self::$componentStack;
    }

    /**
     * 重置上下文
     */
    public static function reset(): void
    {
        self::$currentComponent = null;
        self::$componentStack = [];
    }
}