<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory;

    protected $table = 'enterprises';
    protected $fillable = [
        'name_enterprise',
        'cpf_cnpj_enterprise',
        'rg_ie_enterprise',
        'address_enterprise',
        'number_enterprise',
        'cep_enterprise',
        'city_enterprise',
        'state_enterprise',
        'validade',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
