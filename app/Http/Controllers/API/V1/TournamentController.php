<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament\Tournament;
use App\Services\TournamentService;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/tournaments",
     *   summary="Lista y filtra Torneos",
     *   tags={"Tournaments"},
     *   @OA\Parameter(
     *      name="tournament_name",
     *      in="query",
     *      description="Filtrar por nombre de Torneo",
     *      required=false,
     *   ),
     *   @OA\Parameter(
     *      name="tournament_type",
     *      in="query",
     *      description="Filtrar por Tipo de torneo (masculino o femenino)",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          enum={"male", "female"}
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="season",
     *      in="query",
     *      description="Filtrar por temporada",
     *      required=false,
     *   ),
     *   @OA\Parameter(
     *      name="winner",
     *      in="query",
     *      description="Filtrar por ganador",
     *      required=false,
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="A list with tournaments"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index(Request $request)
    {
        $query = Tournament::query();

        if ($request->has('tournament_date')) {
            $query->where('start_date', $request->input('tournament_date'));
        }

        if ($request->has('tournament_type')) {
            $type = tournamentClassNameByType($request->tournament_type);
            $query->where('tournamentable_type', $type);
        }

        if ($request->has('season')) {
            $query->where('season', $request->input('season'));
        }

        if ($request->has('winner')) {
            $query->whereHas('winner', function($q) use($request){
                $q->where('name', 'like', "%{$request->input('winner')}%");
            });
        }

        $tournaments = $query->get();

        return TournamentResource::collection($tournaments);
    }

    /**
     * @OA\Post(
     *     path="/api/tournaments",
     *     summary="Crea un nuevo torneo",
     *     description="Crea un nuevo torneo y devuelve al ganador.",
     *     tags={"Tournaments"},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Datos del nuevo torneo",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Examples(
     *                  summary="Crear Torneo",
     *                  example="Crear Torneo",
     *                  value={
     *                      "tournament_type": "male",
     *                      "tournament_date": "2024-12-26",
     *                      "tournament_name": "Wimbledon",
     *                      "season": 2025,
     *                      "players": {
     *                          {
     *                              "name": "Player 1",
     *                              "skill": 46,
     *                              "speed": 54,
     *                              "strength": 79,
     *                              "reaction": 60
     *                          },
     *                          {
     *                              "name": "Player 2",
     *                              "skill": 45,
     *                              "speed": 70,
     *                              "strength": 69,
     *                              "reaction": 50
     *                          },
     *                          {
     *                              "name": "Player 3",
     *                              "skill": 48,
     *                              "speed": 65,
     *                              "strength": 75,
     *                              "reaction": 50
     *                          },
     *                          {
     *                              "name": "Player 4",
     *                              "skill": 49,
     *                              "speed": 67,
     *                              "strength": 72,
     *                              "reaction": 50
     *                          }
     *                      }
     *                  },
     *              ),
     *              @OA\Schema(
     *                  type="object",
     *                  required={"tournament_type", "tournament_date", "tournament_name", "season", "players"},
     *                  @OA\Property(
     *                      property="tournament_type",
     *                      type="string",
     *                      enum={"male", "female"},
     *                      description="Tipo de torneo (masculino o femenino)"
     *                  ),
     *                  @OA\Property(
     *                      property="tournament_date",
     *                      type="string",
     *                      format="date",
     *                      description="Fecha del torneo (YYYY-MM-DD)"
     *                  ),
     *                  @OA\Property(
     *                      property="tournament_name",
     *                      type="string",
     *                      description="Nombre del torneo"
     *                  ),
     *                  @OA\Property(
     *                      property="season",
     *                      type="integer",
     *                      description="Año de la temporada del torneo"
     *                  ),
     *                  @OA\Property(
     *                      property="players",
     *                      type="array",
     *                      description="Lista de jugadores participantes",
     *                      @OA\Items(
     *                          type="object",
     *                          required={"name", "skill", "speed", "strength", "reaction"},
     *                          @OA\Property(property="name", type="string", description="Nombre del jugador"),
     *                          @OA\Property(property="skill", type="integer", description="Nivel de habilidad del jugador"),
     *                          @OA\Property(property="speed", type="integer", description="Velocidad de desplazamiento del jugador"),
     *                          @OA\Property(property="strength", type="integer", description="Fuerza del jugador"),
     *                          @OA\Property(property="reaction", type="integer", description="Tiempo de reacción del jugador")
     *                      )
     *                  )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Torneo creado exitosamente",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Error de validación de datos"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function store(CreateTournamentRequest $request)
    {
        $tournament = TournamentService::create($request->all());
        
        //compete
        TournamentService::compete($tournament);

        return response()->json([
            'tournament' => new TournamentResource($tournament->event),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
