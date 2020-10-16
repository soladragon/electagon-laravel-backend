<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'constituency_id',
        'full_name',
        'votes',
        'party', 
        'position',
        'vote_change_percentage',
     ];

     public function constituency()
     {
         return $this->belongsTo('App\Models\constituency');
     }
}
