<?php

namespace App\Domains\RequestManagement\Aggregates;

use App\Domains\RequestManagement\Events\PlantCreationRequested;
use App\Domains\RequestManagement\Events\PlantUpdateRequested;
use App\Domains\RequestManagement\Events\RequestApproved;
use App\Domains\RequestManagement\Events\RequestRejected;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class RequestAggregate extends AggregateRoot
{
    // Aggregate state properties
    //TODO implement a proper state management system with enum states
    private string $status = 'pending';
    private string $requestType = '';
    private string $requestedBy = '';
    private ?string $plantId = null;
    private array $proposedData = [];
    private string $reason = '';
    private ?string $reviewedBy = null;
    private ?string $reviewComment = null;
    private string $requestedAt = '';
    private ?string $reviewedAt = null;

    /**
     * Submit a request for creating a new plant
     */
    public function submitPlantCreationRequest(
        array  $proposedData,
        // TODO implement an enum for reasons
        string $reason,
        string $requestedBy
    ): self
    {
        // Validate request data
        $this->validateProposedPlantData($proposedData);
        $this->validateReason($reason);
        $this->validateUserName($requestedBy);

        // Generate target plant UUID for the future plant
        $plantId = \Str::uuid()->toString();

        $this->recordThat(new PlantCreationRequested(
            requestId: $this->uuid(),
            plantId: $plantId,
            proposedData: $proposedData,
            reason: trim($reason),
            requestedBy: $requestedBy,
            requestedAt: now()->toISOString(),
        ));

        return $this;
    }

    /**
     * Submit a request for updating an existing plant
     */
    public function submitUpdateRequest(
        string $plantId,
        array  $proposedChanges,
        string $reason,
        string $requestedBy
    ): self
    {
        // Validate inputs
        $this->validatePlantId($plantId);
        $this->validateProposedChanges($proposedChanges);
        $this->validateReason($reason);
        $this->validateUserName($requestedBy);

        $this->recordThat(new PlantUpdateRequested(
            requestId: $this->uuid(),
            plantId: $plantId,
            proposedChanges: $proposedChanges,
            reason: trim($reason),
            requestedBy: $requestedBy,
            requestedAt: now()->toISOString(),
        ));

        return $this;
    }

    /**
     * Approve the request
     */
    public function approve(?string $comment = null): self
    {
        // Business rule: Only pending requests can be approved
        if ($this->status !== 'pending') {
            throw new \DomainException('Only pending requests can be approved');
        }

        // Validate comment if provided
        if ($comment !== null) {
            $this->validateComment($comment);
        }

        $this->recordThat(new RequestApproved(
            requestId: $this->uuid(),
            reviewedBy: auth()->user()->name ?? 'System',
            reviewedAt: now()->toISOString(),
            comment: $comment ? trim($comment) : null,
        ));

        return $this;
    }

    /**
     * Reject the request with mandatory comment
     */
    public function reject(string $comment): self
    {
        // Business rule: Only pending requests can be rejected
        if ($this->status !== 'pending') {
            throw new \DomainException('Only pending requests can be rejected');
        }

        // Comment is mandatory for rejection
        $this->validateComment($comment, true);

        $this->recordThat(new RequestRejected(
            requestId: $this->uuid(),
            reviewedBy: auth()->user()->name ?? 'System',
            reviewedAt: now()->toISOString(),
            comment: trim($comment),
        ));

        return $this;
    }

    /**
     * Check if request can be modified (only pending requests)
     */
    public function canBeModified(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is ready for approval workflow
     */
    public function isReadyForReview(): bool
    {
        return $this->status === 'pending' &&
            !empty($this->proposedData) &&
            !empty($this->reason);
    }

    // ===== Event Handlers for State Reconstruction =====

    /**
     * Apply PlantCreationRequested event
     */
    public function applyPlantCreationRequested(PlantCreationRequested $event): void
    {
        $this->requestType = 'new_plant';
        $this->status = 'pending';
        $this->plantId = $event->plantId;
        $this->proposedData = $event->proposedData;
        $this->reason = $event->reason;
        $this->requestedBy = $event->requestedBy;
        $this->requestedAt = $event->requestedAt;
    }

    /**
     * Apply PlantUpdateRequested event
     */
    public function applyPlantUpdateRequested(PlantUpdateRequested $event): void
    {
        $this->requestType = 'update_contribution';
        $this->status = 'pending';
        $this->plantId = $event->plantId;
        $this->proposedData = $event->proposedChanges; // Store changes as proposed data
        $this->reason = $event->reason;
        $this->requestedBy = $event->requestedBy;
        $this->requestedAt = $event->requestedAt;
    }

    /**
     * Apply RequestApproved event
     */
    public function applyRequestApproved(RequestApproved $event): void
    {
        $this->status = 'approved';
        $this->reviewedBy = $event->reviewedBy;
        $this->reviewedAt = $event->reviewedAt;
        $this->reviewComment = $event->comment;
    }

    /**
     * Apply RequestRejected event
     */
    public function applyRequestRejected(RequestRejected $event): void
    {
        $this->status = 'rejected';
        $this->reviewedBy = $event->reviewedBy;
        $this->reviewedAt = $event->reviewedAt;
        $this->reviewComment = $event->comment;
    }

    // ===== Domain Validation Methods =====

    private function validateProposedPlantData(array $proposedData): void
    {
        // Required fields for new plant creation
        $requiredFields = ['name', 'type'];

        foreach ($requiredFields as $field) {
            if (!isset($proposedData[$field]) || empty(trim($proposedData[$field]))) {
                throw new \InvalidArgumentException("Field '{$field}' is required for plant creation");
            }
        }

        // Validate individual fields
        $this->validatePlantName($proposedData['name']);
        $this->validatePlantType($proposedData['type']);

        // Optional fields validation
        if (isset($proposedData['category']) && !empty($proposedData['category'])) {
            $this->validateCategory($proposedData['category']);
        }

        if (isset($proposedData['latin_name']) && !empty($proposedData['latin_name'])) {
            $this->validateLatinName($proposedData['latin_name']);
        }

        if (isset($proposedData['image_url']) && !empty($proposedData['image_url'])) {
            $this->validateImageUrl($proposedData['image_url']);
        }

        // Check for unknown fields
        $allowedFields = ['name', 'type', 'category', 'latin_name', 'description', 'image_url'];
        $unknownFields = array_diff(array_keys($proposedData), $allowedFields);

        if (!empty($unknownFields)) {
            throw new \InvalidArgumentException(
                'Unknown fields in proposed data: ' . implode(', ', $unknownFields)
            );
        }
    }

    private function validateProposedChanges(array $proposedChanges): void
    {
        if (empty($proposedChanges)) {
            throw new \InvalidArgumentException('Proposed changes cannot be empty');
        }

        // Validate that we have valid fields to change
        $allowedFields = ['name', 'type', 'category', 'latin_name', 'description', 'image_url'];
        $invalidFields = array_diff(array_keys($proposedChanges), $allowedFields);

        if (!empty($invalidFields)) {
            throw new \InvalidArgumentException(
                'Invalid fields in proposed changes: ' . implode(', ', $invalidFields)
            );
        }

        // Validate individual field values
        foreach ($proposedChanges as $field => $value) {
            if ($value === null || $value === '') {
                continue; // Allow null/empty values for removal
            }

            match ($field) {
                'name' => $this->validatePlantName($value),
                'type' => $this->validatePlantType($value),
                'category' => $this->validateCategory($value),
                'latin_name' => $this->validateLatinName($value),
                'image_url' => $this->validateImageUrl($value),
                'description' => $this->validateDescription($value),
                default => null
            };
        }
    }

    private function validatePlantId(string $plantId): void
    {
        if (empty(trim($plantId))) {
            throw new \InvalidArgumentException('Plant ID cannot be empty');
        }

        // Validate UUID format
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $plantId)) {
            throw new \InvalidArgumentException('Plant ID must be a valid UUID');
        }
    }

    private function validateReason(string $reason): void
    {
        $trimmedReason = trim($reason);

        if (empty($trimmedReason)) {
            throw new \InvalidArgumentException('Reason cannot be empty');
        }

        if (strlen($trimmedReason) < 10) {
            throw new \InvalidArgumentException('Reason must be at least 10 characters long');
        }

        if (strlen($trimmedReason) > 500) {
            throw new \InvalidArgumentException('Reason cannot exceed 500 characters');
        }
    }

    private function validateUserName(string $userName): void
    {
        $trimmedName = trim($userName);

        if (empty($trimmedName)) {
            throw new \InvalidArgumentException('User name cannot be empty');
        }

        if (strlen($trimmedName) > 100) {
            throw new \InvalidArgumentException('User name cannot exceed 100 characters');
        }
    }

    private function validateComment(string $comment, bool $mandatory = false): void
    {
        $trimmedComment = trim($comment);

        if ($mandatory && empty($trimmedComment)) {
            throw new \InvalidArgumentException('Comment is required for rejection');
        }

        if (!empty($trimmedComment) && strlen($trimmedComment) > 1000) {
            throw new \InvalidArgumentException('Comment cannot exceed 1000 characters');
        }
    }

    // Shared validation methods with PlantAggregate
    private function validatePlantName(string $name): void
    {
        $trimmedName = trim($name);

        if (empty($trimmedName)) {
            throw new \InvalidArgumentException('Plant name cannot be empty');
        }

        if (strlen($trimmedName) < 2) {
            throw new \InvalidArgumentException('Plant name must be at least 2 characters long');
        }

        if (strlen($trimmedName) > 100) {
            throw new \InvalidArgumentException('Plant name cannot exceed 100 characters');
        }

        if (!preg_match('/^[a-zA-ZäöüÄÖÜß0-9\s\-\'\.]+$/u', $trimmedName)) {
            throw new \InvalidArgumentException('Plant name contains invalid characters');
        }
    }

    private function validatePlantType(string $type): void
    {
        $validTypes = ['gemuese', 'obst', 'kraeuter', 'blumen', 'baeume', 'straeucher'];

        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException(
                'Invalid plant type. Allowed: ' . implode(', ', $validTypes)
            );
        }
    }

    private function validateCategory(string $category): void
    {
        if (strlen(trim($category)) > 50) {
            throw new \InvalidArgumentException('Category cannot exceed 50 characters');
        }
    }

    private function validateLatinName(string $latinName): void
    {
        $trimmedLatinName = trim($latinName);

        if (strlen($trimmedLatinName) > 100) {
            throw new \InvalidArgumentException('Latin name cannot exceed 100 characters');
        }

        if (!preg_match('/^[A-Z][a-z]+ [a-z]+/', $trimmedLatinName)) {
            throw new \InvalidArgumentException(
                'Latin name should follow "Genus species" format'
            );
        }
    }

    private function validateImageUrl(string $imageUrl): void
    {
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid image URL format');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException(
                'Image URL must point to a valid image file'
            );
        }
    }

    private function validateDescription(string $description): void
    {
        if (strlen(trim($description)) > 2000) {
            throw new \InvalidArgumentException('Description cannot exceed 2000 characters');
        }
    }

    // ===== Getters for Testing/Debugging =====

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRequestType(): string
    {
        return $this->requestType;
    }

    public function getRequestedBy(): string
    {
        return $this->requestedBy;
    }

    public function getPlantId(): ?string
    {
        return $this->plantId;
    }

    public function getProposedData(): array
    {
        return $this->proposedData;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getReviewedBy(): ?string
    {
        return $this->reviewedBy;
    }

    public function getReviewComment(): ?string
    {
        return $this->reviewComment;
    }

    public function getRequestedAt(): string
    {
        return $this->requestedAt;
    }

    public function getReviewedAt(): ?string
    {
        return $this->reviewedAt;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isNewPlantRequest(): bool
    {
        return $this->requestType === 'new_plant';
    }

    public function isUpdateRequest(): bool
    {
        return $this->requestType === 'update_contribution';
    }
}
