<?php

namespace YourVendor\LaravelHx\Tests\Unit;

use PHPUnit\Framework\TestCase;
use YourVendor\LaravelHx\HxManager;
use YourVendor\LaravelHx\HxComponent;
use YourVendor\LaravelHx\HxContext;

class BasicFunctionalityTest extends TestCase
{
    public function test_hx_manager_can_be_instantiated()
    {
        $container = $this->createMock(\Illuminate\Contracts\Container\Container::class);
        $manager = new HxManager($container);
        
        $this->assertInstanceOf(HxManager::class, $manager);
    }

    public function test_hx_component_abstract_class_exists()
    {
        $this->assertTrue(class_exists(HxComponent::class));
    }

    public function test_hx_context_class_exists()
    {
        $this->assertTrue(class_exists(HxContext::class));
    }

    public function test_helper_functions_exist()
    {
        $this->assertTrue(function_exists('hx'));
        $this->assertTrue(function_exists('hx_action'));
        $this->assertTrue(function_exists('hx_component'));
        $this->assertTrue(function_exists('hx_oob'));
    }
}