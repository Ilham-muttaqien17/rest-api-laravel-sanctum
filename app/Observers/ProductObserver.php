<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $paginator = Product::paginate(env('PAGINATION_PER_PAGE', 5));
        $lastPage = $paginator->lastPage();

        for ($i = 1; $i <= $lastPage; $i++) {
            Cache::forget('product_list:' . $i);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $paginator = Product::paginate(env('PAGINATION_PER_PAGE', 5));
        $lastPage = $paginator->lastPage();

        for ($i = 1; $i <= $lastPage; $i++) {
            Cache::forget('product_list:' . $i);
        }
    }
}
