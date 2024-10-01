<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    Protected $table = 'categories';
    protected $fillable = [
        'name_category',
        'status_category',
        'enterprise_id'
    ];
}
