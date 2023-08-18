<?php

namespace App\Http\Services;

use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\Paginatable;
use App\Http\Resources\AdminResource;
use App\Http\Repositories\AdminRepository;
use App\Http\Services\NotificationService\AdminNotificationService;

class AdminService
{
    use Paginatable;

    public function __construct(
        protected AdminRepository $adminRepository,
        protected AdminNotificationService $adminNotificationService,
    ) {
    }

    public function getAll(array $data)
    {
        $result = $this->adminRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            [],
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
        );
        return [
            'data' => AdminResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): AdminResource
    {
        $password = Str::random(8);
        $data['password'] = bcrypt($password);
        $admin = $this->adminRepository->create($data);
        $admin->assignRole($data['role_id']);
        $this->adminNotificationService->sendWelcomeNotification($admin, $password);
        return new AdminResource($admin->fresh());
    }

    public function update(Admin $admin, array $data): AdminResource
    {
        $data = Arr::only($data, ['name', 'role_id']);
        $this->adminRepository->update($admin, $data);
        $admin->syncRoles($data['role_id']);
        return new AdminResource($admin->fresh());
    }
}
