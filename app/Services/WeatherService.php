<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Exception;

class WeatherService{
    private $apiKey;
    private $baseUrl;

    public function __construct(){
        $this->apiKey = config('services.weather.key');
        $this->baseUrl = config('services.weather.url');
    }

    public function getFiveDayForecast($city){
        // Cache for 1 hour to avoid API rate limits
        $cacheKey = "weather_forecast_{$city}";

        return Cache::remember($cacheKey, 60, function () use ($city) {
            try {
                $locationResponse = Http::get("https://dataservice.accuweather.com/locations/v1/cities/search", [
                    'apikey' => $this->apiKey,
                    'q' => $city,
                ]);

                if ($locationResponse->failed()  || empty($locationResponse->json())){
                    throw new Exception("Failed to get location key");
                }

                $locationKey = $locationResponse->json()[0]['Key'];

                $forecastResponse = Http::get("{$this->baseUrl}/forecasts/v1/daily/5day/{$locationKey}", [
                    'apikey' => $this->apiKey,
                    'language' => 'en-us',
                    'metric' => 'true'
                ]);

                if ($forecastResponse->failed() || empty($forecastResponse->json())){
                    throw new Exception("Forecast API Failed");
                }

                return $forecastResponse->json();
            } catch (Exception $e){
                throw new Exception("Weather Forecast Error: {$e->getMessage()}");
            }
        });
    }
}