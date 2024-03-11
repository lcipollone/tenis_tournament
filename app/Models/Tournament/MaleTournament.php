<?php

namespace App\Models\Tournament;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaleTournament extends Tournament
{
    use HasFactory;

    /*protected $table = 'tournaments';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }*/

    public function additionalScore(Player $player): int
    {
        return $player->strength + $player->speed;
    }

    public function event(){
        return $this->morphOne(Tournament::class, 'tournamentable');
    }

    public function type(){
        return "Male";
    }
}
