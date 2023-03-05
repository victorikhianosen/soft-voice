<?php

namespace App\Http\Repositories;

use Spatie\Permission\Models\Permission;

class PermissionRepository extends AbstractRepository
{
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
    }

    public function model()
    {
        return app(Permission::class);
    }
}