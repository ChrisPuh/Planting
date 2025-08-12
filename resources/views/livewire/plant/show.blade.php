<?php

use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use Livewire\Volt\Component;

new class extends Component {
    protected PlantViewModel $plant;

    public function mount(int $id): void
    {
        // Dummy Timeline Events erstellen
        $dummyTimelineEvents = $this->createDummyTimelineEvents();

        $this->plant = new PlantViewModel(
            id: 1,
            name: 'Rote Beete',
            type: 'Gemüse',
            image_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Beets-Bundle.jpg/330px-Beets-Bundle.jpg',
            category: 'Wurzelgemüse',
            latin_name: null,
            description: 'Die Rote Beete ist eine Wurzelgemüseart, die für ihre leuchtend rote Farbe bekannt ist. Sie wird oft in Salaten, Suppen und als Beilage verwendet. Reich an Vitaminen und Mineralien, ist sie auch für ihre gesundheitsfördernden Eigenschaften bekannt.',

            requested_by: auth()->user()?->is_admin ? 'Max Mustermann' : null,
            requested_at: now()->subDays(10),
            created_by: auth()->user()?->is_admin ? 'Admin User' : null,
            created_at: now()->subDays(5),
            updated_by: auth()->user()?->is_admin ? 'Max Mustermann' : null,
            updated_at: now()->subDays(2),
            deleted_by: auth()->user()?->is_admin ? 'Admin User' : null,
            deleted_at: now()->subDays(1),

            // Timeline Events übergeben
            timelineEvents: $dummyTimelineEvents
        );
    }

    private function createDummyTimelineEvents(): array
    {
        $isAdmin = auth()->user()?->is_admin ?? false;
        $createdAt = now()->subDays(5);
        $updatedAt = now()->subDays(2);
        $deletedAt = now()->subDays(1);

        return [
            // 1. Plant Request
            TimelineEvent::requested(
                'Max Mustermann',
                now()->subDays(10)->format('d.m.Y H:i'),
                $isAdmin
            ),

            // 2. Creation
            TimelineEvent::created(
                'Admin User',
                $createdAt->format('d.m.Y H:i'),
                true
            ),

            // 3. Erste Contribution Request
            TimelineEvent::updateRequested(
                'Lisa Weber',
                $createdAt->copy()->addHours(6)->format('d.m.Y H:i'),
                $isAdmin,
                ['Beschreibung']
            ),

            // 4. Weitere Contribution Request
            TimelineEvent::updateRequested(
                'Thomas Müller',
                $createdAt->copy()->addHours(18)->format('d.m.Y H:i'),
                $isAdmin,
                ['Botanischer Name']
            ),

            // 5. Admin genehmigt beide
            TimelineEvent::updated(
                'Admin User',
                $createdAt->copy()->addDays(1)->format('d.m.Y H:i'),
                true,
                ['Beschreibung', 'Botanischer Name']
            ),

            // 6. User korrigiert Kategorie
            TimelineEvent::updateRequested(
                'Maria Schmidt',
                $createdAt->copy()->addDays(2)->format('d.m.Y H:i'),
                $isAdmin,
                ['Kategorie']
            ),

            // 7. Admin macht eigene Korrektur
            TimelineEvent::updated(
                'Admin User',
                $createdAt->copy()->addDays(2)->addHours(2)->format('d.m.Y H:i'),
                true,
                ['Kategorie']
            ),

            // 8. Bulk Contribution Request
            TimelineEvent::updateRequested(
                'Garten-Experte Klaus',
                $createdAt->copy()->addDays(3)->format('d.m.Y H:i'),
                $isAdmin,
                ['Beschreibung', 'Kategorie', 'Botanischer Name']
            ),

            // 9. Admin genehmigt teilweise
            TimelineEvent::updated(
                'Max Mustermann',
                $updatedAt->format('d.m.Y H:i'),
                true,
                ['Beschreibung']
            ),

            // 10. User ergänzt Info
            TimelineEvent::updateRequested(
                'Botanik-Student Jan',
                $updatedAt->copy()->addDays(1)->format('d.m.Y H:i'),
                $isAdmin,
                ['Botanischer Name']
            ),

            // 11. Community Korrektur
            TimelineEvent::updateRequested(
                'Profi-Gärtnerin Anna',
                $updatedAt->copy()->addDays(2)->format('d.m.Y H:i'),
                $isAdmin,
                ['Beschreibung', 'Kategorie']
            ),

            // 12. Delete
            TimelineEvent::deleted(
                'Admin User',
                $deletedAt->format('d.m.Y H:i'),
                $isAdmin
            ),

            // 13. Restore
            TimelineEvent::restored(
                'Admin User',
                $deletedAt->copy()->addMinutes(30)->format('d.m.Y H:i'),
                $isAdmin
            ),

            // 14. Nach Restore
            TimelineEvent::updateRequested(
                'Community-Helper Max',
                $deletedAt->copy()->addHours(2)->format('d.m.Y H:i'),
                $isAdmin,
                ['Beschreibung']
            ),
        ];
    }
}; ?>

<x-plants.layout.show
    :id="$this->plant->id"
    :name="$this->plant->name"
    :isDeleted="$this->plant->isDeleted()"
    :wasUserCreateRequested="$this->plant->wasUserCreateRequest()"
>
    <x-plants.cards.plant-show-card
        :header="$this->plant->getHeader()"
        :details="$this->plant->getDetails()"
        :metadata="$this->plant->getMetadata()"
        :actions="$this->plant->getActions()"
    />
</x-plants.layout.show>
