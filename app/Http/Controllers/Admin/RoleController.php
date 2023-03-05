<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Repositories\RoleRepository;
use App\Http\Requests\RoleRequest;
use App\Http\Services\RoleService;
use App\Http\Traits\RespondWithHttpStatus;

class RoleController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected RoleRepository $roleRepository,
        protected RoleService $roleService
    ){
    }

    public function index()
    {
        $data = $this->roleRepository->getAll('*');
        return $this->success(data: RoleResource::collection($data));
    }

    public function show(Role $role)
    {
        return $this->success(data: new RoleResource($role));
    }

    public function store(RoleRequest $request)
    {
        $role = $this->roleService->store($request->all());
        return $this->success("Role Created Successfully", new RoleResource($role));
    }

    public function update(Role $role, RoleRequest $request)
    {
        $role = $this->roleService->update($role, $request->all());
        return $this->success("Role Updated Successfully", new RoleResource($role));
    }

    public function delete(Role $role)
    {
        $role = $this->roleService->delete($role);
        return $this->success("Role Deleted Successfully");
    }
}
