<?php

namespace App\Http\Services;

use App\Http\Repositories\UserRepository;
use App\Http\Resources\ListUserResource;
use App\Http\Resources\UserResource;
use App\Traits\Paginatable;

class UserService
{
    use Paginatable;

    public function __construct(
        protected UserRepository $userRepository
    )
    {
    }

    public function getAll(array $data, int $userId = null)
    {
        $conditions = [];
        if ($userId) $conditions['user_id'] = $userId;
        if (isset($data['type']) && $data['type']) $conditions['type'] = $data['type'];
        $result = $this->userRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            $conditions,
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
            ['wallet']
        );
        return [
            'data' => ListUserResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }
}
