<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderItem extends Model
{
    protected $fillable = ['customer_order_id', 'product_id', 'unit_id', 'quantity'];

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}