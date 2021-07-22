<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'balance',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
