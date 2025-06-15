<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

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

                $locationData = $locationResponse->json()[0];
                $locationKey = $locationData['Key'];
                $cityName = $locationData['LocalizedName'];

                $forecastResponse = Http::get("{$this->baseUrl}/forecasts/v1/daily/5day/{$locationKey}", [
                    'apikey' => $this->apiKey,
                    'language' => 'en-us',
                    'metric' => 'true'
                ]);

                if ($forecastResponse->failed() || empty($forecastResponse->json())){
                    throw new Exception("Forecast API Failed");
                }

                return $this->formatForecastData($forecastResponse->json(), $cityName);
            } catch (Exception $e){
                throw new Exception("Weather Forecast Error: {$e->getMessage()}");
            }
        });
    }

    private function formatForecastData($data, $city){
        $forecast = [];

        foreach ($data['DailyForecasts'] as $day){
            $min = round($day['Temperature']['Minimum']['Value'],2);
            $max = round($day['Temperature']['Maximum']['Value'],2);
            $avg = round(($max+$min)/2,2);

            $forecast[] = [
                'city' => $city,
                'date' => $day['Date'],
                'min_temp' => $min,
                'max_temp' => $max,
                'avg_temp' => $avg
            ];
        }
    return $forecast;
    }

    public function getValidCities(){
        return [
            'Brisbane',
            'Gold Coast',
            'Sunshine Coast Airport'
        ];
    }

    public function isValidCity($city)
    {
        return in_array($city, $this->getValidCities());
    }
}