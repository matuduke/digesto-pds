<?php

namespace App\Data;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentType;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\RequiredUnless;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[RequestBody(
    request: "CreateDocumentData",
    required: true,
    content: new JsonContent(ref: "#/components/schemas/CreateDocumentData"),
)]
#[Schema(title: "Create document Data")]
#[MapName(SnakeCaseMapper::class)]
class CreateDocumentData extends Data
{
    public function __construct(
        #[Property(property: 'id', description: 'Document ID')]
        #[Exists(Document::class, 'id')]
        public ?int $id,

        #[Property(property: 'document_type_id', description: 'Document type ID')]
        #[Exists(DocumentType::class, 'id')]
        public int $documentTypeId,

        #[Property(property: 'status', description: 'Document status')]
        #[Enum(DocumentStatus::class)]
        public string $status,

        #[Property(property: 'number', description: 'Document number')]
        #[Max(255)]
        #[RequiredUnless('status', 'borrador')]
        public ?string $number,

        #[Property(property: 'content', description: 'Document content')]
        #[RequiredUnless('status', 'borrador')]
        public ?string $content,

        #[Property(property: 'authorized_user_ids', description: 'Authorized users')]
        public ?string $auuthorizedUserIds,

        #[Property(property: 'authorized_group_ids', description: 'Authorized groups')]
        public ?string $auuthorizedGroupIds,
    ) { }
}
