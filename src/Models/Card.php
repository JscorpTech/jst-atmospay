<?php


namespace JscorpTech\Atmospay\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    public $table = "atmospay_cards";
    public $fillable = [
        "number",
        "expiry",
        "token"
    ];
}
