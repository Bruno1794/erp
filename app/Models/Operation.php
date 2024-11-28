<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $table = 'operations';
    protected $fillable = [
        'operation_name',
        'description',
        'type_operation',
        'create_movement',
        'enterprise_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
