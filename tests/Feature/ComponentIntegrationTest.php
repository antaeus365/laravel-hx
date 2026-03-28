<?php

namespace YourVendor\LaravelHx\Tests\Feature;

use PHPUnit\Framework\TestCase;
use YourVendor\LaravelHx\HxManager;
use YourVendor\LaravelHx\HxComponent;
use YourVendor\LaravelHx\HxContext;

class ComponentIntegrationTest extends TestCase
{
    protected $hxManager;
    
    protected function setUp(): void
    {
        $container = $this->createMock(\Illuminate\Contracts\Container\Container::class);
        $this->hxManager = new HxManager($container);
    }

    public function test_component_registration()
    {
        $this->hxManager->component('App\\Hx\\Components\\TestComponent');
        
        $components = $this->hxManager->getComponents();
        $this->assertCount(1, $components);
        $this->assertEquals('App\\Hx\\Components\\TestComponent', $components[0]);
    }

    public function test_event_triggering()
    {
        $this->hxManager->trigger('user-created', ['id' => 1, 'name' => 'John']);
        
        $events = $this->hxManager->getEvents();
        $this->assertCount(1, $events);
        $this->assertEquals('user-created', $events[0]['event']);
        $this->assertEquals(['id' => 1, 'name' => 'John'], $events[0]['details']);
    }

    public function test_oob_updates()
    {
        $this->hxManager->oob('partials.notification', ['message' => 'Success']);
        
        $updates = $this->hxManager->getOobUpdates();
        $this->assertCount(1, $updates);
        $this->assertEquals('partials.notification', $updates[0]['view']);
        $this->assertEquals(['message' => 'Success'], $updates[0]['data']);
    }

    public function test_headers_management()
    {
        $this->hxManager->header('HX-Location', '/dashboard');
        $this->hxManager->header('Custom-Header', 'value');
        
        $headers = $this->hxManager->getHeaders();
        $this->assertCount(2, $headers);
        $this->assertEquals('/dashboard', $headers['HX-Location']);
        $this->assertEquals('value', $headers['Custom-Header']);
    }

    public function test_component_identifier_generation()
    {
        $mockComponent = $this->createMock(HxComponent::class);
        $mockComponent->id = 123;
        
        $identifier = $this->hxManager->getComponentIdentifier($mockComponent);
        $this->assertStringContainsString('123', $identifier);
    }
}