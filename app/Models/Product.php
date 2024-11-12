<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name_product',
        'manage_stock',
        'barcode',
        'ncm_id',
        'category_id',
        'unit_id',
        'qtd_stock',
        'stock_min',
        'price_sale',
        'status_product',
        'enterprise_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function ncm(){
        return $this->belongsTo(Ncm::class);
    }
    public function unit(){
        return $this->belongsTo(Unit_Size::class);

    }



}
