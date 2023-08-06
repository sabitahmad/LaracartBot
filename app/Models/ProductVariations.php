<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariations extends Model
{

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation_image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'product_variation_image_id','id');
    }

}
