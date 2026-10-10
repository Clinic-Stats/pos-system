<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $fillable = [
        'order_no', 'customer_id', 'claims_regular', 'name', 'phone', 'address', 'note',
        'status', 'sale_id', 'handled_by', 'handled_at',
    ];

    protected $casts = [
        'claims_regular' => 'boolean',
        'handled_at'     => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}