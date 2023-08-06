<?php

namespace App\Traits;

trait Paginatable
{
    public function getMeta(array $data, int $total): array
    {
        return [
            "currentPage" => isset($data['current_page']) ? intval($data['current_page']) : 1,
            "perPage"     => isset($data['per_page']) ? intval($data['per_page']) : 50,
            "total"       => $total,
        ];
    }
}
