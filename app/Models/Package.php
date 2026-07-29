<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_title',
        'package_description',
        'package_slug',
        'event_date_from',
        'event_date_to',
        'is_popular'
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'event_date_from' => 'date',
        'event_date_to' => 'date'
    ];
}