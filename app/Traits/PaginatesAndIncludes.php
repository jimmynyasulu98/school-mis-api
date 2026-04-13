<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginatesAndIncludes
{
    /**
     * Apply pagination and includes to a query
     *
     * @param Builder $query
     * @param Request $request
     * @param int $defaultPerPage
     * @return mixed
     */
    protected function applyPaginationAndIncludes(Builder $query, Request $request, int $defaultPerPage = 10)
    {
        // Get per_page from request, default to $defaultPerPage, max 100
        $perPage = min((int) $request->query('per_page', $defaultPerPage), 100);
        $perPage = max($perPage, 1); // At least 1

        // Handle includes (related resources to load)
        if ($request->has('includes')) {
            $includes = explode(',', $request->query('includes'));
            $includes = array_filter($includes);

            if (!empty($includes)) {
                $query->with($includes);
            }
        }

        // Apply default eager loading if method exists
        if (method_exists($this, 'defaultIncludes')) {
            $defaultIncludesArray = $this->defaultIncludes();
            if (!empty($defaultIncludesArray)) {
                $query->with($defaultIncludesArray);
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Get metadata for pagination documentation
     *
     * @return array
     */
    protected function getPaginationMetadata(): array
    {
        return [
            'per_page' => [
                'description' => 'Number of records per page (max 100, default 10)',
                'type' => 'integer',
                'example' => 10,
            ],
            'includes' => [
                'description' => 'Comma-separated list of related resources to include',
                'type' => 'string',
                'example' => 'includes=relation1,relation2',
            ],
        ];
    }
}
