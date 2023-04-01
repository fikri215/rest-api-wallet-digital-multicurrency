<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyExchangeRate extends Model
{
    protected $guarded = [];

    protected $hidden = ['id', 'created_at', 'updated_at'];
    
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
