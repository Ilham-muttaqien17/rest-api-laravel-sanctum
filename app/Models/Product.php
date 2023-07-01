<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            Cache::forget('products_list');
        });

        static::updated(function () {
            Cache::forget('products_list');
        });
    }
}
