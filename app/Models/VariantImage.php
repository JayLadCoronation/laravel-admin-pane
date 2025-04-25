<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['product_variant_id', 'image_path'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
