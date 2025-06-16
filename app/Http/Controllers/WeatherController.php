<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getCities()
    {
        return response()->json($this->weatherService->getValidCities());
    }

    public function getWeather($city)
    {
        try {
            $weatherData = $this->weatherService->getCurrentWeather($city);
            return response()->json($weatherData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
} 