<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ncm extends Model
{
    use HasFactory;

    protected $table = 'ncms';
    protected $fillable = [
        'name_ncm',
        'cod_ncm',
        'status_ncm',
        'enterprise_id',
    ];
}
