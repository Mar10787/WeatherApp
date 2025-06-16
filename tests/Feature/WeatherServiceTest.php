<?php

use Tests\TestCase;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Cache;

class WeatherServiceTest extends TestCase
{
    private $mockForecastData = [
        [
            'city' => 'Brisbane',
            'date' => '2024-03-20T00:00:00+10:00',
            'min_temp' => 20.5,
            'max_temp' => 28.5,
            'avg_temp' => 24.5
        ],
        [
            'city' => 'Brisbane',
            'date' => '2024-03-21T00:00:00+10:00',
            'min_temp' => 21.0,
            'max_temp' => 29.0,
            'avg_temp' => 25.0
        ],
        [
            'city' => 'Brisbane',
            'date' => '2024-03-22T00:00:00+10:00',
            'min_temp' => 21.5,
            'max_temp' => 29.5,
            'avg_temp' => 25.5
        ],
        [
            'city' => 'Brisbane',
            'date' => '2024-03-23T00:00:00+10:00',
            'min_temp' => 22.0,
            'max_temp' => 30.0,
            'avg_temp' => 26.0
        ],
        [
            'city' => 'Brisbane',
            'date' => '2024-03-24T00:00:00+10:00',
            'min_temp' => 22.5,
            'max_temp' => 30.5,
            'avg_temp' => 26.5
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_returns_five_day_forecast_structure()
    {
        // Mock the Cache facade
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($this->mockForecastData);

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

    public function test_it_validates_city_names()
    {
        $service = new WeatherService();
        
        $this->assertTrue($service->isValidCity('Brisbane'));
        $this->assertTrue($service->isValidCity('Gold Coast'));
        $this->assertTrue($service->isValidCity('Sunshine Coast'));
        $this->assertFalse($service->isValidCity('Invalid City'));
    }

    public function test_it_returns_valid_cities_list()
    {
        $service = new WeatherService();
        $cities = $service->getValidCities();

        $this->assertIsArray($cities);
        $this->assertCount(3, $cities);
        $this->assertContains('Brisbane', $cities);
        $this->assertContains('Gold Coast', $cities);
        $this->assertContains('Sunshine Coast', $cities);
    }

    public function test_it_returns_current_weather()
    {
        // Mock the Cache facade
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($this->mockForecastData);

        $service = new WeatherService();
        $currentWeather = $service->getCurrentWeather('Brisbane');

        $this->assertIsArray($currentWeather);
        $this->assertArrayHasKey('city', $currentWeather);
        $this->assertArrayHasKey('temperature', $currentWeather);
        $this->assertArrayHasKey('min', $currentWeather['temperature']);
        $this->assertArrayHasKey('max', $currentWeather['temperature']);
        $this->assertArrayHasKey('avg', $currentWeather['temperature']);
        
        $this->assertEquals('Brisbane', $currentWeather['city']);
        $this->assertEquals(20.5, $currentWeather['temperature']['min']);
        $this->assertEquals(28.5, $currentWeather['temperature']['max']);
        $this->assertEquals(24.5, $currentWeather['temperature']['avg']);
    }
}