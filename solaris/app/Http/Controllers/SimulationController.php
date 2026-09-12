<?php

namespace App\Http\Controllers;

use App\Services\SolarSimulation;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function __invoke(Request $request, SolarSimulation $simulation)
    {
        $defaults = ['panels' => 20, 'watts' => 450, 'sun_hours' => 5, 'loss_percent' => 20,
            'household_kwh' => 150, 'co2_factor' => 0.4, 'self_consumption' => 80,
            'tariff' => 1, 'investment' => 50000];
        $validated = $request->validate([
            'panels' => 'sometimes|required|integer|min:1|max:1000000',
            'watts' => 'sometimes|required|numeric|min:1|max:2000',
            'sun_hours' => 'sometimes|required|numeric|min:0|max:12',
            'loss_percent' => 'sometimes|required|numeric|min:0|max:100',
            'household_kwh' => 'sometimes|required|numeric|min:1|max:100000',
            'co2_factor' => 'sometimes|required|numeric|min:0|max:5',
            'self_consumption' => 'sometimes|required|numeric|min:0|max:100',
            'tariff' => 'sometimes|required|numeric|min:0|max:100',
            'investment' => 'sometimes|required|numeric|min:0|max:10000000000',
        ]);
        $input = array_replace($defaults, $validated);

        return view('simulation', ['input' => $input, 'result' => $simulation->calculate($input)]);
    }
}
