<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

/**
 * GenericResourceCollection allows to prepare data as collection of JSON with GeneralResource or any other resource.
 */
class GenericResourceCollection extends ResourceCollection
{
    /**
     * Meta information data.
     */
    private array $meta = ['meta' => []];

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource Resource data
     * @param string $collects Class of resource
     */
    public function __construct(mixed $resource, string $collects = GenericResource::class)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }

    /**
     * Customize the pagination information for the resource.
     *
     * @param Request $request
     * @param array $paginated
     * @param array $default
     *
     * @return array
     */
    public function paginationInformation($request, $paginated, $default)
    {
        $pagination = Arr::only(
            $default['meta'],
            ['current_page', 'last_page', 'per_page', 'total']
        );
        $pagination['links'] = $default['links'];

        $this->addMeta('pagination', $pagination);

        return $this->meta;
    }

    /**
     * Add data to meta.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function addMeta(string $key, mixed $value): void
    {
        $this->meta['meta'][$key] = $value;
    }
}
