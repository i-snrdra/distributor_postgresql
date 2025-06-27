<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'stok',
        'satuan',
        'supplier_id',
        'harga_jual',
        'harga_beli'
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'harga_beli' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
