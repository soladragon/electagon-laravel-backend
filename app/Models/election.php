<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class election extends Model
{
    use HasFactory;

    public function constituencies()
    {
        return $this->hasMany('App\Models\constituency')->with('candidates');
    }
}
