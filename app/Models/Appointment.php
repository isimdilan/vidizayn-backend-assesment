<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    
    protected $guarded = [];
    //
    protected $fillable = [
        'user_id',
        'provider_id',
        'date',
        'time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
