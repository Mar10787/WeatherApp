<?php

use Tests\TestCase;
use App\Services\WeatherService;

class WeatherServiceTest extends TestCase{
    public function test_get_five_day_forecast_for_valid_city(){
        $service = new WeatherService();
        $forecast = $service->getFiveDayForecast('Brisbane');

        $this->assertIsArray($forecast);
        $this->assertArrayHasKey('DailyForecasts', $forecast);
        $this->assertCount(5, $forecast['DailyForecasts']);
    }
}