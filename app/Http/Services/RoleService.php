<?php

namespace App\Http\Services;

use Spatie\Permission\Models\Role;
use App\Http\Repositories\RoleRepository;

class RoleService
{
    public function __construct(
        protected RoleRepository $roleRepository
    )
    {
    }

    public function store(array $data)
    {
        $data['guard_name'] = 'admin';
        $role = $this->roleRepository->create($data);
        $role->syncPermissions($data['permissions']);
        return $role;
    }

    public function update(Role $role, array $data)
    {
        if ($role->id === 1) abort(403, "Cannot update super admin role");
        $this->roleRepository->update($role, $data);
        $role->syncPermissions($data['permissions']);
        return $role->refresh();
    }

    public function delete(Role $role)
    {
        if ($role->id === 1) abort(403, "Cannot delete super admin role");
        if ($role->users()->count() > 0) abort(403, "Cannot delete role, there are admins assigned to it");

        $role->syncPermissions([]);
        $this->roleRepository->destroy($role);
    }
}
