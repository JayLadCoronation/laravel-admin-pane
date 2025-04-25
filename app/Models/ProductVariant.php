<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'attribute_value_ids', 'price', 'stock', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'attribute_value_ids' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_value');
    }

    // public function attributeValues()
    // {
    //     return $this->belongsToMany(AttributeValue::class);
    // }
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function images()
    {
        return $this->hasMany(VariantImage::class);
    }

}

