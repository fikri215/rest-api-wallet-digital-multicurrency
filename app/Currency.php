<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $guarded = [];

    public function wallet()
    {
        return $this->hasMany(Wallet::class);
    }

}
