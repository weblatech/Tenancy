<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'is_policy',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_policy' => 'boolean',
    ];
}
