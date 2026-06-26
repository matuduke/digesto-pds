<?php

namespace App\Http\Resources;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Generic resource transforms data with setting NULL values as empty line and allows to set includes in data.
 */
class GenericResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Save an array of includes have in the request.
     */
    private array $includes = [];

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request  Http request
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $includes = $this->prepareIncludes($request);

        return array_merge($data, $includes);
    }

    /**
     * Prepare list of available includes with their functions for getting data.
     *
     * @return array<string, Closure>
     */
    protected function getAvailableIncludesFunctions(): array
    {
        return [];
    }

    /**
     * Prepare list of default includes with their functions for getting data.
     *
     * @return array<string, Closure>
     */
    protected function getDefaultIncludesFunctions(): array
    {
        return [];
    }

    /**
     * Get all includes from request and parse it to an array
     */
    private function loadIncludes(Request $request): void
    {
        if ($request->has('include')) {
            $this->includes = explode(',', $request->input('include'));
        }
    }

    /**
     * Checks if an include has present in array includes
     *
     * @param  string  $include  Name of include
     */
    private function hasInclude(string $include): bool
    {

        return in_array($include, $this->includes);
    }

    /**
     * Prepare array of includes.
     *
     * @param  Request  $request  Http request.
     * @return array<string, MissingValue|mixed>
     */
    protected function prepareIncludes(Request $request): array
    {
        $this->loadIncludes($request);
        $includes = [];

        $availableIncludesFunctions = $this->getAvailableIncludesFunctions();

        foreach ($availableIncludesFunctions as $include => $func) {
            $includes[$include] = $this->includeIfNeed($include, $func);
        }

        $defaultIncludesFunctions = $this->getDefaultIncludesFunctions();

        foreach ($defaultIncludesFunctions as $include => $func) {
            $includes[$include] = $func();
        }

        return $includes;
    }

    /**
     * Get missing data if it takes.
     *
     * @param  string  $includeName  Include name
     * @param  Closure  $func  Function for getting include data
     * @return MissingValue|mixed
     */
    private function includeIfNeed(string $includeName, Closure $func): mixed
    {
        return $this->when($this->hasInclude($includeName), $func);
    }

    /**
     * Set default includes.
     *
     *
     * @return $this
     */
    public function setDefaultIncludes(array|string $includes): self
    {
        if (is_string($includes)) {
            $includes = explode(',', trim($includes));
        }

        $this->includes = $includes;

        return $this;
    }
}
