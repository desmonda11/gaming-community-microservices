<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = ['team_id', 'opponent', 'match_date', 'result', 'score_team', 'score_opponent'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
