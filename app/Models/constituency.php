<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class constituency extends Model
{
    use HasFactory;

    public function election()
    {
        return $this->belongsTo('App\Models\election');
    }

    public function candidates()
    {
        return $this->hasMany('App\Models\candidate');
    }
    
}
