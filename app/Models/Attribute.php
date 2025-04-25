<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'created_at', 'updated_at', 'deleted_at'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
