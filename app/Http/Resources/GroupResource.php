<?php

namespace App\Http\Resources;

use App\Models\Group;
use Illuminate\Http\Request;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'UserGroupResource',
    properties: [
        new Property(property: Group::ID, type: 'integer', example: 1),
        new Property(property: Group::NAME, type: 'string', example: 'Administradores'),
        new Property(property: Group::DESCRIPTION, type: 'string', example: 'Grupo de administradores del sistema'),
    ]
)]
/**
 * @property-read UserGroup $resource
 */
class GroupResource extends GenericResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
