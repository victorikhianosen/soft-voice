<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;
use App\Traits\RespondWithHttpStatus;
use App\Http\Repositories\PermissionRepository;

class PermissionController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected PermissionRepository $permissionRepository
    ){
    }

    public function index()
    {
        $data = $this->permissionRepository->getAll('*');
        return $this->success(data: PermissionResource::collection($data));
    }

    public function show(Permission $permission)
    {
        return $this->success(data: new PermissionResource($permission));
    }
}
