<?php

namespace App\Models\Tournament;

use App\Models\Player;

interface TournamentInterface {
    public function additionalScore(Player $player): int;
}