<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Landlord extends Authenticatable
{
    use HasFactory, HasRoles;

    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];
}
