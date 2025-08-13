<?php

namespace App\Console\Commands;

use App\Domains\PlantManagement\Projectors\PlantProjector;
use App\Domains\PlantManagement\Projectors\PlantTimelineProjector;
use App\Domains\RequestManagement\Projectors\RequestQueueProjector;
use App\Models\Plant;
use App\Models\PlantTimelineProjection;
use App\Models\RequestQueueProjection;
use Illuminate\Console\Command;
use Spatie\EventSourcing\Facades\Projectionist;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class ProjectionManagerCommand extends Command
{
    protected $signature = 'projections:manage
                            {action : The action to perform (status|reset|replay)}
                            {--projector= : Specific projector to target}
                            {--force : Force the action without confirmation}';

    protected $description = 'Manage event sourcing projections - check status, reset, or replay events';

    private array $projectors = [
        'plant' => PlantProjector::class,
        'timeline' => PlantTimelineProjector::class,
        'requests' => RequestQueueProjector::class,
    ];

    public function handle(): int
    {
        $action = $this->argument('action');
        $projector = $this->option('projector');

        return match ($action) {
            'status' => $this->showStatus($projector),
            'reset' => $this->resetProjections($projector),
            'replay' => $this->replayEvents($projector),
            default => $this->error("Unknown action: {$action}. Use: status, reset, or replay")
        };
    }

    private function showStatus(?string $projectorName = null): int
    {
        $this->info('🔍 Event Sourcing Projection Status');
        $this->newLine();

        // Show stored events count
        $eventCount = EloquentStoredEvent::count();
        $this->line("📊 Total Stored Events: <fg=blue>{$eventCount}</>");
        $this->newLine();

        if ($projectorName) {
            $this->showProjectorStatus($projectorName);
        } else {
            foreach ($this->projectors as $name => $class) {
                $this->showProjectorStatus($name);
            }
        }

        // Show projection counts
        $this->info('📈 Current Projection Counts:');
        $this->table(['Projection', 'Count'], [
            ['Plants', Plant::count()],
            ['Timeline Events', PlantTimelineProjection::count()],
            ['Request Queue', RequestQueueProjection::count()],
        ]);

        return Command::SUCCESS;
    }

    private function showProjectorStatus(string $projectorName): void
    {
        if (!isset($this->projectors[$projectorName])) {
            $this->error("Unknown projector: {$projectorName}");
            return;
        }

        $projectorClass = $this->projectors[$projectorName];
        $this->info("🎯 {$projectorName} Projector ({$projectorClass})");

        // Get last processed event for this projector
        $lastProcessedEvent = $this->getLastProcessedEvent($projectorClass);

        if ($lastProcessedEvent) {
            $this->line("   Last Event: #{$lastProcessedEvent->id} - {$lastProcessedEvent->event_class}");
            $this->line("   Processed: {$lastProcessedEvent->created_at->diffForHumans()}");
        } else {
            $this->line("   <fg=yellow>No events processed yet</>");
        }

        $this->newLine();
    }

    private function resetProjections(?string $projectorName = null): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  This will delete ALL projection data. Are you sure?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('🔄 Resetting projections...');

        if ($projectorName) {
            $this->resetSpecificProjector($projectorName);
        } else {
            foreach ($this->projectors as $name => $class) {
                $this->resetSpecificProjector($name);
            }
        }

        $this->info('✅ Projections reset successfully!');
        return Command::SUCCESS;
    }

    private function resetSpecificProjector(string $projectorName): void
    {
        if (!isset($this->projectors[$projectorName])) {
            $this->error("Unknown projector: {$projectorName}");
            return;
        }

        $projectorClass = $this->projectors[$projectorName];
        $this->line("   Resetting {$projectorName} projector...");

        // Reset the projector state
        $projector = app($projectorClass);
        if (method_exists($projector, 'resetState')) {
            $projector->resetState();
        }

        // Reset Spatie's tracking of this projector
        Projectionist::reset($projectorClass);
    }

    private function replayEvents(?string $projectorName = null): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('🔄 This will replay all events to rebuild projections. Continue?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('🎬 Replaying events...');

        if ($projectorName) {
            if (!isset($this->projectors[$projectorName])) {
                $this->error("Unknown projector: {$projectorName}");
                return Command::FAILURE;
            }

            $projectorClass = $this->projectors[$projectorName];
            $this->line("   Replaying events for {$projectorName} projector...");
            Projectionist::replay($projectorClass);
        } else {
            $this->line("   Replaying events for all projectors...");
            Projectionist::replay();
        }

        $this->info('✅ Event replay completed!');
        $this->newLine();

        // Show updated status
        return $this->showStatus($projectorName);
    }

    private function getLastProcessedEvent(string $projectorClass): ?EloquentStoredEvent
    {
        // This is a simplified way to get the last processed event
        // In a real implementation, you might want to track this more precisely
        return EloquentStoredEvent::latest()->first();
    }
}
