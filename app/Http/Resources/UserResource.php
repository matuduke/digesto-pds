<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Override;

#[Schema(
    title: 'UserResource',
    properties: [
        new Property(property: 'id', type: 'integer', example: 4),
        new Property(property: 'name', type: 'string', example: 'Matias Perez'),
        new Property(property: 'email', type: 'string', example: 'matias@example.com'),
    ]
)]
/**
 * @property-read User $resource
 */
class UserResource extends GenericResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
        ];
    }
}
