<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = ['player_id','matches_played','win','lose','kill','death','assist','kda'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
