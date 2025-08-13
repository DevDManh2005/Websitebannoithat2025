<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePermission extends Model
{
    protected $fillable = [
        'area', 'module_name', 'action', 'route_name', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
