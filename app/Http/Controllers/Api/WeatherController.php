<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    private $weatherService;

    public function __construct(WeatherService $weatherService){
        $this->weatherService = $weatherService;
        Log::info('WeatherController constructed');
    }

    public function getCities(): JsonResponse{
        Log::info('getCities method called');
        return response()->json([
            'cities' => $this->weatherService->getValidCities()
        ]);
    }

    public function getForecast(Request $request): JsonResponse{
        Log::info('getForecast method called', ['request' => $request->all()]);
        
        $request->validate([
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
                'data' => $forecast
            ]);
        } catch (\Exception $e){
            Log::error('Forecast error', ['error' => $e->getMessage()]);
            return response()->json([
                'error'=>'Failed to fetch weather data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
