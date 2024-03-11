<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'skill', 'strength', 'speed', 'reaction' ];

    public function score(): int
    {
        return $this->skill;
    }
}
