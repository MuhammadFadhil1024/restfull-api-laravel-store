<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'categories_id', 'title', 'price', 'stock'
    ];

    public function category()
    {
        return $this->hasOne(Category::class, 'categories_id', 'id');
    }
}
