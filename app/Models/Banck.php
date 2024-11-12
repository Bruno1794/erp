<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banck extends Model
{
    use HasFactory;
    protected $table = 'banks';

    protected $fillable = [
        'name_bank',
        'status_bank',
        'enterprise_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
