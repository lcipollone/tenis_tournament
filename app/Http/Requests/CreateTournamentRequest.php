<?php

namespace App\Http\Requests;

use App\Rules\PowerOfTwo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateTournamentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator(Validator $validator)
    {
        $players = $validator->getData()['players'] ?? [];
        $validator->after(
            function ($validator) use ($players) {
                if ($players && (count($players) & (count($players) - 1)) !== 0) {
                    $validator->errors()->add(
                        'players',
                        'The number of players must be power of 2.'
                    );
                }
            }
        );                            
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tournament_type' => [
                'required', 
                Rule::in('male', 'female'),
            ],
            'tournament_date' => [
                'required',
                'date',
            ],
            'tournament_name' => [
                'required',
                'string',
                'max:255',
            ],
            'season' => [
                'required',
                'digits:4',
                'integer',
                'min:'.(date('Y')),
            ],
            'players.*.name' => [
                'required',
                'string',
                'max:255',
            ],
            'players.*.skill' => [
                'required',
                'numeric',
                'min:1',
                'max:100',
            ],
            'players.*.strength' => [
                'numeric',
                'min:1',
                'max:100',
            ],
            'players.*.speed' => [
                'numeric',
                'min:1',
                'max:100',
            ],
            'players.*.reaction' => [
                'numeric',
                'min:1',
                'max:100',
            ],
        ];
    }
}
