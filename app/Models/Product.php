<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category_id',
        'base_buy_price',
        'base_sale_price',
        'kg_per_carton',
        'stock_kg',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}