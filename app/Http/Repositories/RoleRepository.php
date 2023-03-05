<?php

namespace App\Http\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository extends AbstractRepository
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function model()
    {
        return app(Role::class);
    }
}
