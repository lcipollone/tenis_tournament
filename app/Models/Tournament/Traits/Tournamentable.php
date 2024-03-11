<?php

namespace App\Models\Tournament\Traits;

use App\Models\Contest;
use App\Models\Player;
use Illuminate\Support\Collection;


trait Tournamentable
{
    protected function generateMatches($players, int $round): Collection
    {
        $matches = new Collection();

        if ($round == 1){
            $players->shuffle();
        }

        for ($i = 0; $i < count($players); $i += 2) {
            $player1 = $players[$i];
            $player2 = $players[$i + 1];
            $contest = new Contest([
                'round' => $round,
                'player1_id' => $player1->id,
                'player2_id' => $player2->id,
                'tournament_id' => $this->id,
            ]);
            $contest->save();
            $this->contests->push($contest);
            $matches->push($contest);
        }

        return $matches;
    }

    public function compete(): Player
    {
        $round = 1;
        $players = $this->players;
        while ($players->count() > 1) {
            $matches = $this->generateMatches($players, $round);
            $winners = new Collection();

            foreach ($matches as $match) {
                $winner = $match->play();
                $winners->push($winner);
            }

            // Los ganadores pasan a la siguiente ronda
            $players = $winners;
            $round++;
        }

        // Devolver el jugador ganador del torneo
        return $players->first();
    }
}