<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stoks';
    protected $fillable = [
        'product_id',
        'type_moviment',
        'qtd_stock',
        'price_cost',
        'total_value',
        'debit',
        'debit',
        'motive',
        'provider_id',
        'enterprise_id',
        'user_id',
        'created_at',
    ];
    protected $hidden = [

        'updated_at',
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
