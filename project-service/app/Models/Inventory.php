<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['team_id','item_name','category','quantity','condition','notes'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
