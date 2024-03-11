<?php

namespace App\Models\Tournament;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FemaleTournament extends Tournament
{
    use HasFactory;

    public function additionalScore(Player $player): int
    {
        return $player->reaction;
    }

    public function event(){
        return $this->morphOne(Tournament::class, 'tournamentable');
    }

    
    public function type(){
        return "Female";
    }
}
