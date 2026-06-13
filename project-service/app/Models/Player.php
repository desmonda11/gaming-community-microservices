<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'team_id', 'nickname', 'role_in_game', 'rank'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class, 'player_id');
    }
}
