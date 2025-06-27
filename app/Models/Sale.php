<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'sale_date',
        'total_amount'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
