<?php


namespace JscorpTech\Atmospay\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $table = "atmospay_transactions";
    public $fillable = [
        "amount",
        "transaction_id",
        "status",
        "account"
    ];
}
