<?php

namespace App\Http\Repositories;

use App\Models\Admin;

class AdminRepository extends AbstractRepository
{
    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
    }

    public function model()
    {
        return app(Admin::class);
    }
}
