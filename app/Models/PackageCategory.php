<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_title',
        'package_description',
        'slug',
        'event_date_from',
        'event_date_to',
        'is_active'
    ];

    protected $casts = [
        'event_date_from' => 'date',
        'event_date_to' => 'date',
        'is_active' => 'boolean'
    ];
}