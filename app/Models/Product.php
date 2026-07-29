<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'image_url',
        'gallery',
        'status',
        'is_active',
        'category_id',
        'has_variations',
        'is_package',
        'package_data'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'gallery' => 'array',
        'is_active' => 'boolean',
        'has_variations' => 'boolean',
        'is_package' => 'boolean',
        'package_data' => 'json',
    ];

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}