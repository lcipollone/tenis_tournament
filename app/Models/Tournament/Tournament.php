<?php

namespace App\Models\Tournament;

use App\Models\Contest;
use App\Models\Player;
use App\Models\Tournament\Traits\Tournamentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model implements TournamentInterface
{
    use HasFactory;
    use Tournamentable;

    protected $fillable = ['name', 'start_date', 'season', 'winner_player_id', 'tournamentable_type', 'tournamentable_id'];

    public function additionalScore(Player $player): int
    {
        return $this->tournamentable->additionalScore($player);
    }

    public function tournamentable(){
        return $this->morphTo();
    }

    public function contests(): HasMany
    {
        return $this->hasMany(Contest::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'tournament_players');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'winner_player_id', 'id');
    }
}
