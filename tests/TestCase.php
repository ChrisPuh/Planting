<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure Event Sourcing is properly configured
        $this->setupEventSourcing();

        // Clear event store for clean tests
        $this->clearEventStore();
    }

    protected function setupEventSourcing(): void
    {
        // Ensure the event sourcing service provider is loaded
        if (!$this->app->bound('event-sourcing')) {
            $this->app->register(\Spatie\EventSourcing\EventSourcingServiceProvider::class);
        }

        // Set up event sourcing configuration for tests
        config([
            'event-sourcing.stored_event_model' => EloquentStoredEvent::class,
            'event-sourcing.stored_event_repository' => \Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository::class,
            'event-sourcing.snapshot_repository' => \Spatie\EventSourcing\Snapshots\EloquentSnapshotRepository::class,
            'event-sourcing.aggregate_root_repository' => \Spatie\EventSourcing\AggregateRoots\AggregateRootRepository::class,
        ]);
    }

    protected function clearEventStore(): void
    {
        if (class_exists(EloquentStoredEvent::class)) {
            try {
                EloquentStoredEvent::truncate();
            } catch (\Exception $e) {
                // Event store table might not exist yet in some tests
            }
        }
    }

    protected function mockAuthUser(string $name = 'Test User'): void
    {
        // Create a simple user mock
        $user = (object) ['name' => $name];

        // Mock the auth helper
        $this->app->bind('auth', function () use ($user) {
            return new class($user) {
                public function __construct(private $user) {}
                public function user() { return $this->user; }
            };
        });
    }

    // Additional helper methods...
}

trait CreatesApplication
{
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }
}
