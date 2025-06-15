<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    private $weatherService;

    public function __construct(WeatherService $weatherService){
        $this->weatherService =$weatherService;
    }

    public function getCities(): JsonResponse{
        return response()->json([
            'cities' => $this->weatherService->getValidCities()
        ]);
    }

    public function getForecast(Request $request): JsonResponse{
        $request ->validate([
            'city' => 'required|string'
        ]);

        $city = $request->input('city');

        if (!$this->weatherService->isValidCity($city)){
            return response()->json([
                'error' => 'Invalid city. Please select from:  '. implode(',',$this->weatherService->getValidCities())
            ], 400);
        }

        try{
            $forecast = $this->weatherService->getFiveDayForecast($city);

            return response()->json([
                'success'=> true,
                'date' => $forecast
            ]);
        } catch (\Exception $e){
            return response()->json([
                'error'=>'Failed to fetch weather data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
