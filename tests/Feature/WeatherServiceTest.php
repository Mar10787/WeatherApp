<?php

use Tests\TestCase;
use App\Services\WeatherService;

class WeatherServiceTest extends TestCase
{
    public function test_it_returns_five_day_forecast_structure()
    {
        $service = new WeatherService();
        $forecast = $service->getFiveDayForecast('Brisbane');

        $this->assertIsArray($forecast);
        $this->assertCount(5, $forecast);

        foreach ($forecast as $day) {
            $this->assertArrayHasKey('city', $day);
            $this->assertArrayHasKey('date', $day);
            $this->assertArrayHasKey('min_temp', $day);
            $this->assertArrayHasKey('max_temp', $day);
            $this->assertArrayHasKey('avg_temp', $day);
        }
    }
}