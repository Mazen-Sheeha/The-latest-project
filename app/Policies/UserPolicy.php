<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    protected $per = "صلاحية ادارة المدراء";

    public function viewAny(User $authUser): bool
    {
        return $authUser->id === 1 || $authUser->hasPermission($this->per);
    }

    public function create(User $authUser): bool
    {
        return $authUser->id == 1 || $authUser->hasPermission($this->per);
    }

    public function update(User $authUser, User $target): bool
    {
        return $target->id == 1
            ? $authUser->id == 1
            : ($authUser->hasPermission($this->per) || $authUser->id == 1);
    }

    public function delete(User $authUser, User $target): bool
    {
        return $authUser->id === 1 && $target->id != 1;
    }
}
