<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\AdminService;
use App\Http\Requests\ListAdminRequest;
use App\Http\Repositories\AdminRepository;
use App\Http\Requests\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Http\Traits\RespondWithHttpStatus;

class AdminController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected AdminRepository $adminRepository,
        protected AdminService $adminService
    ) {
    }

    public function index(ListAdminRequest $request): JsonResponse
    {
        $result = $this->adminService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Admin $admin): JsonResponse
    {
        return $this->success(data: new AdminResource($admin));
    }

    public function store(AdminRequest $request): JsonResponse
    {
        $admin = $this->adminService->store($request->all());
        return $this->success("Admin created", $admin);
    }

    public function update(AdminRequest $request, Admin $admin): JsonResponse
    {
        $admin = $this->adminService->update($admin, $request->all());
        return $this->success("Admin updated", $admin);
    }

    public function restrict(Admin $admin): JsonResponse
    {
        $this->adminRepository->update($admin, ['active' => 0]);
        return $this->success("Admin restricted successfully");
    }

    public function reinstate(Admin $admin): JsonResponse
    {
        $this->adminRepository->update($admin, ['active' => 1]);
        return $this->success("Admin reinstated successfully");
    }
}
