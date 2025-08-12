<?php

namespace App\Domains\Admin\Plants\DTOs;

use Illuminate\Support\Carbon;

class PlantViewDTO
{
    public function __construct(
        public ?int    $id = null,
        public string  $name,
        public string  $type, // Gemüse, Blume, Gras, ...
        public ?string $category = null, // Unterkategorie wie "Kräuter", "Stauden"
        public ?string $latin_name = null, // Botanischer Name
        public ?string $description = null,
        public ?string $image_url = null,
        public bool    $isDeleted,
        public bool    $wasUserCreateRequested,


        /*
        |--------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------
        | These fields are used for tracking the creation, update, and deletion of the plant records
        |--------------------------------------------------------------
         */
        public ?string $requested_by = null,
        public ?string $requested_at = null,

        public ?string $created_by = null,
        public ?string $created_at = null,
        public ?string $updated_by = null,
        public ?string $updated_at = null,

        public ?string $deleted_by = null,
        public ?string $deleted_at = null,
    )
    {
    }
}
