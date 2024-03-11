<?php

namespace Tests\Unit;

use App\Models\Tournament\Tournament;
use App\Services\TournamentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as TestsTestCase;

class TournamentServiceTest extends TestsTestCase
{
    use RefreshDatabase;

    /**
     * Test para el método create de TournamentService.
     *
     * @return void
     */
    public function testCreateTournament()
    {
        // Crear datos de prueba para el torneo y los jugadores
        $tournamentData = [
            'tournament_type' => 'male',
            'tournament_date' => '2024-12-26',
            'tournament_name' => 'Wimbledon',
            'season' => 2025,
            'players' => [
                ['name' => 'Player 1', 'skill' => 80, 'speed' => 70, 'strength' => 74, 'reaction' => 70],
                ['name' => 'Player 2', 'skill' => 75, 'speed' => 72, 'strength' => 88, 'reaction' => 72],
                ['name' => 'Player 3', 'skill' => 71, 'speed' => 74, 'strength' => 81, 'reaction' => 80],
                ['name' => 'Player 4', 'skill' => 82, 'speed' => 68, 'strength' => 79, 'reaction' => 72],
                ['name' => 'Player 5', 'skill' => 79, 'speed' => 65, 'strength' => 82, 'reaction' => 75],
                ['name' => 'Player 6', 'skill' => 74, 'speed' => 75, 'strength' => 77, 'reaction' => 79],
                ['name' => 'Player 7', 'skill' => 73, 'speed' => 70, 'strength' => 84, 'reaction' => 73],
                ['name' => 'Player 8', 'skill' => 78, 'speed' => 72, 'strength' => 80, 'reaction' => 74],
                // Agrega más jugadores según sea necesario
            ]
        ];

        $tournament = TournamentService::create($tournamentData);

        // Verificar que el torneo y los jugadores fueron creados en la base de datos
        $this->assertDatabaseHas('tournaments', ['name' => 'Wimbledon']);
        foreach ($tournamentData['players'] as $player) {
            $this->assertDatabaseHas('players', ['name' => $player['name']]);
        }

        // Verificar que se crearon los contests de la ronda 1
        $this->assertCount(8, Tournament::where('name', 'Wimbledon')->first()->players()->get());

        // Realizar más verificaciones según sea necesario para validar el proceso de creación del torneo
    }
}
