<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit_Size extends Model
{
    use HasFactory;

    protected $table = 'unit_sizes';
    protected $fillable = [
        'name_unit',
        'status_unit',
        'enterprise_id',
    ];
}
