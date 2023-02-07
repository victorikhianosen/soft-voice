<?php

namespace App\Http\Services;

use App\Http\Traits\Paginatable;
use App\Http\Resources\TransactionResource;
use App\Http\Repositories\TransactionRepository;

class TransactionService
{
    use Paginatable;

    public function __construct(
        protected TransactionRepository $transactionRepository
    )
    {

    }

    public function getAll(array $data)
    {
        $result = $this->transactionRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => request()->user()->id],
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
        );
        return [
            'data' => TransactionResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }
}
