<?php

namespace App\Data;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[RequestBody(
    request: "CreateUserGroupData",
    required: true,
    content: new JsonContent(ref: "#/components/schemas/CreateUserGroupData"),
)]
#[Schema(title: "Create User Group Data")]
#[MapName(SnakeCaseMapper::class)]
class CreateUserGroupData extends Data
{
    public function __construct(
        #[Property(description: 'User group name')]
        #[Max(255)]
        #[Unique('user_groups', 'name')]
        public string $name,

        #[Property(description: 'User group description')]
        public ?string $description,

        #[Property(description: 'Array of user IDs to add to the group')]
        #[ArrayType]
        public ?array $userIds,
    ) { }
}
