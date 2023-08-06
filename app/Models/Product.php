<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productVariations(): HasMany
    {
        return $this->hasMany(ProductVariations::class, 'product_id');
    }

    public function productPictures(): BelongsToMany
    {
        return $this
            ->belongsToMany(Media::class, 'media_product', 'product_id', 'media_id')
            ->withPivot('order')
            ->orderBy('order');
    }
}
