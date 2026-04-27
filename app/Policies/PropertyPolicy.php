<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Property $property): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->isAdmin();
    }
}
