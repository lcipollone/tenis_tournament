<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Tournament\Tournament;
use Illuminate\Support\Facades\App;

class TournamentService
{
    public static function create(array $attributes): Tournament
    {
        $className = tournamentClassNameByType($attributes['tournament_type']);
        $tournament = App::make($className);
        $tournament->save();
        
        // TODO: hacer construct en Male y hacer $this->tournament()->create()
        $tournament->event()->create([
            'name' => $attributes['tournament_name'],
            'start_date' => $attributes['tournament_date'],
            'season' => $attributes['season'],
        ]);

        $players = [];
        foreach ($attributes['players'] as $player) {
            $players[] = new Player([...$player]);
        }

        //addPlayers
        $tournament->event->players()->saveMany($players);
        return $tournament;
    }

    public static function compete(Tournament $tournament): Player
    {
        $winner = $tournament->event->compete();
        $tournament->event->winner()->associate($winner);
        $tournament->event->save();

        return $winner;
    }
}