<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form_Payment extends Model
{
    use HasFactory;

    protected $table = 'forms_payments';
    protected $fillable = [
        'name_payments',
        'type_payments',
        'status_payments',
        'internal_payment',
        'enterprise_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
