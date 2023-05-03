<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LoanCollection extends ResourceCollection
{
    /**
     * @return array
     */
    public function toArrayWithPagination(): array
    {
        return [
            'total_pages' => $this->resource->total(),
            'current_page' => $this->resource->currentPage(),
            'records_per_page' => $this->resource->perPage(),
            'loans' => $this->collection
        ];
    }
}
