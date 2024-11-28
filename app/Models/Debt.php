<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $table = 'debt_payable';
    protected $fillable = [
        'name_debit',
        'number_note',
        'number_check',
        'banck_transmitter_cheque',
        'value_total_debit',
        'parcel',
        'date_venciment',
        'date_payment',
        'value_paid',
        'description',
        'type_debit',
        'status_debit',
        'stok_id',
        'enterprise_id',
        'user_id',
        'provider_id',
        'forms_payments_id',
        'banck_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forms_payments()
    {
        return $this->belongsTo(Form_Payment::class);
    }

    public function provider()
    {
        return $this->belongsTo(Client::class);
    }

    public function banck()
    {
        return $this->belongsTo(Banck::class);
    }
}
