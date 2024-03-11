<?php

namespace App\Models;

use App\Models\Tournament\Tournament;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [ 'player1_id', 'player2_id', 'winner_player_id', 'tournament_id', 'round' ];

    public function player1(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player1_id', 'id');
    }

    public function player2(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'winner_player_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    private function getLuck(int $score, int $scoreToBeat): bool
    {
        $difference = $scoreToBeat - $score + 1;

        $extraPoints = mt_rand(0, $difference);

        return $score + $extraPoints > $scoreToBeat;
    }

    public function play(): Player
    {
        $scorePlayer1 = $this->player1->score() + $this->tournament->tournamentable->additionalScore($this->player1);
        $scorePlayer2 = $this->player2->score() + $this->tournament->tournamentable->additionalScore($this->player2);

        if ($scorePlayer1 > $scorePlayer2) {
            $winner = $this->getLuck($scorePlayer2, $scorePlayer1) ? $this->player2 : $this->player1;
        } elseif ($scorePlayer1 < $scorePlayer2) {
            $winner = $this->getLuck($scorePlayer1, $scorePlayer2) ? $this->player1 : $this->player2;
        } else {
            // Si los puntajes son iguales, determinar al ganador aleatoriamente
            $winner = rand(0, 1) == 0 ? $this->player1 : $this->player2;
        }

        $this->winner()->associate($winner);
        $this->save();

        return $this->winner;
    }
}
