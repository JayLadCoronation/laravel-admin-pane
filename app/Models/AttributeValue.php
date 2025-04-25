<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    public $timestamps = false;

    protected $fillable = ['attribute_id', 'value', 'created_at', 'updated_at', 'deleted_at'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
    public function products()
    {
        return $this->belongsToMany(ProductVariant::class);
    }
    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}

