<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'area',
        'commission_rate',
        'is_active',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}