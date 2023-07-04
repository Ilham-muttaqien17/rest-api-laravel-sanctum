<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Cache::forget('products_list');
        $cachePath = storage_path('framework/cache/data');
        $cacheFiles = File::files($cachePath);
        $cacheKeys = [];

        foreach ($cacheFiles as $file) {
            $cacheKeys[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        foreach ($cacheKeys as $key) {
            if (preg_match('/product_list:/i', $key)) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Cache::forget('products_list');
        $cachePath = storage_path('framework/cache/data');
        $cacheFiles = File::files($cachePath);
        $cacheKeys = [];

        foreach ($cacheFiles as $file) {
            $cacheKeys[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        foreach ($cacheKeys as $key) {
            if (preg_match('/product_list:/i', $key)) {
                Cache::forget($key);
            }
        }
    }
}
