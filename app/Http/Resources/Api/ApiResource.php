<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiResource extends JsonResource
{
    public function with($request): array
    {
        return [];
    }

    public static function paginatedResponse(
        \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator,
        string $resourceClass,
    ): array {
        return [
            'data'  => $resourceClass::collection($paginator->items())->toArray(request()),
            'meta'  => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
                'self'  => $paginator->url($paginator->currentPage()),
            ],
        ];
    }
}
