<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = [
        'nome_client',
        'fone_client',
        'type_partner',
        'type_client',
        'cpf_cnpj_client',
        'date_birth_client',
        'rg_ie_client',
        'address_client',
        'number_client',
        'city_client',
        'state_client',
        'status_client',
        'email_client',
        'observation_client',
        'enterprise_id',
        'date_register',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
