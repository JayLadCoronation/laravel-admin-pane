<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'created_at', 'updated_at', 'deleted_at','category_id'];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
    
    public function setUpdatedAt($value)
    {
        $this->attributes['updated_at'] = time();
    }
}

